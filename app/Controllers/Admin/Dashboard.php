<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\AdminUserModel;

class Dashboard extends BaseController
{
    protected $orderModel;
    protected $adminModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->adminModel = new AdminUserModel();
    }

    public function index()
    {
        // Render the new modular dashboard layout. Panels are loaded on demand.
        $data = [];
        $data['admin_name'] = session()->get('admin_name') ?? 'Administrator';

        return view('admin/dashboard', $data);
    }

    /**
     * Return a panel partial for the dashboard (loaded via AJAX)
     */
    public function panel($name = 'overview')
    {
        $name = preg_replace('/[^a-z0-9_\-]/i', '', $name);

        switch ($name) {
            case 'status':
                $data['recent_orders'] = $this->orderModel->orderBy('created_at', 'DESC')->limit(50)->findAll();
                $data['order_status_counts'] = [
                    'pending' => $this->orderModel->where('order_status', 'pending')->countAllResults(),
                    'in_progress' => $this->orderModel->where('order_status', 'in_progress')->countAllResults(),
                    'processing' => $this->orderModel->where('order_status', 'processing')->countAllResults(),
                    'delivering' => $this->orderModel->where('order_status', 'delivering')->countAllResults(),
                    'finished' => $this->orderModel->where('order_status', 'finished')->countAllResults(),
                    'cancelled' => $this->orderModel->where('order_status', 'cancelled')->countAllResults(),
                ];
                $data['payment_counts'] = [
                    'paid' => $this->orderModel->where('payment_status', 'paid')->countAllResults(),
                    'unpaid' => $this->orderModel->where('payment_status', 'unpaid')->countAllResults(),
                ];
                return view('admin/panels/status', $data);
            case 'reports':
                return view('admin/panels/reports');
            case 'analytics':
                return view('admin/panels/analytics');
            default:
                // overview summary
                $data['total_orders'] = $this->orderModel->countAllResults();
                $data['orders_unpaid'] = $this->orderModel->where('payment_status', 'unpaid')->countAllResults();
                $data['orders_paid'] = $this->orderModel->where('payment_status', 'paid')->countAllResults();
                $data['orders_processing'] = $this->orderModel->whereIn('order_status', ['in_progress','processing','approved'])->countAllResults();
                $data['recent_orders'] = $this->orderModel->orderBy('created_at', 'DESC')->limit(10)->findAll();
                return view('admin/panels/overview', $data);
        }
    }

    /**
     * Provide analytics JSON data (lazy-loaded by the analytics panel)
     */
    public function analyticsData()
    {
        // limit data to last 30 days to avoid heavy loads
        // For analytics, show recognized revenue (final_price when confirmed) to avoid counting pipeline orders
        $since30 = date('Y-m-d', strtotime('-30 days'));

        // Allow forcing dummy data for demo/testing via ?dummy=1
        $forceDummy = $this->request->getGet('dummy') ? true : false;

        // daily (last 30 days)
        $daily = [];
        if (! $forceDummy) {
            $daily = $this->orderModel
                ->select("DATE(created_at) as d, COUNT(*) as orders, SUM(CASE WHEN price_confirmed = 1 THEN final_price ELSE 0 END) as revenue_confirmed")
                ->where('created_at >=', $since30)
                ->groupBy('d')
                ->orderBy('d','ASC')
                ->get()
                ->getResultArray();
        }

        // weekly (last 8 weeks)
        $since8w = date('Y-m-d', strtotime('-8 weeks'));
        $weekly = [];
        if (! $forceDummy) {
            $weekly = $this->orderModel
                ->select("YEAR(created_at) as yr, WEEK(created_at,1) as wk, COUNT(*) as orders, SUM(CASE WHEN price_confirmed = 1 THEN final_price ELSE 0 END) as revenue_confirmed")
                ->where('created_at >=', $since8w)
                ->groupBy('yr,wk')
                ->orderBy('yr','ASC')
                ->orderBy('wk','ASC')
                ->get()
                ->getResultArray();
        }

        // today's summary
        $today = date('Y-m-d');
        $totalToday = (int) $this->orderModel->where('DATE(created_at)', $today)->countAllResults();
        $finishedToday = (int) $this->orderModel->where('DATE(created_at)', $today)->where('order_status','finished')->countAllResults();
        $revenueTodayRow = $this->orderModel->selectSum('final_price')->where('price_confirmed',1)->where('DATE(created_at)', $today)->first();
        $revenueToday = isset($revenueTodayRow['final_price']) ? (float)$revenueTodayRow['final_price'] : 0;

        // If forcing dummy, or no data available, return friendly dummy data for preview/testing
        if ($forceDummy || (empty($daily) && empty($weekly))) {
            // generate simple dummy daily for last 14 days
            $daily = [];
            for ($i = 13; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $daily[] = ['d'=>$d, 'orders'=>rand(2,12), 'revenue_confirmed'=>rand(200000,1500000)];
            }
            // generate simple dummy weekly for last 8 weeks
            $weekly = [];
            for ($w = 7; $w >= 0; $w--) {
                $wk = date('W', strtotime("-{$w} weeks"));
                $yr = date('Y', strtotime("-{$w} weeks"));
                $weekly[] = ['yr'=>$yr,'wk'=>$wk,'orders'=>rand(12,80),'revenue_confirmed'=>rand(1200000,8000000)];
            }
            $totalToday = rand(0,6);
            $finishedToday = rand(0,$totalToday);
            $revenueToday = rand(0,1000000);
        }

        return $this->response->setJSON([
            'success' => true,
            'daily' => $daily,
            'weekly' => $weekly,
            'summary' => [
                'total_today' => $totalToday,
                'finished_today' => $finishedToday,
                'revenue_today' => $revenueToday
            ]
        ]);
    }

    /**
     * Return detailed report rows for finished orders (JSON)
     */
    public function reportData()
    {
        $range = $this->request->getGet('range') ?? 'daily';

        // fetch finished orders within reasonable window (last 2 years)
        $builder = $this->orderModel->where('order_status', 'finished');
        // optional date filters could be added, for now return last 2 years
        $builder->where('created_at >=', date('Y-m-d', strtotime('-2 years')));
        $orders = $builder->orderBy('created_at','DESC')->findAll();

        $rows = [];
        foreach ($orders as $o) {
            $date = date('Y-m-d', strtotime($o['created_at']));
            $confirmedFinalPrice = (!empty($o['final_price']) && !empty($o['price_confirmed'])) ? (float)$o['final_price'] : null;
            $lines = preg_split('/\r?\n/', $o['design_description'] ?? '');
            if (!$lines) $lines = [];

            if ($confirmedFinalPrice !== null) {
                $rows[] = [
                    'date' => $date,
                    'order_code' => $o['order_code'],
                    'product' => trim($o['product_type'] ?? 'Custom Order'),
                    'qty' => 1,
                    'unit_price' => $confirmedFinalPrice,
                    'total' => $confirmedFinalPrice,
                    'price_status' => 'confirmed',
                    'final_price' => $confirmedFinalPrice
                ];
                continue;
            }

            foreach ($lines as $ln) {
                if (preg_match('/^(\d+)\s*x\s*(.+?)@\s*Rp\s*([0-9\.,]+)/i', $ln, $m)) {
                    $qty = (int)$m[1];
                    $name = trim($m[2]);
                    $unit = floatval(str_replace([',','.',' '], ['', '', ''], $m[3]));
                    $total = $qty * $unit;
                } elseif (preg_match('/^(\d+)\s*x\s*(.+)$/i', $ln, $m2)) {
                    $qty = (int)$m2[1];
                    $name = trim($m2[2]);
                    $unit = (float)$o['total_price'] / max(1,$qty);
                    $total = $qty * $unit;
                } else {
                    $qty = 1;
                    $name = trim($ln ?: ($o['product_type'] ?? 'Product'));
                    $unit = (float)$o['total_price'];
                    $total = $unit;
                }

                $rows[] = [
                    'date' => $date,
                    'order_code' => $o['order_code'],
                    'product' => $name,
                    'qty' => $qty,
                    'unit_price' => $unit,
                    'total' => $total,
                    'price_status' => (!empty($o['price_confirmed']) ? 'confirmed' : 'pending'),
                    'final_price' => isset($o['final_price']) ? $o['final_price'] : null
                ];
            }
        }

        return $this->response->setJSON(['success'=>true,'data'=>$rows]);
    }

    /**
     * Export simple CSV report for daily/weekly/monthly ranges.
     */
    public function exportReport()
    {
        $range = $this->request->getPost('range') ?? 'daily';
        $detailed = $this->request->getPost('detailed') ? true : false;

        // build query per range
        // Use only confirmed final_price for revenue aggregation to avoid counting pending custom orders
        if ($range === 'daily') {
            $rows = $this->orderModel->select("DATE(created_at) as period, COUNT(*) as total_orders, SUM(CASE WHEN price_confirmed = 1 THEN final_price ELSE 0 END) as revenue")->where('order_status', 'finished')->groupBy('period')->orderBy('period','DESC')->get()->getResultArray();
        } elseif ($range === 'weekly') {
            $rows = $this->orderModel->select("YEAR(created_at) as yr, WEEK(created_at,1) as wk, COUNT(*) as total_orders, SUM(CASE WHEN price_confirmed = 1 THEN final_price ELSE 0 END) as revenue")->where('order_status', 'finished')->groupBy('yr,wk')->orderBy('yr','DESC')->orderBy('wk','DESC')->get()->getResultArray();
        } else {
            // monthly
            $rows = $this->orderModel->select("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total_orders, SUM(CASE WHEN price_confirmed = 1 THEN final_price ELSE 0 END) as revenue")->where('order_status', 'finished')->groupBy('period')->orderBy('period','DESC')->get()->getResultArray();
        }

        // If detailed requested, produce per-item CSV rows
        $filename = 'report_' . ($detailed ? 'detailed_' : '') . $range . '_' . date('Ymd_His') . '.csv';
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fprintf($out, "%s", chr(0xEF) . chr(0xBB) . chr(0xBF));
        if ($detailed) {
            // fetch finished orders and expand items
            $orders = $this->orderModel->where('order_status','finished')->orderBy('created_at','DESC')->findAll();
            fputcsv($out, ['date','order_code','product','qty','unit_price','total','price_status','final_price'], ';');
            foreach ($orders as $o) {
                $confirmedFinalPrice = (!empty($o['final_price']) && !empty($o['price_confirmed'])) ? (float)$o['final_price'] : null;
                $lines = preg_split('/\r?\n/', $o['design_description'] ?? '');
                if (!$lines) $lines = [];

                if ($confirmedFinalPrice !== null) {
                    $name = trim($o['product_type'] ?? 'Custom Order');
                    fputcsv($out, [date('Y-m-d', strtotime($o['created_at'])), $o['order_code'], $name, 1, $confirmedFinalPrice, $confirmedFinalPrice, 'confirmed', $confirmedFinalPrice], ';');
                    continue;
                }

                foreach ($lines as $ln) {
                    if (preg_match('/^(\d+)\s*x\s*(.+?)@\s*Rp\s*([0-9\.,]+)/i', $ln, $m)) {
                        $qty = (int)$m[1];
                        $name = trim($m[2]);
                        $unit = floatval(str_replace([',','.',' '], ['', '', ''], $m[3]));
                        $total = $qty * $unit;
                    } elseif (preg_match('/^(\d+)\s*x\s*(.+)$/i', $ln, $m2)) {
                        $qty = (int)$m2[1];
                        $name = trim($m2[2]);
                        $unit = (float)$o['total_price'] / max(1,$qty);
                        $total = $qty * $unit;
                    } else {
                        $qty = 1;
                        $name = trim($ln ?: ($o['product_type'] ?? 'Product'));
                        $unit = (float)$o['total_price'];
                        $total = $unit;
                    }
                    fputcsv($out, [date('Y-m-d', strtotime($o['created_at'])), $o['order_code'], $name, $qty, $unit, $total, (!empty($o['price_confirmed']) ? 'confirmed' : 'pending'), isset($o['final_price']) ? $o['final_price'] : ''], ';');
                }
            }
        } else {
            if ($range === 'weekly') {
                fputcsv($out, ['year','week','total_orders','revenue'], ';');
                foreach ($rows as $r) {
                    fputcsv($out, [$r['yr'],$r['wk'],$r['total_orders'],$r['revenue']], ';');
                }
            } else {
                fputcsv($out, array_keys((array)$rows[0]), ';');
                foreach ($rows as $r) {
                    fputcsv($out, $r, ';');
                }
            }
        }
        fclose($out);
        return;
    }
}

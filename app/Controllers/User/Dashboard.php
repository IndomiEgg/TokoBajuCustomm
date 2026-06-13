<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $orderModel;
    protected $userModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        
        $data = [
            'user' => $this->userModel->find($userId),
            'recentOrders' => $this->orderModel->getUserOrders($userId, null),
            'orderStats' => [
                'pending' => $this->orderModel->where('user_id', $userId)->where('order_status', 'pending')->countAllResults(),
                'approved' => $this->orderModel->where('user_id', $userId)->where('order_status', 'approved')->countAllResults(),
                'in_progress' => $this->orderModel->where('user_id', $userId)->where('order_status', 'in_progress')->countAllResults(),
                'ready_to_ship' => $this->orderModel->where('user_id', $userId)->where('order_status', 'ready_to_ship')->countAllResults(),
                'shipped' => $this->orderModel->where('user_id', $userId)->where('order_status', 'shipped')->countAllResults(),
                'delivered' => $this->orderModel->where('user_id', $userId)->where('order_status', 'delivered')->countAllResults(),
                'finished' => $this->orderModel->where('user_id', $userId)->where('order_status', 'finished')->countAllResults(),
            ],
            'totalSpending' => $this->orderModel->select('SUM(total_price) as total')
                                                 ->where('user_id', $userId)
                                                 ->where('payment_status', 'paid')
                                                 ->first()['total'] ?? 0,
        ];

        return view('user/dashboard', $data);
    }

    public function history()
    {
        $userId = session()->get('user_id');
        
        $data = [
            'orders' => $this->orderModel->getUserOrders($userId),
            'orderStatuses' => OrderModel::getStatuses(),
            'paymentStatuses' => OrderModel::getPaymentStatuses(),
        ];

        return view('user/history', $data);
    }

    public function commissionForm()
    {
        return view('user/commission-form');
    }

    public function editProfile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (empty($user)) {
            return redirect()->to('user/dashboard')->with('error', 'Pengguna tidak ditemukan.');
        }

        return view('user/edit-profile', ['user' => $user]);
    }

    public function updateProfile()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('user/edit-profile');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (empty($user)) {
            return redirect()->to('user/dashboard')->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone_number' => 'permit_empty|numeric|min_length[8]|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'city' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|numeric|max_length[10]',
            'country' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Data tidak valid. Silakan periksa kembali: ' . implode(', ', $this->validator->getErrors()));
        }

        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country'),
        ];

        if ($this->userModel->update($userId, $updateData)) {
            // Update session values so UI reflects latest profile immediately
            session()->set([
                'full_name' => $updateData['full_name'],
                'email' => $updateData['email'],
                'phone_number' => $updateData['phone_number'] ?? null,
            ]);

            return redirect()->to('user/dashboard')->with('success', 'Profil Anda berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
    }

    public function createCommission()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('user/commission-form');
        }

        $userId = session()->get('user_id');
        
        // Validate input
        $rules = [
            'product_type' => 'required|in_list[jacket,hoodie,denim,shirt,tattoo,religious]',
            'garment_size' => 'required',
            'base_color' => 'required|min_length[3]|max_length[50]',
            'material_type' => 'required',
            'artwork_theme' => 'required|min_length[5]|max_length[100]',
            'design_description' => 'required|min_length[50]|max_length[2000]',
            'budget_range' => 'required',
            'target_deadline' => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $message = 'Data tidak valid. Silakan periksa kembali: ' . implode(', ', $errors);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $message, 'errors' => $errors]);
            }

            return redirect()->back()
                            ->withInput()
                            ->with('error', $message);
        }

        // Get placement location as array
        $placementLocation = $this->request->getPost('placement_location') ?? [];
        if (!is_array($placementLocation)) {
            $placementLocation = [$placementLocation];
        }

        // Prepare order data
        $orderData = [
            'product_type' => $this->request->getPost('product_type'),
            'garment_size' => $this->request->getPost('garment_size'),
            'base_color' => $this->request->getPost('base_color'),
            'material_type' => $this->request->getPost('material_type'),
            'artwork_theme' => $this->request->getPost('artwork_theme'),
            'placement_location' => json_encode($placementLocation),
            'design_description' => $this->request->getPost('design_description'),
            'budget_range' => $this->request->getPost('budget_range'),
            'target_deadline' => $this->request->getPost('target_deadline'),
            'notes' => $this->request->getPost('notes') ?? '',
            'user_id' => $userId,
        ];

        // Create order
        $orderData['order_code'] = 'BTM-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 8));
        $orderData['order_status'] = 'pending';
        $orderData['payment_status'] = 'unpaid';
        $orderData['payment_method'] = 'whatsapp';

        if ($this->orderModel->insert($orderData)) {
            $orderId = $this->orderModel->getInsertID();
            $order = $this->orderModel->find($orderId);

            // Log the action
            log_message('info', "Commission order created by user $userId: Order ID $orderId, Theme: " . $orderData['artwork_theme'] . ", Order Code: " . $orderData['order_code']);

            $successMessage = "✓ Pesanan custom Anda telah diterima! Order Code: " . $order['order_code'] . ". Tim kami akan menghubungi Anda segera via WhatsApp.";

            if ($this->request->isAJAX()) {
                $phone = '6281361073822';
                $placementLocations = !empty($placementLocation) ? implode(', ', $placementLocation) : 'Tidak ditentukan';
                $orderText = "Halo, saya ingin memesan pesanan custom berikut:\n";
                $orderText .= "Order Code: {$order['order_code']}\n";
                $orderText .= "Produk: {$order['product_type']}\n";
                $orderText .= "Ukuran: {$order['garment_size']}\n";
                $orderText .= "Warna Dasar: {$order['base_color']}\n";
                $orderText .= "Material: {$order['material_type']}\n";
                $orderText .= "Tema Artwork: {$order['artwork_theme']}\n";
                $orderText .= "Lokasi Penempatan: {$placementLocations}\n";
                $orderText .= "Target Deadline: {$order['target_deadline']}\n";
                $orderText .= "Budget Range: {$order['budget_range']}\n";
                if (!empty($order['notes'])) {
                    $orderText .= "Catatan Tambahan: {$order['notes']}\n";
                }
                $orderText .= "Deskripsi: {$order['design_description']}";
                $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . rawurlencode($orderText);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $successMessage,
                    'whatsapp_url' => $whatsappUrl,
                    'order_text' => $orderText,
                ]);
            }

            return redirect()->to('user/dashboard')
                            ->with('success', $successMessage);
        } else {
            $errorMessage = 'Gagal menyimpan pesanan. Silakan coba lagi.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $errorMessage]);
            }

            return redirect()->back()
                            ->withInput()
                            ->with('error', $errorMessage);
        }
    }
}

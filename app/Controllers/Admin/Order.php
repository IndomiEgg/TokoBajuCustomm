<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class Order extends BaseController
{
    protected $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $data['orders'] = $this->orderModel->orderBy('created_at', 'DESC')->findAll();
        return view('admin/orders', $data);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $paymentStatus = $this->request->getPost('payment_status');
        $order = $this->orderModel->find($id);

        if (!$order) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Order tidak ditemukan']);
            }

            return redirect()->back()->with('error', 'Order tidak ditemukan');
        }

        if (empty($status) && empty($paymentStatus)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Status tidak diberikan']);
            }
            return redirect()->back()->with('error', 'Status tidak diberikan');
        }

        $allowedStatuses = ['pending', 'in_progress', 'processing', 'delivering', 'finished', 'cancelled'];
        $allowedPayments = ['paid', 'unpaid'];

        if (!empty($status) && !in_array($status, $allowedStatuses, true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status order tidak valid']);
        }

        if (!empty($paymentStatus) && !in_array($paymentStatus, $allowedPayments, true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status pembayaran tidak valid']);
        }

        if ($paymentStatus === 'paid' && $order['payment_status'] === 'paid') {
            return $this->response->setJSON(['success' => false, 'message' => 'Order sudah berstatus Paid']);
        }

        // Prevent any changes if order is already finished
        if ($order['order_status'] === 'finished') {
            return $this->response->setJSON(['success' => false, 'message' => 'Order sudah berstatus Finished dan tidak dapat diubah']);
        }

        $updateData = [];
        if ($status !== null && $status !== '' && $status !== $order['order_status']) {
            $updateData['order_status'] = $status;
        }
        if ($paymentStatus !== null && $paymentStatus !== '' && $paymentStatus !== $order['payment_status']) {
            $updateData['payment_status'] = $paymentStatus;
        }

        if (empty($updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tidak ada perubahan pada order',
                'order' => $order,
            ]);
        }

        $this->orderModel->update($id, $updateData);
        $order = $this->orderModel->find($id);
        $message = 'Status order diperbarui';

        if (isset($updateData['payment_status']) && !isset($updateData['order_status'])) {
            $message = 'Status pembayaran berhasil diubah';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'order' => $order,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Update final price / confirmation for an order (admin)
     */
    public function updatePrice($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Order tidak ditemukan']);
        }

        // Prevent changes if order already finished
        if ($order['order_status'] === 'finished') {
            return $this->response->setJSON(['success' => false, 'message' => 'Order sudah berstatus Finished dan tidak dapat diubah']);
        }

        $finalPriceRaw = $this->request->getPost('final_price');
        $priceConfirmed = $this->request->getPost('price_confirmed') ? 1 : 0;
        $priceNote = $this->request->getPost('price_note');

        // sanitize numeric price (allow commas/dots)
        $finalPrice = null;
        if ($finalPriceRaw !== null && $finalPriceRaw !== '') {
            $clean = preg_replace('/[^0-9\.\,]/', '', $finalPriceRaw);
            $clean = str_replace(',', '', $clean);
            if (is_numeric($clean)) $finalPrice = number_format((float)$clean, 2, '.', '');
        }

        // If admin marks confirmed but didn't supply price, reject
        if ($priceConfirmed === 1 && $finalPrice === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Harga final harus diisi saat mengkonfirmasi']);
        }

        $update = [
            'final_price' => $finalPrice,
            'price_confirmed' => $priceConfirmed,
            'price_note' => $priceNote,
            'whatsapp_url' => $this->request->getPost('whatsapp_url') ?? $order['whatsapp_url'] ?? null,
        ];

        $this->orderModel->update($id, $update);
        $order = $this->orderModel->find($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Harga order diperbarui', 'order' => $order]);
        }

        return redirect()->back()->with('success', 'Harga order diperbarui');
    }
}

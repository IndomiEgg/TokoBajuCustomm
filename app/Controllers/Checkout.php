<?php

namespace App\Controllers;

use App\Models\OrderModel;

class Checkout extends BaseController
{
    public function process()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response
                        ->setStatusCode(401)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Unauthorized. Please login before checking out.',
                        ]);
        }

        $payload = $this->request->getJSON(true);
        if (empty($payload)) {
            $payload = $this->request->getPost();
        }

        $items = $payload['items'] ?? $payload['cart'] ?? [];
        if (!is_array($items) || empty($items)) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Cart tidak boleh kosong saat checkout.',
                        ]);
        }

        $totalPrice = isset($payload['total_price']) ? (float) $payload['total_price'] : 0;
        if ($totalPrice <= 0) {
            $totalPrice = 0;
            foreach ($items as $item) {
                $totalPrice += isset($item['price'], $item['quantity'])
                    ? (float) $item['price'] * (int) $item['quantity']
                    : 0;
            }
        }

        $sizes = [];
        $colors = [];
        $materials = [];
        $themes = [];
        $descriptions = [];

        foreach ($items as $item) {
            if (!empty($item['size'])) {
                $sizes[] = $item['size'];
            }
            if (!empty($item['color'])) {
                $colors[] = $item['color'];
            }
            if (!empty($item['material'])) {
                $materials[] = $item['material'];
            }
            if (!empty($item['customDetails']['theme'])) {
                $themes[] = $item['customDetails']['theme'];
            }
            if (!empty($item['name']) && isset($item['quantity'])) {
                $descriptions[] = sprintf(
                    '%d x %s (%s, %s) @ Rp %s',
                    $item['quantity'],
                    $item['name'],
                    $item['size'] ?? 'n/a',
                    $item['color'] ?? 'n/a',
                    number_format($item['price'] ?? 0, 0, ',', '.'),
                );
            }
        }

        $productType = count($items) === 1 ? ($items[0]['name'] ?? 'Shopping Cart') : 'Mixed Cart';
        $artworkTheme = !empty($themes) ? implode(', ', array_unique($themes)) : 'Shopping Cart Checkout';

        $orderDescription = implode("\n", $descriptions);
        $notes = $payload['notes'] ?? $orderDescription;

        $orderData = [
            'order_code' => 'BTM-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 8)),
            'user_id' => $userId,
            'product_type' => $productType,
            'garment_size' => $sizes ? implode(', ', array_unique($sizes)) : null,
            'base_color' => $colors ? implode(', ', array_unique($colors)) : null,
            'material_type' => $materials ? implode(', ', array_unique($materials)) : null,
            'artwork_theme' => $artworkTheme,
            'placement_location' => json_encode([]),
            'design_description' => $orderDescription,
            'budget_range' => $payload['budget_range'] ?? 'N/A',
            'target_deadline' => $payload['target_deadline'] ?? date('Y-m-d', strtotime('+7 days')),
            'total_price' => $totalPrice,
            'order_status' => OrderModel::STATUS_PENDING,
            'payment_status' => OrderModel::PAYMENT_UNPAID,
            'payment_method' => $payload['payment_method'] ?? 'whatsapp',
            'notes' => $notes,
        ];

        $orderModel = new OrderModel();
        if ($orderModel->insert($orderData)) {
            $orderId = $orderModel->getInsertID();

            log_message('info', sprintf(
                'Checkout order created for user %d: order_id=%d, order_code=%s, total=%s',
                $userId,
                $orderId,
                $orderData['order_code'],
                number_format($totalPrice, 0, ',', '.'),
            ));

            return $this->response
                        ->setJSON([
                            'success' => true,
                            'order_id' => $orderId,
                            'order_code' => $orderData['order_code'],
                            'total_price' => $totalPrice,
                        ]);
        }

        return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Gagal menyimpan pesanan. Silakan coba lagi.',
                    ]);
    }
}

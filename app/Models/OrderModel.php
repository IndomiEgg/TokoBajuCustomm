<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'order_code',
        'user_id',
        'product_type',
        'garment_size',
        'base_color',
        'material_type',
        'artwork_theme',
        'placement_location',
        'design_description',
        'budget_range',
        'target_deadline',
        'total_price',
        'final_price',
        'price_confirmed',
        'price_note',
        'whatsapp_url',
        'order_status',
        'payment_status',
        'payment_method',
        'assigned_curator',
        'assigned_designer',
        'notes',
        'approved_at',
        'started_at',
        'quality_checked_at',
        'ready_at',
        'shipped_at',
        'delivered_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Order Status Constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_FINISHED = 'finished';
    const STATUS_QUALITY_CHECK = 'quality_check';
    const STATUS_READY_TO_SHIP = 'ready_to_ship';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment Status Constants
     */
    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';

    /**
     * Get all order statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_DELIVERING => 'Delivering',
            self::STATUS_QUALITY_CHECK => 'Quality Check',
            self::STATUS_READY_TO_SHIP => 'Ready to Ship',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_FINISHED => 'Finished',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    /**
     * Get all payment statuses
     */
    public static function getPaymentStatuses()
    {
        return [
            self::PAYMENT_UNPAID => 'Unpaid',
            self::PAYMENT_PARTIAL => 'Partial',
            self::PAYMENT_PAID => 'Paid'
        ];
    }

    /**
     * Get user's orders
     */
    public function getUserOrders(int $userId, $status = null)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($status) {
            $builder->where('order_status', $status);
        }
        
        $orders = $builder->orderBy('created_at', 'DESC')->findAll();

        foreach ($orders as &$order) {
            if (!empty($order['placement_location']) && is_string($order['placement_location'])) {
                $decoded = json_decode($order['placement_location'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $order['placement_location'] = $decoded;
                }
            }
        }

        return $orders;
    }

    /**
     * Get pending orders (not approved yet)
     */
    public function getPendingOrders()
    {
        return $this->where('order_status', self::STATUS_PENDING)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get orders by status
     */
    public function getByStatus(string $status)
    {
        return $this->where('order_status', $status)
                    ->findAll();
    }

    /**
     * Get active orders (not delivered/cancelled)
     */
    public function getActiveOrders()
    {
        return $this->whereIn('order_status', [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_PROCESSING,
            self::STATUS_DELIVERING,
            self::STATUS_QUALITY_CHECK,
            self::STATUS_READY_TO_SHIP,
            self::STATUS_SHIPPED
        ])->findAll();
    }

    /**
     * Get orders assigned to designer
     */
    public function getDesignerOrders(int $designerId)
    {
        return $this->where('assigned_designer_id', $designerId)
                    ->whereNotIn('order_status', [
                        self::STATUS_DELIVERED,
                        self::STATUS_FINISHED,
                        self::STATUS_CANCELLED
                    ])
                    ->findAll();
    }

    /**
     * Get orders assigned to curator
     */
    public function getCuratorOrders(int $curatorId)
    {
        return $this->where('assigned_curator_id', $curatorId)
                    ->whereNotIn('order_status', [
                        self::STATUS_DELIVERED,
                        self::STATUS_FINISHED,
                        self::STATUS_CANCELLED
                    ])
                    ->findAll();
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status)
    {
        $data = ['order_status' => $status];

        // Update corresponding timestamp
        switch ($status) {
            case self::STATUS_APPROVED:
                $data['approved_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_IN_PROGRESS:
                $data['started_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_QUALITY_CHECK:
                $data['quality_checked_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_READY_TO_SHIP:
                $data['ready_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_SHIPPED:
                $data['shipped_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_DELIVERED:
                $data['delivered_at'] = date('Y-m-d H:i:s');
                break;
        }

        return $this->update($orderId, $data);
    }

    /**
     * Get order with user details
     */
    public function getOrderWithUser(int $orderId)
    {
        return $this->select('orders.*, users.full_name, users.email, users.phone_number')
                    ->join('users', 'users.id = orders.user_id')
                    ->where('orders.id', $orderId)
                    ->first();
    }

    /**
     * Get revenue for period
     */
    public function getRevenueBetween($startDate, $endDate)
    {
        // Return revenue from confirmed final prices if set, otherwise fall back to total_price
        $row = $this->select("SUM(CASE WHEN price_confirmed = 1 THEN final_price ELSE total_price END) as revenue")
                    ->where('payment_status', self::PAYMENT_PAID)
                    ->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->get()
                    ->getRowArray();

        return $row['revenue'] ?? 0;
    }

    /**
     * Revenue that counts only confirmed final prices (useful for reporting pipeline vs recognized)
     */
    public function getConfirmedRevenueBetween($startDate, $endDate)
    {
        return $this->selectSum('final_price')
                    ->where('price_confirmed', 1)
                    ->where('payment_status', self::PAYMENT_PAID)
                    ->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->first()['final_price'] ?? 0;
    }

    /**
     * Get order count for period
     */
    public function getOrderCountBetween($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->countAllResults();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'full_name',
        'username',
        'email',
        'phone_number',
        'password',
        'profile_picture',
        'address',
        'city',
        'postal_code',
        'country',
        'account_status',
        'verification_token',
        'is_verified',
        'whatsapp_verified',
        'last_login'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find user by username
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get user orders count
     */
    public function getUserOrdersCount(int $userId)
    {
        return $this->db->table('orders')
            ->where('user_id', $userId)
            ->countAllResults();
    }

    /**
     * Get user active orders
     */
    public function getUserActiveOrders(int $userId)
    {
        return $this->db->table('orders')
            ->where('user_id', $userId)
            ->whereIn('order_status', ['pending', 'approved', 'in_progress', 'quality_check', 'ready_to_ship', 'shipped'])
            ->get()
            ->getResultArray();
    }

    /**
     * Get user completed orders
     */
    public function getUserCompletedOrders(int $userId)
    {
        return $this->db->table('orders')
            ->where('user_id', $userId)
            ->whereIn('order_status', ['delivered', 'finished'])
            ->get()
            ->getResultArray();
    }
}


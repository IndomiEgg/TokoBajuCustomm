<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table      = 'admin_users';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'full_name',
        'email',
        'password',
        'phone_number',
        'role',
        'department',
        'profile_picture',
        'account_status',
        'is_active',
        'last_login',
        'created_by'
    ];

    protected function getActiveStatusField()
    {
        if ($this->db->fieldExists('account_status', $this->table)) {
            return 'account_status';
        }

        if ($this->db->fieldExists('is_active', $this->table)) {
            return 'is_active';
        }

        return null;
    }

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Role Constants
     */
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_CURATOR = 'curator';
    const ROLE_DESIGNER = 'designer';

    /**
     * Get all available roles
     */
    public static function getRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_CURATOR => 'Curator',
            self::ROLE_DESIGNER => 'Designer'
        ];
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email)
    {
        $activeField = $this->getActiveStatusField();
        if ($activeField === null) {
            return $this->where('email', $email)->first();
        }

        $statusValue = $activeField === 'is_active' ? 1 : 'active';
        return $this->where('email', $email)
                    ->where($activeField, $statusValue)
                    ->first();
    }

    /**
     * Get admins by role
     */
    public function getByRole(string $role)
    {
        $activeField = $this->getActiveStatusField();
        if ($activeField === null) {
            return $this->where('role', $role)->findAll();
        }

        $statusValue = $activeField === 'is_active' ? 1 : 'active';
        return $this->where('role', $role)
                    ->where($activeField, $statusValue)
                    ->findAll();
    }

    /**
     * Get active designers
     */
    public function getActiveDesigners()
    {
        return $this->getByRole(self::ROLE_DESIGNER);
    }

    /**
     * Get active curators
     */
    public function getActiveCurators()
    {
        return $this->getByRole(self::ROLE_CURATOR);
    }

    /**
     * Check admin has permission (basic implementation)
     */
    public function hasPermission(int $adminId, string $permission)
    {
        $admin = $this->find($adminId);
        
        if (!$admin) {
            return false;
        }

        // Super admin has all permissions
        if ($admin['role'] === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        // Define role-based permissions
        $permissions = [
            self::ROLE_ADMIN => [
                'view_orders',
                'manage_orders',
                'manage_payments',
                'view_users',
                'generate_reports'
            ],
            self::ROLE_CURATOR => [
                'view_orders',
                'manage_orders',
                'assign_designers'
            ],
            self::ROLE_DESIGNER => [
                'view_assigned_orders',
                'update_order_progress'
            ]
        ];

        return isset($permissions[$admin['role']]) && 
               in_array($permission, $permissions[$admin['role']]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $adminId)
    {
        return $this->update($adminId, [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
}

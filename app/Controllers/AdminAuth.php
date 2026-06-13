<?php

namespace App\Controllers;

use App\Models\AdminUserModel;
use CodeIgniter\Controller;

class AdminAuth extends Controller
{
    protected $adminModel;
    protected $session;

    public function __construct()
    {
        $this->adminModel = new AdminUserModel();
        $this->session = session();
    }

    

    /**
     * Show Admin Login Page
     */
    public function index()
    {
        // If already logged in as admin, redirect to admin dashboard
        if ($this->session->get('admin_id')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('auth/admin_login');
    }

    /**
     * Process Admin Login
     */
    public function login()
    {
        if (! $this->request->is('post')) {
            return redirect()->back();
        }

        // Validate Input
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Email atau password tidak valid')
                ->withInput();
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Log attempt (don't log password)
        log_message('info', '[AdminAuth] login attempt for: ' . $email);

        // Find admin user
        $admin = $this->adminModel->findByEmail($email);

        if (!$admin) {
            return redirect()->back()
                ->with('error', 'Email atau password salah')
                ->withInput();
        }

        // Verify password
        if (!password_verify($password, $admin['password'])) {
            return redirect()->back()
                ->with('error', 'Email atau password salah')
                ->withInput();
        }

        // Update last login
        $this->adminModel->updateLastLogin($admin['id']);

        // Set session
        $this->session->set([
            'admin_id'       => $admin['id'],
            'admin_email'    => $admin['email'],
            'admin_name'     => $admin['full_name'],
            'admin_role'     => $admin['role'],
            'is_admin_logged_in' => true,
            'admin_last_activity' => time()
        ]);

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success', 'Selamat datang, ' . $admin['full_name'] . '!');
    }

    /**
     * Logout Admin
     */
    public function logout()
    {
        $this->session->remove(['admin_id', 'admin_email', 'admin_name', 'admin_role', 'is_admin_logged_in']);
        return redirect()->to(base_url('admin/login'))
            ->with('success', 'Anda telah logout');
    }

    /**
     * Show Forgot Password Form
     */
    public function forgotPassword()
    {
        return view('auth/admin_forgot_password');
    }

    /**
     * Check Admin Permission
     */
    public function checkPermission(string $permission)
    {
        $adminId = $this->session->get('admin_id');
        
        if (!$adminId) {
            return false;
        }

        return $this->adminModel->hasPermission($adminId, $permission);
    }
}

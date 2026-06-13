<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use Config\Services;

class Auth extends Controller
{
    protected $userModel;
    protected $session;
    protected $logger;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
        $this->logger = Services::logger();
    }

    /**
     * Show Login/Register Page
     */
    public function index()
    {
        // Jika sudah login, redirect ke user dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to(base_url('user/dashboard'));
        }

        return view('auth/login');
    }

    /**
     * Process Login Form
     */
    public function login()
    {
        $this->logger->info("=== LOGIN START ===");
        $this->logger->info("Request method: " . $this->request->getMethod(true));
        
        if (! $this->request->is('post')) {
            $this->logger->warning("Not a POST request");
            return redirect()->back();
        }

        $postData = $this->request->getPost();
        $this->logger->info("POST Data received: " . json_encode(['email' => $postData['email'] ?? null, 'password' => '***']));

        // Validasi Input
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            $this->logger->warning("Validation failed: " . json_encode($this->validator->getErrors()));
            return redirect()->back()
                ->with('error', 'Email atau password tidak valid')
                ->withInput();
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $this->logger->info("Attempting login for email: " . $email);

        // Cari User di Database
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            $this->logger->warning("User not found for email: " . $email);
            return redirect()->back()
                ->with('error', 'Email atau password salah')
                ->withInput();
        }

        $this->logger->info("User found: " . $user['username']);

        // Verifikasi Password
        $passwordIsValid = false;

        if (password_verify($password, $user['password'])) {
            $this->logger->info("Password verified via bcrypt");
            $passwordIsValid = true;
        } elseif ($user['password'] === $password) {
            // Backward compatibility for legacy plain-text passwords stored in the database.
            $this->logger->warning("Using plain-text password (legacy), upgrading to bcrypt");
            $passwordIsValid = true;
            $this->userModel->update($user['id'], [
                'password' => password_hash($password, PASSWORD_BCRYPT),
            ]);
        }

        if (! $passwordIsValid) {
            $this->logger->warning("Password verification failed for user: " . $user['username']);
            return redirect()->back()
                ->with('error', 'Email atau password salah')
                ->withInput();
        }

        $this->logger->info("Password is valid");

        // Cek Status Akun
        if ($user['account_status'] === 'suspended') {
            $this->logger->warning("Account suspended: " . $user['username']);
            return redirect()->back()
                ->with('error', 'Akun Anda telah di-suspend')
                ->withInput();
        }

        if ($user['account_status'] === 'inactive') {
            $this->logger->warning("Account inactive: " . $user['username']);
            return redirect()->back()
                ->with('error', 'Akun Anda tidak aktif. Silakan verifikasi email')
                ->withInput();
        }

        $this->logger->info("Account status is active");

        // Update Last Login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        $this->logger->info("Updated last_login for user: " . $user['username']);

        // Set Session
        $this->session->set([
            'user_id'       => $user['id'],
            'email'         => $user['email'],
            'username'      => $user['username'],
            'full_name'     => $user['full_name'],
            'phone_number'  => $user['phone_number'],
            'is_logged_in'  => true,
            'last_activity' => time()
        ]);

        $this->logger->info("Session set for user: " . $user['username']);
        $this->logger->info("=== LOGIN SUCCESS ===");

        return redirect()->to(base_url('user/dashboard'))
            ->with('success', 'Selamat datang, ' . $user['full_name'] . '!');
    }

    /**
     * Test endpoint
     */
    public function testAuth()
    {
        return $this->response->setJSON([
            'status' => 'Auth controller working',
            'method' => $this->request->getMethod(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Process Register Form
     */
    public function register()
    {
        $postData = $this->request->getPost();
        $this->logger->info("=== REGISTER START ===");
        $this->logger->info("POST Data received: " . json_encode($postData));
        
        // Validate Input
        $rules = [
            'full_name'       => 'required|min_length[3]|max_length[100]',
            'username'        => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'           => 'required|valid_email|is_unique[users.email]',
            'phone_number'    => 'required|min_length[10]|max_length[20]',
            'password'        => 'required|min_length[6]|max_length[255]',
            'password_confirm'=> 'required|matches[password]',
            'agree_terms'     => 'required'
        ];

        $messages = [
            'username' => [
                'is_unique' => 'Username sudah terdaftar'
            ],
            'email' => [
                'is_unique' => 'Email sudah terdaftar'
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak sesuai'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            $this->logger->error("VALIDATION FAILED: " . json_encode($errors));
            return redirect()->back()
                ->with('register_error', implode('<br>', $errors))
                ->withInput();
        }
        
        $this->logger->info("Validation passed");

        // Hash Password
        $hashedPassword = password_hash($postData['password'], PASSWORD_BCRYPT);
        $this->logger->info("Password hashed successfully");

        // Prepare Data
        $data = [
            'full_name'    => $postData['full_name'],
            'username'     => $postData['username'],
            'email'        => $postData['email'],
            'phone_number' => $postData['phone_number'],
            'password'     => $hashedPassword,
            'account_status' => 'active',
            'is_verified'  => 1
        ];
        $this->logger->info("Data prepared for insert: " . json_encode(array_merge($data, ['password' => '***'])));

        // Insert User
        $result = $this->userModel->insert($data);
        $insertErrors = $this->userModel->errors();
        
        if ($result === false) {
            $this->logger->error("Insert FAILED. Errors: " . json_encode($insertErrors));
            return redirect()->back()
                ->with('register_error', 'Gagal menyimpan data: ' . json_encode($insertErrors))
                ->withInput();
        }
        
        $this->logger->info("Insert succeeded with ID: $result");
        
        $newUserId = $this->userModel->getInsertID();
        $this->logger->info("Retrieved insert ID: $newUserId");
        
        $user = $this->userModel->find($newUserId);
        if (!$user) {
            $this->logger->error("Could not retrieve user after insert. ID: $newUserId");
            return redirect()->back()
                ->with('register_error', 'User created but could not retrieve data.')
                ->withInput();
        }
        
        $this->logger->info("User found after insert: " . json_encode($user));

        // Set Session
        $this->session->set([
            'user_id'       => $user['id'],
            'email'         => $user['email'],
            'username'      => $user['username'],
            'full_name'     => $user['full_name'],
            'phone_number'  => $user['phone_number'],
            'is_logged_in'  => true,
            'last_activity' => time()
        ]);
        
        $this->logger->info("Session set for user: " . $user['username']);
        $this->logger->info("=== REGISTER SUCCESS ===");

        return redirect()->to(base_url('user/dashboard'))
            ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user['full_name'] . '!');
    }


    /**
     * Logout User
     */
    public function logout()
    {
        $this->session->remove([
            'user_id',
            'email',
            'username',
            'full_name',
            'phone_number',
            'is_logged_in',
            'last_activity',
        ]);

        $this->session->destroy();
        service('session')->start();

        return redirect()->to(base_url('/'))
            ->with('success', 'Anda telah logout');
    }

    /**
     * Show Forgot Password Form
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    /**
     * Protected Route - Check if logged in
     */
    public function protectedRoute()
    {
        if (!$this->session->get('is_logged_in')) {
            return redirect()->to(base_url('auth'))
                ->with('error', 'Silakan login terlebih dahulu');
        }
    }
}


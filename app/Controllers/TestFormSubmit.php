<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestFormSubmit extends Controller
{
    public function receiveLogin()
    {
        $logger = \Config\Services::logger();
        $logger->info("=== TEST FORM SUBMIT ===");
        $logger->info("Method: " . $this->request->getMethod());
        $logger->info("POST data: " . json_encode($this->request->getPost()));
        
        return $this->response->setJSON([
            'status' => 'received',
            'method' => $this->request->getMethod(),
            'email' => $this->request->getPost('email'),
            'has_password' => !empty($this->request->getPost('password')),
            'csrf_valid' => !empty($this->request->getPost('csrf_test_name')),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

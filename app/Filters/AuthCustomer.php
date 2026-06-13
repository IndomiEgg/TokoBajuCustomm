<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthCustomer implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('is_logged_in')) {
            return redirect()->to(base_url('login'));
        }

        // Inactivity timeout (seconds) - keep in sync with App.php sessionExpiration
        $timeout = 7200; // 2 hours
        $last = $session->get('last_activity') ?? time();

        if (time() - $last > $timeout) {
            $session->destroy();
            return redirect()->to(base_url('login'))
                ->with('error', 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.');
        }

        // Refresh last activity time (sliding expiration)
        $session->set('last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after action required.
    }
}

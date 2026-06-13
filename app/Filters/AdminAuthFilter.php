<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminAuth Filter - Protect admin routes
 */
class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if admin is logged in
        $session = session();

        if (! $session->get('is_admin_logged_in')) {
            return redirect()->to(base_url('admin/login'))
                ->with('error', 'Silakan login sebagai admin terlebih dahulu');
        }

        // Admin inactivity timeout
        $timeout = 7200; // 2 hours
        $last = $session->get('admin_last_activity') ?? $session->get('last_activity') ?? time();

        if (time() - $last > $timeout) {
            // remove admin session keys
            $session->remove(['admin_id', 'admin_email', 'admin_name', 'admin_role', 'is_admin_logged_in']);
            return redirect()->to(base_url('admin/login'))
                ->with('error', 'Sesi admin telah berakhir karena tidak aktif. Silakan login kembali.');
        }

        // refresh admin last activity
        $session->set('admin_last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}

<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --------------------------------------------------------------------
// FRONTEND (Halaman Utama & Belanja)
// --------------------------------------------------------------------
$routes->get('/', 'Home::index');               // Halaman utama (Gallery/Portfolio)
$routes->get('/shop/ready-to-wear', 'Shop::readyToWear'); 
$routes->get('/shop/custom', 'Shop::custom');   // Halaman pesanan kustom

// --------------------------------------------------------------------
// CART & CHECKOUT (Tanpa Payment Gateway)
// --------------------------------------------------------------------
$routes->get('/cart', 'Cart::index', ['filter' => 'authCustomer']);
$routes->post('/cart/add', 'Cart::add', ['filter' => 'authCustomer']);
$routes->post('/checkout/process', 'Checkout::process', ['filter' => 'authCustomer']); // Logika order -> Redirect WA

// --------------------------------------------------------------------
// AUTHENTICATION (Login & Register)
// --------------------------------------------------------------------
$routes->get('/auth', 'Auth::index');                // Menampilkan halaman login/register
$routes->get('/login', 'Auth::index');               // Alias untuk /auth
$routes->post('/auth/login', 'Auth::login');         // Process login
$routes->post('/auth/process', 'Auth::login');       // Legacy login alias
$routes->post('/auth/register', 'Auth::register');   // Register baru user
$routes->get('/auth/logout', 'Auth::logout');        // Logout user
$routes->get('/auth/forgot-password', 'Auth::forgotPassword'); // Forgot password
$routes->get('/dashboard', 'User\Dashboard::index', ['filter' => 'authCustomer']);

// Admin authentication (separate from frontend auth)
$routes->get('/admin/login', 'AdminAuth::index');
$routes->post('/admin/login', 'AdminAuth::login');
$routes->get('/admin/logout', 'AdminAuth::logout');

// --------------------------------------------------------------------
// DASHBOARD USER (Membutuhkan Login)
// --------------------------------------------------------------------
$routes->group('user', ['filter' => 'authCustomer'], function($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');
    $routes->get('history', 'User\Dashboard::history'); // Tracking status pesanan
    $routes->get('commission-form', 'User\Dashboard::commissionForm'); // Menampilkan form pesanan custom
    $routes->post('create-commission', 'User\Dashboard::createCommission'); // Simpan pesanan custom
    $routes->get('edit-profile', 'User\Dashboard::editProfile'); // Edit profil pelanggan
    $routes->post('update-profile', 'User\Dashboard::updateProfile'); // Simpan perubahan profil
});

// --------------------------------------------------------------------
// DASHBOARD ADMIN (Membutuhkan Login Admin)
// --------------------------------------------------------------------
$routes->group('admin', ['filter' => 'authAdmin'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index'); // Visualisasi Chart.js ada di sini
    $routes->get('dashboard/panel/(:any)', 'Admin\Dashboard::panel/$1');
    $routes->get('dashboard/analytics-data', 'Admin\Dashboard::analyticsData');
    $routes->get('dashboard/report-data', 'Admin\Dashboard::reportData');
    $routes->post('dashboard/reports/export', 'Admin\Dashboard::exportReport');
    $routes->get('orders', 'Admin\Order::index');
    $routes->post('orders/update-status/(:num)', 'Admin\Order::updateStatus/$1'); // Update tracking
    $routes->post('orders/update-price/(:num)', 'Admin\Order::updatePrice/$1'); // Set or confirm final price
});

<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Mengarahkan ke file app/Views/home/index.php yang baru saja kita buat
        return view('home/index');
    }
}
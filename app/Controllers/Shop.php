<?php

namespace App\Controllers;

class Shop extends BaseController
{
    public function custom()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('user/commission-form'));
        }

        return redirect()->to(base_url('/') . '#custom');
    }
}

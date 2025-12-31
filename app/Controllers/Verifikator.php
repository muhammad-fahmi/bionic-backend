<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Verifikator extends BaseController
{
    public function index()
    {
        $user_data = session()->get('user_info');
        $sent_data = [
            'page_title' => 'Verifikator Page',
            'user_info' => $user_data
        ];

        return view('verifikator/vw_dashboard', $sent_data);
    }
}

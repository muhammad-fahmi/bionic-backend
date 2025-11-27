<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    public function index()
    {
        $sent_data = [];

        return view('vw_dashboard_admin', $sent_data);
    }

    public function user()
    {
        $users = $this->user->findAll();
        $shifts = $this->user->select('shift_code')->join('r_shifts', 'm_users.id = r_shifts.user_id')->where('shift_date', date('Y-m-d', strtotime('now')))->findAll();
        $sent_data = [
            'users' => $users,
            'shifts' => $shifts
        ];
        return view('vw_m_user', $sent_data);
    }
}

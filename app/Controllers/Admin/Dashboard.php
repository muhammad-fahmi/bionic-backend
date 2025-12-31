<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();

        // Get user counts by role
        $totalUsers = $userModel->countAll();
        $adminCount = $userModel->where('jabatan', 'admin')->countAllResults();
        $operatorCount = $userModel->where('jabatan', 'petugas')->countAllResults();
        $verifikatorCount = $userModel->where('jabatan', 'verifikator')->countAllResults();

        $sent_data = [
            'page_title' => 'Administrator Page',
            'user_info' => session()->get('user_info'),
            'total_users' => $totalUsers,
            'admin_count' => $adminCount,
            'operator_count' => $operatorCount,
            'verifikator_count' => $verifikatorCount,
        ];

        return view('admin/vw_dashboard_admin', $sent_data);
    }
}

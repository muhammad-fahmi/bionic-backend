<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TaskManagement extends BaseController
{
    public function index()
    {
        return view('admin/vw_task_management');
    }
}

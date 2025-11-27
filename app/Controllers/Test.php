<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Test extends BaseController
{
    public function index()
    {
        $model = new UserModel();
        $data = $model->findAll();
        return $this->response->setStatusCode(200)->setJSON($data);
    }
}

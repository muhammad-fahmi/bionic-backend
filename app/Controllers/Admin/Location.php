<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LocationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Location extends BaseController
{
    public function index()
    {
        $locationModel = new LocationModel();
        $locations = $locationModel->findAll();

        $sent_data = [
            'page_title' => 'Manajemen Lokasi',
            'user_info' => session()->get('user_info'),
            'locations' => $locations
        ];

        return view('admin/vw_manage_location', $sent_data);
    }

    public function show($id)
    {
        $locationModel = new LocationModel();
        $location = $locationModel->find($id);

        if (!$location) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'Lokasi tidak ditemukan'
            ]);
        }

        $data = ['location' => $location];
        $formHtml = view('admin/modals/vw_edit_location_form', $data);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 200,
            'html' => $formHtml,
            'location' => $location
        ]);
    }

    public function update($id)
    {
        $locationModel = new LocationModel();

        $data = $this->request->getJSON(true);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'shift' => 'required|integer|in_list[1,2,3]'
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $updateData = [
            'name' => $data['name'],
            'shift' => $data['shift']
        ];

        if ($locationModel->update($id, $updateData)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'Lokasi berhasil diupdate',
                'data' => $updateData
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal mengupdate lokasi'
        ]);
    }

    public function delete($id)
    {
        $locationModel = new LocationModel();

        $location = $locationModel->find($id);
        if (!$location) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'Lokasi tidak ditemukan'
            ]);
        }

        if ($locationModel->delete($id)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'Lokasi berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menghapus lokasi'
        ]);
    }

    public function add()
    {
        $locationModel = new LocationModel();

        $data = $this->request->getJSON(true);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'shift' => 'required|integer|in_list[1,2,3]'
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $insertData = [
            'name' => $data['name'],
            'shift' => $data['shift']
        ];

        if ($locationModel->insert($insertData)) {
            $newLocationId = $locationModel->getInsertID();
            $newLocation = $locationModel->find($newLocationId);

            return $this->response->setStatusCode(201)->setJSON([
                'status' => 201,
                'message' => 'Lokasi berhasil ditambahkan',
                'data' => $newLocation
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menambahkan lokasi'
        ]);
    }
}

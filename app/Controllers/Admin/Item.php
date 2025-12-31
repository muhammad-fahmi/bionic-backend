<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\LocationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Item extends BaseController
{
    protected $user_data;

    public function __construct()
    {
        $this->user_data = session()->get('user_info');
    }

    public function index()
    {
        $itemModel = new ItemModel();
        $locationModel = new LocationModel();

        $items = $itemModel->getAllItemWithLocation();
        $location = $locationModel->findAll();

        // Process nama_display for each item
        foreach ($items as &$item) {
            $nama_display = str_replace('_', ' ', $item['nama']);
            $nama_display = str_replace('langit langit', 'langit-langit', $nama_display);
            $item['nama_display'] = $nama_display;
        }

        $sent_data = [
            'page_title' => 'Manajemen Item',
            'user_info' => session()->get('user_info'),
            'items' => $items,
            'locations' => $location
        ];

        return view('admin/vw_manage_item', $sent_data);
    }

    public function modal()
    {
        $type = $this->request->getPost('type');
        switch ($type) {
            case 'add':
                $location = new LocationModel();
                $data = [
                    'locations' => $location->select('id,name')->findAll()
                ];
                return $this->response->setJSON([
                    "title" => "Add Item",
                    "formHTML" => view('admin/modals/vw_add_item_form', $data)
                ]);
            case 'edit':
                $id = $this->request->getPost('id');
                $itemModel = new ItemModel();
                $item = $itemModel->find($id);
                if (!$item) {
                    return $this->response->setStatusCode(404)->setJSON([
                        "status" => "Failed",
                        "message" => "Item tidak ditemukan."
                    ]);
                }
                // Process nama_display
                $nama_display = str_replace('_', ' ', $item['nama']);
                $nama_display = str_replace('langit langit', 'langit-langit', $nama_display);
                $item['nama_display'] = $nama_display;

                $location = new LocationModel();
                $data = [
                    'item' => $item,
                    'locations' => $location->select('id,name')->findAll()
                ];
                return $this->response->setJSON([
                    "title" => "Edit Item",
                    "formHTML" => view('admin/modals/vw_edit_item_form', $data)
                ]);
            case 'delete':
                $id = $this->request->getPost('id');
                $itemModel = new ItemModel();
                $item = $itemModel->find($id);
                if (!$item) {
                    return $this->response->setStatusCode(404)->setJSON([
                        "status" => "Failed",
                        "message" => "Item tidak ditemukan."
                    ]);
                }
                $data = [
                    'item' => $item
                ];
                return $this->response->setJSON([
                    "title" => "Hapus Item",
                    "formHTML" => view('admin/modals/vw_delete_item_form', $data)
                ]);
            default:
                return $this->response->setStatusCode(404)->setJSON([
                    "status" => "Failed",
                    "message" => "Modal tidak ditemukan."
                ]);
        }
    }

    public function update($id)
    {
        $itemModel = new ItemModel();

        $rules = [
            'lokasi_id' => 'permit_empty|integer',
            'nama' => 'required|min_length[3]|max_length[100]',
            'type' => 'required|in_list[complete_task,additional_task]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->request->getPost();

        $updateData = [
            'lokasi_id' => $data['lokasi_id'] ?? null,
            'nama' => $data['nama'],
            'nama_display' => $data['nama'],
            'type' => $data['type']
        ];

        if ($itemModel->update($id, $updateData)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'Item berhasil diupdate',
                'data' => $updateData
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal mengupdate item'
        ]);
    }

    public function delete($id)
    {
        $itemModel = new ItemModel();

        $item = $itemModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'Item tidak ditemukan'
            ]);
        }

        if ($itemModel->delete($id)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'Item berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menghapus item'
        ]);
    }

    public function add()
    {
        $itemModel = new ItemModel();

        $rules = [
            'lokasi_id' => 'permit_empty|integer',
            'nama' => 'required|min_length[3]|max_length[100]',
            'type' => 'required|in_list[complete_task,additional_task]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->request->getPost();

        $insertData = [
            'lokasi_id' => $data['lokasi_id'] ?? null,
            'nama' => $data['nama'],
            'nama_display' => $data['nama'],
            'type' => $data['type']
        ];

        if ($itemModel->insert($insertData)) {
            $newItemId = $itemModel->getInsertID();
            $newItem = $itemModel->find($newItemId);

            return $this->response->setStatusCode(201)->setJSON([
                'status' => 201,
                'message' => 'Item berhasil ditambahkan',
                'data' => $newItem
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menambahkan item'
        ]);
    }
}

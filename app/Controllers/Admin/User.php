<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    public function index()
    {
        $user = new UserModel();
        $users = $user->getAllUser();
        $sent_data = [
            'page_title' => 'Manage User',
            'user_info' => session()->get('user_info'),
            'users' => $users
        ];
        return view('admin/vw_manage_user', $sent_data);
    }

    public function create()
    {
        $userModel = new UserModel();

        // Validation rules
        $rules = [
            'nama' => 'required|min_length[3]|max_length[200]',
            'jabatan' => 'required|in_list[petugas,verifikator,admin]',
            'username' => 'required|min_length[3]|max_length[150]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        $messages = [
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok dengan password'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->request->getJSON(true);

        // Check if username already exists
        if ($userModel->isUsernameExists($data['username'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Username sudah digunakan',
                'errors' => ['username' => 'Username sudah digunakan']
            ]);
        }

        // Prepare insert data
        $insertData = [
            'nama' => $data['nama'],
            'jabatan' => $data['jabatan'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ];

        if ($userModel->createUser($insertData)) {
            // Get the newly created user
            $newUserId = $userModel->getInsertID();
            $newUser = $userModel->getUserById($newUserId);

            return $this->response->setStatusCode(201)->setJSON([
                'status' => 201,
                'message' => 'User berhasil ditambahkan',
                'data' => $newUser
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menambahkan user'
        ]);
    }

    public function show($id)
    {
        $userModel = new UserModel();
        $user = $userModel->getUserById($id);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'User tidak ditemukan'
            ]);
        }

        $data = ['user' => $user];
        $formHtml = view('admin/modals/vw_edit_user_form', $data);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 200,
            'html' => $formHtml,
            'user' => $user
        ]);
    }

    public function update($id)
    {
        $userModel = new UserModel();

        // Validation rules
        $rules = [
            'nama' => 'required|min_length[3]|max_length[200]',
            'jabatan' => 'required|in_list[petugas,verifikator,admin]',
            'username' => 'required|min_length[3]|max_length[150]',
            'password' => 'permit_empty|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->request->getJSON(true);

        // Check unique username
        if (!$userModel->isUsernameUnique($data['username'], $id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Username sudah digunakan',
                'errors' => ['username' => 'Username sudah digunakan']
            ]);
        }

        // Prepare update data
        $updateData = [
            'nama' => $data['nama'],
            'jabatan' => $data['jabatan'],
            'username' => $data['username']
        ];

        // Only update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if ($userModel->updateUser($id, $updateData)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'User berhasil diupdate',
                'data' => $updateData
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal mengupdate user'
        ]);
    }

    public function delete($id)
    {
        $userModel = new UserModel();

        // Check if user exists
        $user = $userModel->getUserById($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'User tidak ditemukan'
            ]);
        }

        // Prevent deleting yourself (optional security check)
        $currentUserId = session()->get('user_info')['id'] ?? null;
        if ($currentUserId == $id) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Anda tidak dapat menghapus akun sendiri'
            ]);
        }

        if ($userModel->deleteUser($id)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'User berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menghapus user'
        ]);
    }
}

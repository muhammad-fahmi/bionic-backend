<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\IncomingRequest;

/**
 * @property IncomingRequest $request
 */
class Auth extends BaseController
{
    // Auth - Login Entry Point
    public function login()
    {
        if (session()->has('jwt')) {
            $jwt = $this->jwt->decode(session()->get('jwt'));
            if (time() <= $jwt['expire_time']) {
                if ($jwt['user_role'] == 'administrator') {
                    $role = 'admin';
                } else if ($jwt['user_role'] == 'operator') {
                    $role = 'operator';
                } else if ($jwt['user_role'] == 'verifikator') {
                    $role = 'verifikator';
                } else {
                    return redirect()->to('auth/login');
                }
                return redirect()->to($role);
            }
        }

        $sent_data = [
            'page_title' => "Login Page"
        ];
        return view('auth/vw_login', $sent_data);
    }

    // Login Handler
    public function login_handler()
    {
        if (!$this->request->is('post')) {
            return redirect('auth/login');
        }

        // Input validation
        $validation = service('validation');
        $rules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 50 karakter'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 3 karakter'
                ]
            ]
        ];

        $data = $this->request->getPost(array_keys($rules));

        // Run Validation
        if (!$this->validateData($data, $rules)) {
            // Validation failed, return to login page with errors
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $validData = $this->validator->getValidated();

        $username = $validData['username'];
        $password = $validData['password'];

        $model = new UserModel();
        $user = $model
            ->select('user_id,name,user_role,password')
            ->where('username', $username)
            ->first();

        if (!$user) {
            // User not found
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        [
            'user_id' => $id,
            'name' => $name,
            'user_role' => $role,
            'password' => $db_password
        ] = $user;

        if (!password_verify($password, $db_password)) {
            // Password incorrect
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        // Handle different user roles
        if ($role == 'verifikator') {
            $data = [
                'user_id' => $id,
                'name' => $name,
                'user_role' => $role,
                'expire_time' => time() + (5 * 60)
            ];

            $this->jwt->encode($data);
            return redirect('verifikator');
        }

        if ($role == 'administrator') {
            $data = [
                'user_id' => $id,
                'name' => $name,
                'user_role' => $role,
                'expire_time' => time() + (5 * 60)
            ];

            $this->jwt->encode($data);
            return redirect('admin');
        }

        // Store shift info in session including dates for notification logic
        $data = [
            'user_id' => $id,
            'name' => $name,
            'user_role' => $role,
            'expire_time' => time() + (5 * 60)
        ];

        $this->jwt->encode($data);

        return redirect('operator');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}

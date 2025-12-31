<?php

namespace App\Controllers;

use App\Models\ShiftModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use DateTime;

/**
 * @property IncomingRequest $request
 */
class Auth extends ResourceController
{

    // Auth - Login Entry Point
    public function login()
    {
        session()->destroy();
        $sent_data = [
            'page_title' => "Login Page"
        ];
        return view('auth/vw_login', $sent_data);
    }

    // Login Handler
    public function login_handler()
    {
        if (!$this->request->is('post')) {
            return view('auth/login');
        }

        // Input validation
        $validation = service('validation');
        $rules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[5]|max_length[50]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 5 karakter',
                    'max_length' => '{field} maksimal 50 karakter'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 5 karakter'
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

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $model = new UserModel();
        $user = $model
            ->select('id,nama,jabatan,password')
            ->where('username', $username)
            ->first();

        if (!$user) {
            // User not found
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        [
            'id' => $id,
            'nama' => $nama,
            'jabatan' => $jabatan,
            'password' => $db_password
        ] = $user;

        if (!password_verify($password, $db_password)) {
            // Password incorrect
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        // Handle different user roles
        if ($jabatan == 'verifikator') {
            $data = [
                'id' => $id,
                'nama' => $nama,
                'jabatan' => $jabatan,
            ];

            session()->set('user_info', $data);
            return redirect('verifikator');
        }

        if ($jabatan == 'admin') {
            $data = [
                'id' => $id,
                'nama' => $nama,
                'jabatan' => $jabatan,
            ];

            session()->set('user_info', $data);
            return redirect('admin');
        }

        // Handle operator (petugas) login with dynamic shift assignment
        $shiftAssignmentModel = new \App\Models\ShiftAssignmentModel();
        $shiftRotationService = new \App\Libraries\ShiftRotationService();

        // Get current shift assignment
        $currentShift = $shiftAssignmentModel->getCurrentShift($id);

        // If no shift assigned or shift expired, assign a new one
        if (!$currentShift || $currentShift['end_date'] < date('Y-m-d')) {
            // Auto-assign a balanced shift to this operator
            $shiftRotationService->assignShiftToNewUser($id, date('Y-m-d'));

            // Re-fetch the newly assigned shift
            $currentShift = $shiftAssignmentModel->getCurrentShift($id);
        }

        $shiftCode = $currentShift['shift_code'] ?? 1; // Default to shift 1 if something goes wrong

        // Log daily shift usage to r_shifts table (for backward compatibility)
        $shift_model = new ShiftModel();
        $data_exist = $shift_model
            ->where('user_id', $id)
            ->where('shift_date', date('Y-m-d'))
            ->findAll();

        if (count($data_exist) == 0) {
            $shift_model->save([
                'user_id' => $id,
                'shift_code' => $shiftCode,
                'shift_date' => date('Y-m-d')
            ]);
        }

        // Store shift info in session including dates for notification logic
        $data = [
            'id' => $id,
            'nama' => $nama,
            'jabatan' => $jabatan,
            'shift' => $shiftCode,
            'shift_start_date' => $currentShift['start_date'] ?? null,
            'shift_end_date' => $currentShift['end_date'] ?? null,
        ];

        session()->set('user_info', $data);

        return redirect('operator');
    }


    public function create()
    {
        [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'username' => $username,
            'password' => $password,
        ] = $this->request->getVar();

        $model = new UserModel();
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1
        ];
        $result = $model->insert($data, false);
        if ($result) {
            echo "Data berhasil ditambahkan";
            return $this->response->setStatusCode(200);
        } else {
            echo "Data gagal ditambahkan";
            return $this->response->setStatusCode(201);
        }
    }
}

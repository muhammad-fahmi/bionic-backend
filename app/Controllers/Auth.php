<?php

namespace App\Controllers;

use App\Models\ShiftModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use DateTime;

/**
 * @property IncomingRequest $request
 */
class Auth extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Data awal
        $shift = [1, 2, 3];

        // Daftar nama karyawan
        $names = ['Ari', 'Yanto', 'Budi'];

        // Hitung offset berdasarkan tanggal
        // Misal: tiap hari akan bergeser 1 posisi
        $date = new DateTime();
        $day = (int)$date->format('z'); // hari ke-berapa dalam setahun (0-365)
        $offset = $day % count($shift); // rotasi berdasarkan sisa bagi

        // Lakukan rotasi array
        $rotatedShift = array_merge(
            array_slice($shift, $offset),
            array_slice($shift, 0, $offset)
        );

        // Mapping hasil rotasi ke variabel
        foreach ($names as $index => $name) {
            $$name = $rotatedShift[$index];
        }

        $model = new UserModel();
        [
            'id' => $id,
            'nama' => $nama,
            'jabatan' => $jabatan,
            'password' => $db_password
        ] = $model
            ->select('*')
            ->where('username', $username)
            ->first();


        if (password_verify($password, $db_password)) {
            if ($jabatan == 'verifikator') {
                $data = [
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                ];
            } else {
                $shift_model = new ShiftModel();
                $data_exist = $shift_model->where('user_id', $id)->where('shift_date', date('Y-m-d', strtotime('now')))->findAll();
                if (count($data_exist) == 0) {
                    $shift_model->save([
                        'user_id' => $id,
                        'shift_code' => $$nama,
                        'shift_date' => date('Y-m-d')
                    ]);
                }

                $data = [
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'shift' => $$nama
                ];
            }

            return $this->response
                ->setStatusCode(200)
                ->setJSON($data);
        } else {
            return $this->response
                ->setStatusCode(404,"user tidak ditemukan");
        }
    }

    /**
     * Membuat user baru(hanya bisa diakses oleh admin)
     *
     * @return ResponseInterface
     */
    public function create()
    {
        [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'username' => $username,
            'password' => $password,
            'id_shift' => $id_shift
        ] = $this->request->getVar();

        $model = new UserModel();
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id_shift' => $id_shift,
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

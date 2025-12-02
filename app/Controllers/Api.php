<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class Api extends BaseController
{
    public function getTask($shift, $id_lokasi = false)
    {
        if ($shift >= 4) {
            return $this->response->setStatusCode(404);
        }

        $tasks = $this->item
            ->select('m_items.id,nama as "item",lokasi,shift,lokasi_id')
            ->join('m_locations', 'm_items.lokasi_id = m_locations.id')
            ->where('shift', $shift)->findAll();

        $actions = $this->item
            ->select('m_items.id,nama_aksi')
            ->join('m_locations', 'm_items.lokasi_id = m_locations.id')
            ->join('m_actions', 'm_items.nama = m_actions.nama_item')
            ->where('shift', $shift)->findAll();

        if ($id_lokasi != false) {
            $tasks = $this->item
                ->select('m_items.id,nama as "item",lokasi,shift,lokasi_id')
                ->join('m_locations', 'm_items.lokasi_id = m_locations.id')
                ->where('shift', $shift)
                ->where('m_locations.id', $id_lokasi)->findAll();

            $actions = $this->item
                ->select('m_items.id,nama_aksi')
                ->join('m_locations', 'm_items.lokasi_id = m_locations.id')
                ->join('m_actions', 'm_items.nama = m_actions.nama_item')
                ->where('shift', $shift)
                ->where('m_locations.id', $id_lokasi)->findAll();

            $results = [];
            foreach ($actions as $action) {
                if (!isset($results[$action['id']])) {
                    $results[$action['id']] = [$action['nama_aksi']];
                } else {
                    array_push($results[$action['id']], $action['nama_aksi']);
                }
            }

            $end_result = [];
            foreach ($tasks as $task) {
                foreach ($results as $key => $result) {
                    if ($task['id'] == $key) {
                        $task['aksi'] = $result;
                        array_push($end_result, $task);
                    }
                }
            }
        }


        return $this->response->setStatusCode(200)->setJSON($end_result);
    }

    public function getQr()
    {
        $location = $this->location->findAll();
        return $this->response->setStatusCode(200)->setJSON($location);
    }

    public function postTask()
    {
        // ambil data dari input request
        // panggil model - model master yang diperlukan untuk mengambil data
        // join kan tabel
        // cek apakah input null atau '',jika iya kirim pesan error
        // panggil model - model history yang diperlukan untuk menyimpan data
        // simpan data di masing - masing tabel melalui model
        $data = $this->request->getVar('data');
        $this->submittedTask->insert([
            "tanggal" => (new DateTime())->format("Y-m-d H:i:s"),
            "petugas" => $data->nama,
            "shift" => $data->id_shift,
            "lokasi_id" => $data->lokasi_id,
            "status" => 'pending'
        ]);
        $task_id = $this->submittedTask->getInsertID();
        foreach ($data->data as $key => $kondisi) {
            $this->submittedTaskItem->insert([
                "task_submission_id" => $task_id,
                "item_id" => $key,
                "kondisi" => $kondisi[0],
            ]);
            if (isset($kondisi[1])) {
                foreach ($kondisi[1] as $aksi => $value) {
                    if ($value == true) {
                        $this->submittedTaskAction->insert([
                            "task_submission_id" => $task_id,
                            "item_id" => $key,
                            "nama" => $aksi
                        ]);
                    }
                }
            }
        }
        $response = [
            "status" => "OK",
            "message" => "Data berhasil ditambahkan."
        ];

        return $this->response->setStatusCode(200)->setJSON($response);
    }

    public function getSubmittedTask()
    {
        $data = $this->submittedTask
            ->select("r_tasksubmissions.id,r_tasksubmissions.tanggal,r_tasksubmissions.petugas,r_tasksubmissions.shift,r_tasksubmissions.lokasi_id,r_tasksubmissions.status,m_locations.lokasi,m_locations.shift")
            ->join('m_locations', 'r_tasksubmissions.lokasi_id = m_locations.id')
            ->findAll();

        return $this->response->setStatusCode(200)->setJSON($data);
    }

    public function updateSubmittedTask()
    {
        $data = $this->request->getVar('data');
        $id = $this->request->getVar('id');
        $this->submittedTask
            ->update($id, [
                'status' => $data,
            ]);

        return $this->response->setStatusCode(200)->setJSON([
            'message' => "Task berhasil di update"
        ]);
    }
}

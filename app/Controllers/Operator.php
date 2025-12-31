<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\LocationModel;
use App\Models\TaskSubmissionActionModel;
use App\Models\TaskSubmissionItemModel;
use App\Models\TaskSubmissionModel;
use App\Models\TaskViewModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use ErrorException;
use Exception;

class Operator extends BaseController
{
    protected $user_data;
    public function __construct()
    {
        $this->user_data = (session()->has('user_info')) ? session()->get('user_info') : '';
    }

    public function index()
    {
        $location = new LocationModel();
        $sent_data = [
            'page_title' => 'Dashboard',
            'user_info' => $this->user_data,
            'rooms' => $location->getAllRoomByShift($this->user_data['shift'])
        ];
        return view('operator/vw_dashboard', $sent_data);
    }

    public function scan($location_id = null)
    {
        $item = new ItemModel();
        $location = new LocationModel();

        // Fetch items with their associated actions
        $items = $item->getItemsWithActionsByLocation($location_id);

        $sent_data = [
            'page_title' => 'Scan Page',
            'user_info' => $this->user_data,
            'location' => $location->getLocationNameById($location_id),
            'location_id' => $location_id,
            'items' => $items
        ];

        return view('operator/vw_qr_scan', $sent_data);
    }

    public function submit()
    {
        $post_data = $this->request->getPost();

        $taskSubmissionModel = new TaskSubmissionModel();
        $taskSubmissionItemModel = new TaskSubmissionItemModel();
        $taskSubmissionActionModel = new TaskSubmissionActionModel();

        // Create main task submission
        $taskSubmissionModel->save([
            'tanggal' => date('Y-m-d H:i:s'),
            'petugas' => $this->user_data['nama'],
            'shift' => $this->user_data['shift'],
            'lokasi_id' => (int) $post_data['lokasi_id'],
            'status' => 'pending'
        ]);
        $submission_id = $taskSubmissionModel->getInsertID();

        // Process each item
        foreach ($post_data as $key => $value) {
            // Check if this is an item kondisi field
            if (strpos($key, 'item_kondisi_') === 0) {
                $item_id = str_replace('item_kondisi_', '', $key);

                // Save item status
                $taskSubmissionItemModel->save([
                    'task_submission_id' => $submission_id,
                    'item_id' => (int) $item_id,
                    'kondisi' => $value,
                    'revisi' => ''
                ]);
            }

            // Check if this is an action checkbox
            if (strpos($key, 'action_') === 0) {
                $parts = explode('_', $key);
                if (count($parts) >= 3) {
                    $action_id = $parts[1];
                    $item_id = $parts[2];

                    // Save action status
                    $taskSubmissionActionModel->save([
                        'task_submission_id' => $submission_id,
                        'item_id' => (int) $item_id,
                        'nama' => $post_data['action_name_' . $action_id . '_' . $item_id] ?? '',
                        'dikerjakan' => ($value === 'on' || $value === '1') ? 1 : 0
                    ]);
                }
            }
        }

        session()->setFlashdata('success', 'Task berhasil disubmit!');
        return redirect()->to('operator');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShiftAssignmentModel;
use App\Libraries\ShiftRotationService;
use CodeIgniter\HTTP\ResponseInterface;

class Shift extends BaseController
{
    protected $shiftAssignmentModel;
    protected $shiftRotationService;

    public function __construct()
    {
        $this->shiftAssignmentModel = new ShiftAssignmentModel();
        $this->shiftRotationService = new ShiftRotationService();
    }

    /**
     * Display shift management page
     */
    public function index()
    {
        $stats = $this->shiftRotationService->getShiftStatistics();

        $sent_data = [
            'page_title' => 'Manajemen Shift',
            'user_info' => session()->get('user_info'),
            'stats' => $stats
        ];

        return view('admin/vw_manage_shift', $sent_data);
    }

    /**
     * Initialize shifts for all operators (first-time setup)
     */
    public function initialize()
    {
        $startDate = $this->request->getJSON()->start_date ?? date('Y-m-d');
        $createdBy = session()->get('user_info')['id'] ?? null;

        // Check for existing assignments
        $existingShifts = $this->shiftAssignmentModel->getAllActiveShifts();

        if (!empty($existingShifts)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Shift assignment sudah ada. Gunakan "Rotate All" untuk merotasi shift yang ada.',
                'existing_count' => count($existingShifts)
            ]);
        }

        $result = $this->shiftRotationService->initializeShifts($startDate, $createdBy);

        if ($result['success'] > 0) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => "Berhasil menginisialisasi {$result['success']} shift assignment",
                'data' => $result
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menginisialisasi shift',
            'errors' => $result['errors']
        ]);
    }

    /**
     * Rotate all active shifts to next shift number
     */
    public function rotateAll()
    {
        $startDate = $this->request->getJSON()->start_date ?? date('Y-m-d');
        $createdBy = session()->get('user_info')['id'] ?? null;

        $result = $this->shiftRotationService->rotateAllShifts($startDate, $createdBy);

        if ($result['success'] > 0) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => "Berhasil merotasi {$result['success']} shift assignment",
                'data' => $result
            ]);
        }

        if ($result['total_shifts'] === 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Tidak ada shift aktif untuk dirotasi. Inisialisasi shift terlebih dahulu.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal merotasi shift',
            'errors' => $result['errors']
        ]);
    }

    /**
     * Manually assign shift to a specific user
     */
    public function assignUser()
    {
        $rules = [
            'user_id' => 'required|integer',
            'shift_code' => 'required|integer|in_list[1,2,3]',
            'start_date' => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->request->getJSON(true);
        $createdBy = session()->get('user_info')['id'] ?? null;

        $result = $this->shiftAssignmentModel->assignShift(
            $data['user_id'],
            $data['shift_code'],
            $data['start_date'],
            $createdBy
        );

        if ($result) {
            // Get the updated shift info
            $shift = $this->shiftAssignmentModel->getCurrentShift($data['user_id']);

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'Shift berhasil di-assign',
                'data' => $shift
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal meng-assign shift'
        ]);
    }

    /**
     * Get shift history for a specific user
     */
    public function history($userId)
    {
        $history = $this->shiftAssignmentModel->getShiftHistory($userId, 20);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 200,
            'data' => $history
        ]);
    }

    /**
     * Get current shift statistics (AJAX endpoint)
     */
    public function getStatistics()
    {
        $stats = $this->shiftRotationService->getShiftStatistics();

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 200,
            'data' => $stats
        ]);
    }
}

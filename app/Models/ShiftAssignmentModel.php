<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftAssignmentModel extends Model
{
    protected $table = 'r_shift_assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'shift_code',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get the current active shift for a specific user
     *
     * @param int $userId
     * @return array|null Returns shift data or null if no active shift
     */
    public function getCurrentShift(int $userId): ?array
    {
        return $this->select('r_shift_assignments.*, m_users.nama, m_users.jabatan')
                    ->join('m_users', 'm_users.id = r_shift_assignments.user_id')
                    ->where('r_shift_assignments.user_id', $userId)
                    ->where('r_shift_assignments.is_active', 1)
                    ->first();
    }

    /**
     * Get all currently active shift assignments
     * Excludes deleted users via JOIN
     *
     * @return array
     */
    public function getAllActiveShifts(): array
    {
        return $this->select('r_shift_assignments.*, m_users.nama, m_users.jabatan, m_users.username')
                    ->join('m_users', 'm_users.id = r_shift_assignments.user_id')
                    ->where('r_shift_assignments.is_active', 1)
                    ->where('m_users.jabatan', 'petugas') // Only operators
                    ->orderBy('r_shift_assignments.shift_code', 'ASC')
                    ->orderBy('m_users.nama', 'ASC')
                    ->findAll();
    }

    /**
     * Get shifts that are expiring soon (for notifications)
     *
     * @param int $days Number of days before expiration (default 1 for day 6 notification)
     * @return array
     */
    public function getShiftsExpiringSoon(int $days = 1): array
    {
        $targetDate = date('Y-m-d', strtotime("+{$days} days"));

        return $this->select('r_shift_assignments.*, m_users.nama, m_users.jabatan, m_users.username')
                    ->join('m_users', 'm_users.id = r_shift_assignments.user_id')
                    ->where('r_shift_assignments.is_active', 1)
                    ->where('r_shift_assignments.end_date', $targetDate)
                    ->where('m_users.jabatan', 'petugas')
                    ->findAll();
    }

    /**
     * Get shifts that have expired and need rotation
     *
     * @return array
     */
    public function getShiftsNeedingRotation(): array
    {
        $today = date('Y-m-d');

        return $this->select('r_shift_assignments.*, m_users.nama, m_users.jabatan, m_users.username')
                    ->join('m_users', 'm_users.id = r_shift_assignments.user_id')
                    ->where('r_shift_assignments.is_active', 1)
                    ->where('r_shift_assignments.end_date <', $today)
                    ->where('m_users.jabatan', 'petugas')
                    ->findAll();
    }

    /**
     * Calculate days remaining in current shift for a user
     *
     * @param int $userId
     * @return int|null Returns number of days or null if no active shift
     */
    public function getDaysRemaining(int $userId): ?int
    {
        $shift = $this->getCurrentShift($userId);

        if (!$shift) {
            return null;
        }

        $today = new \DateTime();
        $endDate = new \DateTime($shift['end_date']);
        $diff = $today->diff($endDate);

        return (int) $diff->format('%r%a'); // Returns negative if expired, positive if future
    }

    /**
     * Assign a shift to a user
     * Creates an 8-day assignment period starting from the specified date
     * Day 1-7: Active shift period
     * Day 7: Notification appears
     * Day 8: Shift changes (end date)
     *
     * @param int $userId
     * @param int $shiftCode (1, 2, or 3)
     * @param string $startDate Format: Y-m-d
     * @param int|null $createdBy Admin user ID
     * @return bool
     */
    public function assignShift(int $userId, int $shiftCode, string $startDate, ?int $createdBy = null): bool
    {
        // Calculate end date (7 days after start date = 8-day period)
        // Shift changes on day 8
        $endDate = date('Y-m-d', strtotime($startDate . ' +7 days'));

        // Use database transaction for atomicity
        $this->db->transStart();

        // Deactivate any existing active shift for this user
        $this->deactivateUserShift($userId);

        // Create new assignment
        $data = [
            'user_id' => $userId,
            'shift_code' => $shiftCode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => 1,
            'created_by' => $createdBy,
        ];

        $result = $this->insert($data);

        $this->db->transComplete();

        return $this->db->transStatus() && $result !== false;
    }

    /**
     * Deactivate the current active shift for a user
     * Called before assigning a new shift
     *
     * @param int $userId
     * @return bool
     */
    public function deactivateUserShift(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('is_active', 1)
                    ->set(['is_active' => 0])
                    ->update();
    }

    /**
     * Get shift history for a specific user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getShiftHistory(int $userId, int $limit = 10): array
    {
        return $this->select('r_shift_assignments.*, m_users.nama as created_by_name')
                    ->join('m_users', 'm_users.id = r_shift_assignments.created_by', 'left')
                    ->where('r_shift_assignments.user_id', $userId)
                    ->orderBy('r_shift_assignments.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get count of operators by shift code
     * Used for balancing assignments
     *
     * @return array ['1' => count, '2' => count, '3' => count]
     */
    public function getShiftDistribution(): array
    {
        $result = $this->select('shift_code, COUNT(*) as count')
                       ->join('m_users', 'm_users.id = r_shift_assignments.user_id')
                       ->where('r_shift_assignments.is_active', 1)
                       ->where('m_users.jabatan', 'petugas')
                       ->groupBy('shift_code')
                       ->findAll();

        $distribution = ['1' => 0, '2' => 0, '3' => 0];

        foreach ($result as $row) {
            $distribution[$row['shift_code']] = (int) $row['count'];
        }

        return $distribution;
    }

    /**
     * Get all operators (petugas) from m_users
     *
     * @return array
     */
    public function getAllOperators(): array
    {
        $userModel = new \App\Models\UserModel();
        return $userModel->where('jabatan', 'petugas')->findAll();
    }
}

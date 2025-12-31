<?php

namespace App\Libraries;

use App\Models\ShiftAssignmentModel;
use App\Models\UserModel;

class ShiftRotationService
{
    protected $shiftAssignmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->shiftAssignmentModel = new ShiftAssignmentModel();
        $this->userModel = new UserModel();
    }

    /**
     * Initialize shifts for all operators (first-time setup)
     * Assigns shifts in round-robin pattern: user_index % 3 + 1
     *
     * @param string $startDate Format: Y-m-d (default: today)
     * @param int|null $createdBy Admin user ID
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function initializeShifts(string $startDate = null, ?int $createdBy = null): array
    {
        $startDate = $startDate ?? date('Y-m-d');
        $operators = $this->userModel->where('jabatan', 'petugas')->findAll();

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($operators as $index => $operator) {
            // Round-robin algorithm: (index % 3) + 1
            $shiftCode = ($index % 3) + 1;

            $result = $this->shiftAssignmentModel->assignShift(
                $operator['id'],
                $shiftCode,
                $startDate,
                $createdBy
            );

            if ($result) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Failed to assign shift to user {$operator['nama']} (ID: {$operator['id']})";
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
            'total_operators' => count($operators)
        ];
    }

    /**
     * Rotate all active shifts to the next shift number
     * Rotation algorithm: (current_shift % 3) + 1
     * 1 → 2, 2 → 3, 3 → 1
     *
     * @param string $startDate Format: Y-m-d (default: today)
     * @param int|null $createdBy Admin user ID
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function rotateAllShifts(string $startDate = null, ?int $createdBy = null): array
    {
        $startDate = $startDate ?? date('Y-m-d');
        $activeShifts = $this->shiftAssignmentModel->getAllActiveShifts();

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($activeShifts as $shift) {
            // Rotation algorithm: (current_shift % 3) + 1
            $nextShift = ($shift['shift_code'] % 3) + 1;

            $result = $this->shiftAssignmentModel->assignShift(
                $shift['user_id'],
                $nextShift,
                $startDate,
                $createdBy
            );

            if ($result) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Failed to rotate shift for user {$shift['nama']} (ID: {$shift['user_id']})";
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
            'total_shifts' => count($activeShifts)
        ];
    }

    /**
     * Automatically rotate only expired shifts
     * Called by cron job daily
     *
     * @param string $startDate Format: Y-m-d (default: today)
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function autoRotateExpiredShifts(string $startDate = null): array
    {
        $startDate = $startDate ?? date('Y-m-d');
        $expiredShifts = $this->shiftAssignmentModel->getShiftsNeedingRotation();

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($expiredShifts as $shift) {
            // Rotation algorithm: (current_shift % 3) + 1
            $nextShift = ($shift['shift_code'] % 3) + 1;

            $result = $this->shiftAssignmentModel->assignShift(
                $shift['user_id'],
                $nextShift,
                $startDate,
                null // Auto-rotation, no created_by
            );

            if ($result) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Failed to auto-rotate shift for user {$shift['nama']} (ID: {$shift['user_id']})";
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
            'total_expired' => count($expiredShifts)
        ];
    }

    /**
     * Assign a balanced shift to a new operator
     * Chooses the shift with the minimum number of operators
     *
     * @param int $userId
     * @param string $startDate Format: Y-m-d (default: today)
     * @param int|null $createdBy Admin user ID
     * @return bool
     */
    public function assignShiftToNewUser(int $userId, string $startDate = null, ?int $createdBy = null): bool
    {
        $startDate = $startDate ?? date('Y-m-d');

        // Get current distribution
        $distribution = $this->shiftAssignmentModel->getShiftDistribution();

        // Find shift with minimum operators
        $minShift = 1;
        $minCount = $distribution['1'];

        foreach ([2, 3] as $shift) {
            if ($distribution[$shift] < $minCount) {
                $minShift = $shift;
                $minCount = $distribution[$shift];
            }
        }

        // Assign the balanced shift
        return $this->shiftAssignmentModel->assignShift(
            $userId,
            $minShift,
            $startDate,
            $createdBy
        );
    }

    /**
     * Get the next shift number based on rotation algorithm
     *
     * @param int $currentShift
     * @return int
     */
    public function getNextShift(int $currentShift): int
    {
        return ($currentShift % 3) + 1;
    }

    /**
     * Check if any shifts are expiring soon (for notifications)
     *
     * @param int $days Number of days before expiration (default: 1)
     * @return array
     */
    public function getExpiringShifts(int $days = 1): array
    {
        return $this->shiftAssignmentModel->getShiftsExpiringSoon($days);
    }

    /**
     * Get shift statistics for admin dashboard
     *
     * @return array
     */
    public function getShiftStatistics(): array
    {
        $distribution = $this->shiftAssignmentModel->getShiftDistribution();
        $activeShifts = $this->shiftAssignmentModel->getAllActiveShifts();
        $expiredShifts = $this->shiftAssignmentModel->getShiftsNeedingRotation();
        $expiringShifts = $this->shiftAssignmentModel->getShiftsExpiringSoon(1);

        return [
            'distribution' => $distribution,
            'total_active' => count($activeShifts),
            'total_expired' => count($expiredShifts),
            'total_expiring_soon' => count($expiringShifts),
            'active_shifts' => $activeShifts,
            'expired_shifts' => $expiredShifts,
            'expiring_shifts' => $expiringShifts
        ];
    }
}

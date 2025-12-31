<?php

/**
 * Get shift notification for the current user
 * Returns notification data if the user's shift is expiring soon or expired
 *
 * @return array|null ['type' => 'warning'|'danger', 'title' => string, 'message' => string, 'days_remaining' => int, 'next_shift' => int]
 */
if (!function_exists('getShiftNotification')) {
    function getShiftNotification(): ?array
    {
        $userInfo = session()->get('user_info');

        // Only show notifications for operators
        if (!$userInfo || $userInfo['jabatan'] !== 'petugas') {
            return null;
        }

        $endDate = $userInfo['shift_end_date'] ?? null;
        $currentShift = $userInfo['shift'] ?? null;

        if (!$endDate || !$currentShift) {
            return null;
        }

        // Calculate days remaining
        $today = new DateTime();
        $end = new DateTime($endDate);
        $diff = $today->diff($end);
        $daysRemaining = (int) $diff->format('%r%a'); // Negative if expired, positive if future

        // Calculate next shift
        $nextShift = ($currentShift % 3) + 1;

        // Expired shift (past end date)
        if ($daysRemaining < 0) {
            return [
                'type' => 'danger',
                'title' => 'Shift Anda Telah Berakhir!',
                'message' => "Shift {$currentShift} Anda telah berakhir sejak {$endDate}. Shift baru Anda adalah Shift {$nextShift}. Silakan hubungi admin jika belum diperbarui.",
                'days_remaining' => $daysRemaining,
                'next_shift' => $nextShift,
                'end_date' => $endDate
            ];
        }

        // Day 6 notification (1 day before expiration)
        if ($daysRemaining === 1) {
            $tomorrowDate = date('Y-m-d', strtotime($endDate));
            return [
                'type' => 'warning',
                'title' => 'Shift Akan Berubah Besok!',
                'message' => "Shift {$currentShift} Anda akan berakhir besok ({$tomorrowDate}). Mulai lusa, shift Anda akan berubah menjadi Shift {$nextShift}.",
                'days_remaining' => $daysRemaining,
                'next_shift' => $nextShift,
                'end_date' => $endDate
            ];
        }

        // Today is the last day
        if ($daysRemaining === 0) {
            return [
                'type' => 'warning',
                'title' => 'Hari Terakhir Shift Ini!',
                'message' => "Hari ini adalah hari terakhir untuk Shift {$currentShift}. Mulai besok, shift Anda akan berubah menjadi Shift {$nextShift}.",
                'days_remaining' => $daysRemaining,
                'next_shift' => $nextShift,
                'end_date' => $endDate
            ];
        }

        // No notification needed
        return null;
    }
}

/**
 * Render shift notification alert HTML
 * Returns Bootstrap alert HTML if there's a notification to display
 *
 * @return string HTML content or empty string
 */
if (!function_exists('renderShiftNotification')) {
    function renderShiftNotification(): string
    {
        $notification = getShiftNotification();

        if (!$notification) {
            return '';
        }

        $alertClass = $notification['type'] === 'danger' ? 'alert-danger' : 'alert-warning';
        $icon = $notification['type'] === 'danger' ? 'fa-circle-exclamation' : 'fa-triangle-exclamation';

        $html = <<<HTML
        <div class="alert {$alertClass} alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid {$icon} me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">{$notification['title']}</h5>
                    <p class="mb-0">{$notification['message']}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        HTML;

        return $html;
    }
}

/**
 * Get shift days remaining for display
 *
 * @return array ['days' => int, 'total_days' => 8, 'percentage' => float]
 */
if (!function_exists('getShiftProgress')) {
    function getShiftProgress(): array
    {
        $userInfo = session()->get('user_info');

        if (!$userInfo || $userInfo['jabatan'] !== 'petugas') {
            return ['days' => 0, 'total_days' => 8, 'percentage' => 0];
        }

        $startDate = $userInfo['shift_start_date'] ?? null;
        $endDate = $userInfo['shift_end_date'] ?? null;

        if (!$startDate || !$endDate) {
            return ['days' => 0, 'total_days' => 8, 'percentage' => 0];
        }

        $today = new DateTime();
        $end = new DateTime($endDate);
        $diff = $today->diff($end);
        $daysRemaining = (int) $diff->format('%r%a');

        // Ensure non-negative
        $daysRemaining = max(0, $daysRemaining);

        $percentage = ($daysRemaining / 8) * 100;

        return [
            'days' => $daysRemaining,
            'total_days' => 8,
            'percentage' => round($percentage, 1),
            'end_date' => $endDate
        ];
    }
}

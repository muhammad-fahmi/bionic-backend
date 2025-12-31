<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .shift-badge {
        font-size: 1.2rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    #table_shifts {
        width: 100%;
        text-align: center;

        & th {
            font-style: oblique;
            font-size: 16px;
        }
    }

    .alert-expiring {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    .alert-expired {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen Shift
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid p-3">
    <div class="d-flex justify-content-start my-3">
        <a href="/admin" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #5d87ff;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Active</h6>
                            <h3 class="mb-0"><?= $stats['total_active'] ?></h3>
                        </div>
                        <i class="fa-solid fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #49beff;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Shift 1</h6>
                            <h3 class="mb-0"><?= $stats['distribution']['1'] ?></h3>
                        </div>
                        <span class="shift-badge bg-info-subtle text-info">1</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #13deb9;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Shift 2</h6>
                            <h3 class="mb-0"><?= $stats['distribution']['2'] ?></h3>
                        </div>
                        <span class="shift-badge bg-success-subtle text-success">2</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #ffae1f;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Shift 3</h6>
                            <h3 class="mb-0"><?= $stats['distribution']['3'] ?></h3>
                        </div>
                        <span class="shift-badge bg-warning-subtle text-warning">3</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <?php if ($stats['total_expired'] > 0): ?>
        <div class="alert alert-expired alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Shift Kadaluarsa Ditemukan!</h5>
                    <p class="mb-0">Terdapat <?= $stats['total_expired'] ?> shift yang sudah kadaluarsa. Klik "Rotate All Shifts" untuk memperbarui.</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($stats['total_expiring_soon'] > 0): ?>
        <div class="alert alert-expiring alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-triangle-exclamation me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Shift Akan Berakhir Besok</h5>
                    <p class="mb-0"><?= $stats['total_expiring_soon'] ?> shift akan berakhir besok. Sistem akan otomatis merotasi saat cron job berjalan.</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Aksi Manajemen Shift</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success" onclick="initializeShifts()" <?= $stats['total_active'] > 0 ? 'disabled' : '' ?>>
                    <i class="fa-solid fa-play"></i>
                    Initialize Shifts
                </button>
                <button class="btn btn-primary" onclick="rotateAllShifts()" <?= $stats['total_active'] == 0 ? 'disabled' : '' ?>>
                    <i class="fa-solid fa-rotate"></i>
                    Rotate All Shifts
                </button>
                <button class="btn btn-info" onclick="refreshStats()">
                    <i class="fa-solid fa-refresh"></i>
                    Refresh Statistics
                </button>
            </div>
            <small class="text-muted d-block mt-2">
                <i class="fa-solid fa-info-circle"></i>
                Initialize hanya tersedia jika belum ada shift aktif. Gunakan Rotate untuk merotasi shift yang ada.
            </small>
        </div>
    </div>

    <!-- Current Assignments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Shift Assignment Aktif</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="table_shifts">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Operator</th>
                            <th>Username</th>
                            <th>Shift</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Sisa Hari</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['active_shifts'])): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada shift aktif. Klik "Initialize Shifts" untuk memulai.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $index = 0; ?>
                            <?php foreach ($stats['active_shifts'] as $shift): ?>
                                <?php
                                $today = new DateTime();
                                $endDate = new DateTime($shift['end_date']);
                                $diff = $today->diff($endDate);
                                $daysRemaining = (int) $diff->format('%r%a');

                                $statusClass = '';
                                $statusText = '';
                                if ($daysRemaining < 0) {
                                    $statusClass = 'bg-danger-subtle text-danger';
                                    $statusText = 'Kadaluarsa';
                                } elseif ($daysRemaining <= 1) {
                                    $statusClass = 'bg-warning-subtle text-warning';
                                    $statusText = 'Segera Berakhir';
                                } else {
                                    $statusClass = 'bg-success-subtle text-success';
                                    $statusText = 'Aktif';
                                }

                                $shiftBadgeClass = match($shift['shift_code']) {
                                    1 => 'bg-info-subtle text-info',
                                    2 => 'bg-success-subtle text-success',
                                    3 => 'bg-warning-subtle text-warning',
                                    default => 'bg-secondary-subtle text-secondary'
                                };
                                ?>
                                <tr>
                                    <td><?= ++$index; ?></td>
                                    <td><?= esc($shift['nama']) ?></td>
                                    <td><?= esc($shift['username']) ?></td>
                                    <td>
                                        <span class="badge <?= $shiftBadgeClass ?>">
                                            Shift <?= $shift['shift_code'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y', strtotime($shift['start_date'])) ?></td>
                                    <td><?= date('d M Y', strtotime($shift['end_date'])) ?></td>
                                    <td>
                                        <?= $daysRemaining >= 0 ? $daysRemaining . ' hari' : abs($daysRemaining) . ' hari lalu' ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    async function initializeShifts() {
        if (!confirm('Apakah Anda yakin ingin menginisialisasi shift untuk semua operator?\n\nIni akan membuat assignment shift baru dengan pola round-robin.')) {
            return;
        }

        try {
            const response = await fetch('<?= base_url('admin/manage/shift/initialize') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    start_date: '<?= date('Y-m-d') ?>',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                })
            });

            const result = await response.json();

            if (result.status === 200) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.message || 'Gagal menginisialisasi shift');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menginisialisasi shift');
        }
    }

    async function rotateAllShifts() {
        if (!confirm('Apakah Anda yakin ingin merotasi SEMUA shift?\n\nSemua operator akan dipindah ke shift berikutnya (1→2, 2→3, 3→1).')) {
            return;
        }

        try {
            const response = await fetch('<?= base_url('admin/manage/shift/rotate-all') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    start_date: '<?= date('Y-m-d') ?>',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                })
            });

            const result = await response.json();

            if (result.status === 200) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.message || 'Gagal merotasi shift');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat merotasi shift');
        }
    }

    async function refreshStats() {
        try {
            const response = await fetch('<?= base_url('admin/manage/shift/statistics') ?>');
            const result = await response.json();

            if (result.status === 200) {
                location.reload(); // Simple approach: reload the page
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat refresh statistik');
        }
    }
</script>
<?= $this->endSection() ?>

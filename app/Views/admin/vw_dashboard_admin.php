<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('page_title') ?>
Admin Page
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
    .btn {
        width: auto;
        height: 100px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-3">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-around align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total User</h6>
                            <h2 class="mb-0 fw-bold text-center"><?= $total_users ?? 0 ?></h2>
                        </div>
                        <div class="text-primary">
                            <iconify-icon icon="heroicons:user-group-solid" width="72" height="72"
                                class="opacity-50 mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-around align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Admin</h6>
                            <h2 class="mb-0 fw-bold text-center"><?= $admin_count ?? 0 ?></h2>
                        </div>
                        <div class="text-danger">
                            <iconify-icon icon="fa7-solid:user-shield" width="72" height="72"
                                class="opacity-50 mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Petugas</h6>
                            <h2 class="mb-0 fw-bold text-center"><?= $operator_count ?? 0 ?></h2>
                        </div>
                        <div class="text-success">
                            <iconify-icon icon="fa7-solid:user-gear" width="72" height="72"
                                class="opacity-50 mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Verifikator</h6>
                            <h2 class="mb-0 fw-bold text-center"><?= $verifikator_count ?? 0 ?></h2>
                        </div>
                        <div class="text-warning">
                            <iconify-icon icon="fa7-solid:user-check" width="72" height="72"
                                class="opacity-50 mb-0"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
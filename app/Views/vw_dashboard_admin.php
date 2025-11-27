<?= $this->extend('vw_master') ?>

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
    <div class="card">
        <h5 class="card-header">ADMIN DASHBOARD</h5>
        <div class="card-body">
            <div class="row justify-content-around">
                <div class="col-md-6 my-2">
                    <a href="/admin/m_user" class="btn btn-secondary d-flex flex-column justify-content-center align-items-center">
                        <i class="fa-solid fa-user" style="font-size: 24px;"></i>
                        Manajemen User
                    </a>
                </div>
                <div class="col-md-6 my-2">
                    <a href="#" class="btn btn-secondary d-flex flex-column justify-content-center align-items-center" onclick="alert('on development')">
                        <i class="fa-solid fa-list-check" style="font-size: 24px;"></i>
                        Manajemen Shift
                    </a>
                </div>
                <div class="col-md-6 my-2">
                    <a href="#" class="btn btn-secondary d-flex flex-column justify-content-center align-items-center" onclick="alert('on development')">
                        <i class="fa-solid fa-door-open" style="font-size: 24px;"></i>
                        Manajemen Ruangan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
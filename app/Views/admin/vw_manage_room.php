<?= $this->extend('vw_master') ?>

<?= $this->section('style') ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen Room
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <div class="d-flex justify-content-start my-3">
        <a href="/admin/dashboard" class="btn btn-primary text-end">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>
    <div class="card">
        <h5 class="card-header">Manajemen Shift</h5>
        <div class="card-body p-3 table_container">
            <table class="table" id="table_data_shift">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>
<?= $this->endSection() ?>


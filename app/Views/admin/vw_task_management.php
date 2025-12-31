<?= $this->extend('vw_master') ?>

<?= $this->section('page_title') ?>
Task Submission
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-3">
    <div class="card">
        <h5 class="card-header">TASK SUBMISSION</h5>
        <div class="card-body">
            <table class="table text-center" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Shift</th>
                        <th>Status Task</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 1; ?>
                    <?php foreach ($data as $task): ?>
                        <tr>
                            <td><?= $index++; ?></td>
                            <td><?= $task['tanggal']; ?></td>
                            <td><?= $task['petugas']; ?></td>
                            <td><?= $task['lokasi']; ?></td>
                            <td><?= $task['shift']; ?></td>
                            <td>
                                <span
                                    class="badge <?= $task['status'] == 'pending' ? 'text-bg-warning' : ($task['status'] == 'approved' ? 'text-bg-success' : 'text-bg-danger') ?> p-2">
                                    <?= $task['status'] ?>
                                </span>
                            </td>
                            <td class="d-flex justify-content-center">
                                <button type="button" class="btn btn-sm btn-info mx-2" onclick="alert('Under Development')">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <span>
                                        Detail
                                    </span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
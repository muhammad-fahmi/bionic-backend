<?= $this->extend('vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_user {
        width: 100%;
        text-align: center;

        & th {
            font-style: oblique;
            font-size: 18px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen User
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <div class="d-flex justify-content-start my-3">
        <a href="/admin" class="btn btn-primary text-end">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                            <label for="floatingInput">Nama</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                                <option selected>Open this select menu</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                            <label for="floatingSelect">Works with selects</label>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                                    <label for="floatingInput">Username</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="floatingInput" placeholder="name@example.com">
                                    <label for="floatingInput">Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                                <option selected>Open this select menu</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                            <label for="floatingSelect">Works with selects</label>
                        </div>
                        <div class="form-floating mb-3">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5>Manajemen User</h5>
            <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="fa-solid fa-user-plus"></i>
                Add User
            </button>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table" id="table_data_user">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Shift Hari Ini</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 0; ?>
                    <?php foreach ($users as $key => $user): ?>
                        <?php if ($user['jabatan'] == 'admin') continue; ?>
                        <tr>
                            <td><?= ++$index; ?></td>
                            <td><?= $user['nama']; ?></td>
                            <td><?= ucfirst($user['jabatan']); ?></td>
                            <?php if (isset($shifts[$key])): ?>
                                <td><?= $shifts[$key]['shift_code']; ?></td>
                            <?php else: ?>
                                <?php if ($user['jabatan'] == 'petugas'): ?>
                                    <td><?= "Belum Ditentukan"; ?></td>
                                <?php else: ?>
                                    <td><?= "-"; ?></td>
                                <?php endif; ?>
                            <?php endif; ?>
                            <td>
                                <div class="btn-group-justified">
                                    <button class="btn btn-primary">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        Detail
                                    </button>
                                    <button class="btn btn-warning">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit
                                    </button>
                                    <button class="btn btn-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
    function showModal() {
        console.log('function called');
    }
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="modal fade" id="vertical-center-scroll-modal" tabindex="-1" aria-labelledby="vertical-center-modal"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        <span id="modal_title"></span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Overflowing text to show scroll behavior</h4>
                    <p>
                        Praesent commodo cursus magna, vel scelerisque
                        nisl consectetur et. Vivamus sagittis lacus vel
                        augue laoreet rutrum faucibus dolor auctor.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-danger-subtle text-danger font-medium waves-effect text-start"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg') ?>" alt="" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-n3 text-center">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class=" d-flex align-items-center justify-content-center"
                                style="width: 110px; height: 110px;">
                                <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                    style="width: 100px; height: 100px;">
                                    <img src="<?= base_url('assets/profiles/') . $user_info['nama'] . '.jpg' ?>" alt=""
                                        class="w-100 h-100">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="fs-5 mb-0 fw-semibold"><?= $user_info['nama'] ?></h5>
                            <p class="mb-0 fs-4"><?= $user_info['jabatan'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-0">
        <div class="card-header text-bg-primary text-white">
            <h3 class="card-title text-white text-center">
                Daftar Pekerjaan Yang Di Sudah Dikerjakan
            </h3>
        </div>
        <div class="card-body">
            <div class="table-container overflow-x-scroll">
                <table class="table table-striped text-center" style="vertical-align: middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="list_pekerjaan"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    (async function () {
        const response = await fetch('api/getTaskSubmission/list');
        const data = await response.json();
        $.each(data, function (index, value) {
            $('#list_pekerjaan').append(`
                <tr>
                    <td>${++index}</td>
                    <td>${value.tanggal}</td>
                    <td>${value.petugas}</td>
                    <td>${value.lokasi}</td>
                    <td>
                        <div class="badge rounded-pill text-bg-warning">
                            ${value.status}
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-primary d-flex p-1" onclick="showModal(${value.id},'${value.lokasi}')">
                            <iconify-icon icon="ph:magnifying-glass-bold" width="20" height="20" class="me-1"></iconify-icon>
                            <span>Detail</span>
                        </button>
                    </td>
                </tr>
            `);
        });
    })();

    function showModal(id, lokasi) {
        $('#vertical-center-scroll-modal').modal('toggle');
        $('#modal_title').html(lokasi);
    }
</script>
<?= $this->endSection() ?>
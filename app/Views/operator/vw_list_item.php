<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg'); ?>" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-n3 text-center">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class=" d-flex align-items-center justify-content-center"
                                style="width: 110px; height: 110px;">
                                <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                    style="width: 100px; height: 100px;">
                                    <img src="<?= base_url('assets/profiles') . "/" . $user_info['nama'] . ".jpg"; ?>"
                                        class="w-100 h-100">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="fs-5 mb-0 fw-semibold"><?= $user_info['nama'] ?></h5>
                            <p class="mb-0 fs-4"><?= $user_info['jabatan'] ?></p>
                            <small class="mb-0 fs-4">Shift: <?= $user_info['shift'] ?></sm>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden p-3">
        <h3 class="card-title"><?= $location ?></h3>
        <?php foreach ($items as $item): ?>
            <div class="card overflow-hidden p-3">
                <div class="card-body">
                    <a href="#" class="btn btn-primary"><?= $item['name'] ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<!--  -->
<?= $this->endSection() ?>
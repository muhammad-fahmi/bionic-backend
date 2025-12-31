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
                                    <img src="<?= base_url('assets/profiles/') . esc($user_info['nama']) . '.jpg' ?>"
                                        class="w-100 h-100">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="fs-5 mb-0 fw-semibold"><?= esc($user_info['nama']) ?></h5>
                            <p class="mb-0 fs-4"><?= esc($user_info['jabatan']) ?></p>
                            <small class="mb-0 fs-4">Shift: <?= esc($user_info['shift']) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden p-3" id="camera_container">
        <h3 class="card-title text-center text-uppercase fw-bold d-flex align-items-center justify-content-center mb-3">
            <?= esc($location) ?>
        </h3>
        <div class="card-body d-flex p-2 justify-content-center">
            <div id="reader" style="width:50vw;"></div>
        </div>
        <div class="card-footer d-flex">
            <a class="btn btn-primary d-flex align-items-center px-1 py-2" href="<?= base_url('operator') ?>">
                <iconify-icon icon="streamline:return-2-remix" class="mx-2"></iconify-icon>
                Kembali
            </a>
        </div>
    </div>

    <form action="<?= base_url('operator/submit_task'); ?>" method="post">
        <?= csrf_field(); ?>
        <input type="hidden" name="lokasi_id" value="<?= $location_id ?>">

        <!-- List Daftar Pekerjaan -->
        <div class="card overflow-hidden p-3 d-none" id="task_content_container">
            <h3 class="card-title text-center text-uppercase fw-bold d-flex align-items-center justify-content-center mb-3">
                <?= esc($location) ?>
            </h3>
            <div class="card-body p-0">
                <?php if (empty($items)): ?>
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Tidak ada item untuk dibersihkan di lokasi ini.
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div class="card mb-3">
                            <div class="card-header text-bg-secondary text-white">
                                <h5 class="card-title text-white mb-0">
                                    <?= esc($item['nama_display'] ?? $item['nama']) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Item Status Selection -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status Item:</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio"
                                               class="btn-check"
                                               name="item_kondisi_<?= $item['id'] ?>"
                                               id="dibersihkan_<?= $item['id'] ?>"
                                               value="dibersihkan"
                                               checked>
                                        <label class="btn btn-outline-success" for="dibersihkan_<?= $item['id'] ?>">
                                            <i class="fa-solid fa-check-circle me-1"></i>
                                            Dibersihkan
                                        </label>

                                        <input type="radio"
                                               class="btn-check"
                                               name="item_kondisi_<?= $item['id'] ?>"
                                               id="dilewati_<?= $item['id'] ?>"
                                               value="dilewati">
                                        <label class="btn btn-outline-warning" for="dilewati_<?= $item['id'] ?>">
                                            <i class="fa-solid fa-forward me-1"></i>
                                            Dilewati
                                        </label>
                                    </div>
                                </div>

                                <!-- Actions Checklist -->
                                <?php if (!empty($item['actions'])): ?>
                                    <div class="actions-section">
                                        <label class="form-label fw-bold">Aksi yang Dikerjakan:</label>
                                        <div class="list-group">
                                            <?php foreach ($item['actions'] as $action): ?>
                                                <label class="list-group-item d-flex align-items-center">
                                                    <input class="form-check-input me-2"
                                                           type="checkbox"
                                                           name="action_<?= $action['id'] ?>_<?= $item['id'] ?>"
                                                           id="action_<?= $action['id'] ?>_<?= $item['id'] ?>"
                                                           value="1">
                                                    <input type="hidden"
                                                           name="action_name_<?= $action['id'] ?>_<?= $item['id'] ?>"
                                                           value="<?= esc($action['nama_aksi']) ?>">
                                                    <span><?= esc($action['nama_aksi']) ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted mb-0">
                                        <i class="fa-solid fa-info-circle me-1"></i>
                                        Tidak ada aksi spesifik untuk item ini.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a class="btn btn-danger d-flex align-items-center" href="<?= base_url('operator'); ?>">
                    <iconify-icon icon="majesticons:restricted" width="17" height="17" class="me-1"></iconify-icon>
                    <span>Batal</span>
                </a>
                <button class="btn btn-primary d-flex align-items-center" type="submit">
                    <iconify-icon icon="material-symbols:save" width="17" height="17" class="me-1"></iconify-icon>
                    <span>Simpan</span>
                </button>
            </div>
        </div>
    </form>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    (async () => {
        const html5QrCode = new Html5Qrcode("reader");
        // This method will trigger user permissions
        await Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                let cameraId = devices[0].id;
                html5QrCode.start(
                    { deviceId: { exact: cameraId } },
                    {
                        fps: 5,
                        qrbox: 250
                    },
                    (decodedText, decodedResult) => {
                        try {
                            const data = JSON.parse(decodedText);
                            // Check if QR code matches the location ID
                            if (data.id == '<?= $location_id ?>') {
                                $("#task_content_container").removeClass('d-none');
                                html5QrCode.stop().then(() => {
                                    $("#camera_container").css('display', 'none');
                                }).catch((err) => {
                                    console.log('Error stopping camera:', err);
                                });
                            } else {
                                alert('Ruangan yang anda kunjungi tidak sesuai! Silahkan scan QR code untuk ruangan: <?= esc($location) ?>');
                            }
                        } catch (e) {
                            alert('QR Code tidak valid! Silahkan scan QR code yang benar.');
                            console.error('QR Parse Error:', e);
                        }
                    })
                    .catch((err) => {
                        console.log('Scan error:', err);
                    });
            }
        }).catch(err => {
            console.error('Camera error:', err);
            alert('Kamera tidak dapat diakses! Pastikan Anda memberikan izin akses kamera.');
        });
    })();
</script>
<?= $this->endSection() ?>

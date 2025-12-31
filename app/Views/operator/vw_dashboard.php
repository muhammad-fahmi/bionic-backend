<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg') ?>" class="img-fluid">
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
                            <h4 class="fs-5 mb-1 fw-semibold text-uppercase"><?= esc($user_info['nama']); ?>
                                (<?= $user_info['jabatan'] ?>)
                            </h4>
                            <h5 class="mb-0 text-uppercase">-- Shift <?= esc($user_info['shift']); ?> --</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Display shift notification if available
    echo renderShiftNotification();
    ?>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card p-3">
        <h3 class="card-title text-center text-uppercase">
            Daftar Ruangan
        </h3>
        <hr style="border: 1px solid black;">
        <input type="text" id="searchBox" placeholder="Search..." class="form-control" />
        <div class="card-body">
            <div class="row justify-content-center">
                <?php foreach ($rooms as $room): ?>
                    <div class='col-sm-12 col-md-4 col-lg-6 p-2 room_button'>
                        <button class="btn btn-primary text-bg-primary p-3 text-uppercase"
                            style="width:100%;height:calc(100vh / 10);"
                            onclick="location.href = '<?= base_url('operator/scan/') . $room['id']; ?>'">
                            <?= $room['name'] ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    const searchBox = document.getElementById("searchBox");
    const listItems = document.querySelectorAll("div.room_button");

    searchBox.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase();
        listItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(query)) {
                item.style.display = ""; // Show
            } else {
                item.style.display = "none"; // Hide
            }
        });
    });
</script>
<?= $this->endSection() ?>
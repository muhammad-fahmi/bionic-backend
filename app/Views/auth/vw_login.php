<?= $this->extend('layouts/vw_auth_layout') ?>

<?= $this->section('page_title') ?>
Login Page
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div class="card-body">
    <a href="<?= base_url('/auth/login'); ?>" class="text-nowrap logo-img text-center d-block mb-5 w-100">
        <b class="logo-icon">
            <img src="<?= base_url("logo_dark.png") ?>" style="width:32px" alt="homepage">
        </b>
        <span class="logo-text">
            <h4 class="text-uppercase d-inline">Bionic Natura</h4>
        </span>
    </a>


    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger" role="alert">
            <div class="row">
                <div class="col-2">
                    <iconify-icon icon="zondicons:exclamation-solid" width="20" height="20"></iconify-icon>
                </div>
                <div class="col">
                    <strong><?= session()->get('error') ?></strong>
                </div>
            </div>
        </div>
    <?php endif; ?>



    <?= form_open('auth/login', ['method' => 'post']); ?>
    <?= csrf_field(); ?>
    <!-- Username Field -->
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text"
            class="form-control <?= (session()->has('errors') && isset(session()->get('errors')['username'])) ? 'is-invalid' : '' ?>"
            id="username" name="username" value="<?= old('username') ?>" aria-describedby="username" autofocus>
        <?php if (session()->has('errors') && isset(session()->get('errors')['username'])): ?>
            <p class="invalid-feedback"><?= session()->get('errors')['username']; ?></p>
        <?php endif; ?>
    </div>

    <!-- Password Field -->
    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password"
            class="form-control <?= (session()->has('errors') && isset(session()->get('errors')['password'])) ? 'is-invalid' : '' ?>"
            id="password" name="password">
        <?php if (session()->has('errors') && isset(session()->get('errors')['password'])): ?>
            <p class="invalid-feedback"><?= session()->get('errors')['password']; ?></p>
        <?php endif; ?>
    </div>

    <!-- Submit Button -->
    <button class="btn btn-primary w-100 py-8 mb-4 rounded-2" type="submit">Sign In</button>

    <?= form_close(); ?>
</div>
<?= $this->endSection(); ?>
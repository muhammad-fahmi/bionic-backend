<form id="form_add_user">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="add_nama" class="form-label">Nama <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="add_nama" name="nama" required>
        <div class="invalid-feedback" id="error_add_nama"></div>
    </div>

    <div class="mb-3">
        <label for="add_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
        <select class="form-select" id="add_jabatan" name="jabatan" required>
            <option value="" selected disabled>Pilih Jabatan</option>
            <option value="petugas">Petugas</option>
            <option value="verifikator">Verifikator</option>
            <option value="admin">Admin</option>
        </select>
        <div class="invalid-feedback" id="error_add_jabatan"></div>
    </div>

    <div class="mb-3">
        <label for="add_username" class="form-label">Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="add_username" name="username" required>
        <div class="invalid-feedback" id="error_add_username"></div>
    </div>

    <div class="mb-3">
        <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="add_password" name="password" required>
        <small class="text-muted">Minimal 6 karakter</small>
        <div class="invalid-feedback" id="error_add_password"></div>
    </div>

    <div class="mb-3">
        <label for="add_password_confirm" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="add_password_confirm" name="password_confirm" required>
        <div class="invalid-feedback" id="error_add_password_confirm"></div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Tambah User
        </button>
    </div>
</form>

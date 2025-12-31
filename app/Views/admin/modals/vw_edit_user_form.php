<form id="form_edit_user" data-user-id="<?= $user['id'] ?>">
    <?= csrf_field(); ?>

    <div class="mb-3">
        <label for="edit_nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="edit_nama" name="nama"
               value="<?= esc($user['nama']) ?>" required>
        <div class="invalid-feedback" id="error_nama"></div>
    </div>

    <div class="mb-3">
        <label for="edit_jabatan" class="form-label">Jabatan</label>
        <select class="form-select" id="edit_jabatan" name="jabatan" required>
            <option value="petugas" <?= $user['jabatan'] == 'petugas' ? 'selected' : '' ?>>Petugas</option>
            <option value="verifikator" <?= $user['jabatan'] == 'verifikator' ? 'selected' : '' ?>>Verifikator</option>
            <option value="admin" <?= $user['jabatan'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <div class="invalid-feedback" id="error_jabatan"></div>
    </div>

    <div class="mb-3">
        <label for="edit_username" class="form-label">Username</label>
        <input type="text" class="form-control" id="edit_username" name="username"
               value="<?= esc($user['username']) ?>" required>
        <div class="invalid-feedback" id="error_username"></div>
    </div>

    <div class="mb-3">
        <label for="edit_password" class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
        <input type="password" class="form-control" id="edit_password" name="password">
        <div class="invalid-feedback" id="error_password"></div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save"></i> Simpan
        </button>
    </div>
</form>

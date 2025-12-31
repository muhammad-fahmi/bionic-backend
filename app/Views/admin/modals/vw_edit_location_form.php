<form id="form_edit_location" method="post" data-location-id="<?= $location['id'] ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="edit_name" class="form-label">Nama Lokasi</label>
        <input type="text" class="form-control" id="edit_name" name="name" value="<?= esc($location['name']) ?>" placeholder="Masukkan nama lokasi">
        <div id="error_name" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="edit_shift" class="form-label">Shift</label>
        <select class="form-select" id="edit_shift" name="shift">
            <option value="">Pilih Shift</option>
            <option value="1" <?= $location['shift'] == 1 ? 'selected' : '' ?>>Shift 1</option>
            <option value="2" <?= $location['shift'] == 2 ? 'selected' : '' ?>>Shift 2</option>
            <option value="3" <?= $location['shift'] == 3 ? 'selected' : '' ?>>Shift 3</option>
        </select>
        <div id="error_shift" class="invalid-feedback"></div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

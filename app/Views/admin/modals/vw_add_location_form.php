<form id="form_add_location" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="add_name" class="form-label">Nama Lokasi</label>
        <input type="text" class="form-control" id="add_name" name="name" placeholder="Masukkan nama lokasi">
        <div id="error_add_name" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="add_shift" class="form-label">Shift</label>
        <select class="form-select" id="add_shift" name="shift">
            <option value="">Pilih Shift</option>
            <option value="1">Shift 1</option>
            <option value="2">Shift 2</option>
            <option value="3">Shift 3</option>
        </select>
        <div id="error_add_shift" class="invalid-feedback"></div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

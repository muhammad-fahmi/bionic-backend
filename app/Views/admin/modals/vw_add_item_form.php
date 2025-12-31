<form id="form_add_item" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="add_lokasi_id" class="form-label">Lokasi</label>
        <select class="form-select" id="add_lokasi_id" name="lokasi_id">
            <option value="">Additional Task (Tanpa Lokasi)</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?= $location['id'] ?>"><?= $location['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <div id="error_add_lokasi_id" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="add_nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="add_nama" name="nama" placeholder="Masukkan nama">
        <div id="error_add_nama" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="add_type" class="form-label">Type</label>
        <select class="form-select" id="add_type" name="type">
            <option value="">Pilih Type</option>
            <option value="complete_task">Complete Task</option>
            <option value="additional_task">Additional Task</option>
        </select>
        <div id="error_add_type" class="invalid-feedback"></div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
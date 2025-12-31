<form id="form_edit_item" method="post" data-item-id="<?= $item['id'] ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="edit_lokasi_id" class="form-label">Lokasi</label>
        <select class="form-select" id="edit_lokasi_id" name="lokasi_id">
            <option value="" <?= empty($item['lokasi_id']) ? 'selected' : '' ?>>Additional Task (Tanpa Lokasi)</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?= $location['id'] ?>" <?= $item['lokasi_id'] == $location['id'] ? 'selected' : '' ?>>
                    <?= $location['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="error_lokasi_id" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="edit_nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="edit_nama" name="nama" value="<?= esc($item['nama_display']) ?>" placeholder="Masukkan nama">
        <div id="error_nama" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="edit_type" class="form-label">Type</label>
        <select class="form-select" id="edit_type" name="type">
            <option value="">Pilih Type</option>
            <option value="complete_task" <?= $item['type'] === 'complete_task' ? 'selected' : '' ?>>Complete Task</option>
            <option value="additional_task" <?= $item['type'] === 'additional_task' ? 'selected' : '' ?>>Additional Task</option>
        </select>
        <div id="error_type" class="invalid-feedback"></div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

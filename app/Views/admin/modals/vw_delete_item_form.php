<div class="text-center">
    <p>Apakah Anda yakin ingin menghapus item "<strong><?= esc($item['nama_display']) ?></strong>"?</p>
    <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
</div>
<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-danger" onclick="deleteItem(<?= $item['id'] ?>)">Hapus</button>
</div>

<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_item {
        width: 100%;
        text-align: center;
    }

    #table_data_item th {
        font-style: oblique;
        font-size: 18px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen Item
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <div class="d-flex justify-content-start my-3">
        <a href="/admin" class="btn btn-primary text-end d-flex align-items-center">
            <iconify-icon icon="famicons:arrow-back" width="24" height="24"></iconify-icon>
            <span class="ms-1">Back</span>
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <h5 class="mb-0">Manajemen Item</h5>
                <!-- Add Item Button -->
                <button class="btn btn-primary ms-3" onclick="addModal()">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Item
                </button>
            </div>
            <div class="d-flex gap-2">
                <!-- Text Filter -->
                <input type="text" id="search_input" class="form-control" placeholder="Cari item..."
                    style="width: 250px;">
                <select name="filter_location" id="filter_location" class="form-select">
                    <option value="">Pilih Lokasi</option>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?= $location['id'] ?>"><?= $location['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table table-striped text-center align-middle" id="table_data_item">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 0; ?>
                    <?php foreach ($items as $item): ?>
                        <tr data-location-id="<?= $item['lokasi_id'] ?>">
                            <td><?= ++$index; ?></td>
                            <td><?= esc($item['nama_display']) ?></td>
                            <td><?= $item['name'] ?></td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-warning d-flex align-items-center mx-1"
                                        onclick="showModal('<?= $item['id'] ?>')">
                                        <iconify-icon icon="flowbite:edit-outline" width="24" height="24"></iconify-icon>
                                        Edit
                                    </button>
                                    <button class="btn btn-danger d-flex align-items-center mx-1"
                                        onclick="showDeleteModal('<?= $item['id'] ?>')">
                                        <iconify-icon icon="material-symbols:delete-outline-rounded" width="24"
                                            height="24"></iconify-icon>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    function addModal() {
        $.ajax({
            url: '/admin/manage/item/modal',
            type: 'POST',
            data: {
                type: 'add',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'Failed') {
                    alert(response.message);
                    return;
                }
                $('#modalContainer').html(`
                    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addItemModalLabel">${response.title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${response.formHTML}
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $('#addItemModal').modal('show');
                $('#form_add_item').on('submit', function(e) {
                    e.preventDefault();
                    addItem();
                });
            },
            error: function() {
                alert('Error loading modal');
            }
        });
    }

    function addItem() {
        const formData = new FormData(document.getElementById('form_add_item'));
        $.ajax({
            url: '/admin/manage/item/add',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 201) {
                    $('#addItemModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                for (const field in errors) {
                    $('#error_add_' + field).text(errors[field]).show();
                }
            }
        });
    }

    function showModal(id) {
        $.ajax({
            url: '/admin/manage/item/modal',
            type: 'POST',
            data: {
                type: 'edit',
                id: id,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'Failed') {
                    alert(response.message);
                    return;
                }
                $('#modalContainer').html(`
                    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editItemModalLabel">${response.title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${response.formHTML}
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $('#editItemModal').modal('show');
                $('#form_edit_item').on('submit', function(e) {
                    e.preventDefault();
                    updateItem(id);
                });
            },
            error: function() {
                alert('Error loading modal');
            }
        });
    }

    function updateItem(id) {
        const formData = new FormData(document.getElementById('form_edit_item'));
        $.ajax({
            url: '/admin/manage/item/edit/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.status === 200) {
                    $('#editItemModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                for (const field in errors) {
                    $('#error_' + field).text(errors[field]).show();
                }
            }
        });
    }

    function showDeleteModal(id) {
        $.ajax({
            url: '/admin/manage/item/modal',
            type: 'POST',
            data: {
                type: 'delete',
                id: id,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'Failed') {
                    alert(response.message);
                    return;
                }
                $('#modalContainer').html(`
                    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteItemModalLabel">${response.title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${response.formHTML}
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $('#deleteItemModal').modal('show');
            },
            error: function() {
                alert('Error loading modal');
            }
        });
    }

    function deleteItem(id) {
        $.ajax({
            url: '/admin/manage/item/delete/' + id,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                _method: 'DELETE'
            },
            headers: {
                'X-HTTP-Method-Override': 'DELETE'
            },
            success: function(response) {
                if (response.status === 200) {
                    $('#deleteItemModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                try {
                    const json = xhr.responseJSON || JSON.parse(xhr.responseText);
                    if (json && json.message) {
                        alert('Error: ' + json.message);
                        return;
                    }
                } catch (e) {}
                alert('Error deleting item');
            }
        });
    }

    $(document).ready(function() {
        $('#search_input').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#table_data_item tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('#filter_location').on('change', function() {
            const value = $(this).val();
            if (value === '') {
                $('#table_data_item tbody tr').show();
            } else {
                $('#table_data_item tbody tr').filter(function() {
                    return $(this).data('location-id') == value;
                }).show();
                $('#table_data_item tbody tr').not('[data-location-id="' + value + '"]').hide();
            }
        });
    });
</script>
<div id="modalContainer"></div>
<?= $this->endSection() ?>

<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_location {
        width: 100%;
        text-align: center;

        & th {
            font-style: oblique;
            font-size: 18px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen Lokasi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <div class="d-flex justify-content-start my-3">
        <a href="/admin" class="btn btn-primary text-end">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5>Manajemen Lokasi</h5>
            <button class="btn btn-primary ms-3" onclick="showAddModal()">
                <i class="fa-solid fa-plus"></i>
                Tambah Lokasi
            </button>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table table-striped text-center align-middle" id="table_data_location">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lokasi</th>
                        <th>Shift</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 0; ?>
                    <?php foreach ($locations as $location): ?>
                        <tr>
                            <td><?= ++$index; ?></td>
                            <td><?= $location['name'] ?></td>
                            <td>
                                <span class="badge <?= match($location['shift']) {
                                    1 => 'bg-info',
                                    2 => 'bg-success',
                                    3 => 'bg-warning',
                                    default => 'bg-secondary'
                                } ?>">
                                    Shift <?= $location['shift'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-warning d-flex align-items-center mx-1"
                                        onclick="showModal('<?= $location['id'] ?>')">
                                        <iconify-icon icon="flowbite:edit-outline" width="24" height="24"></iconify-icon>
                                        Edit
                                    </button>
                                    <button class="btn btn-danger d-flex align-items-center mx-1"
                                        onclick="confirmDelete('<?= $location['id'] ?>', '<?= esc($location['name']) ?>')">
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
    async function showModal(locationId) {
        try {
            const response = await fetch(`<?= base_url('admin/manage/location/') ?>${locationId}`);
            const result = await response.json();

            if (result.status === 200) {
                $("#bs-example-modal-md #mediumModalLabel").text(`Edit Lokasi: ${result.location.name}`);
                $("#bs-example-modal-md .modal-body").html(result.html);
                $("#bs-example-modal-md").modal('show');

                // Remove previous event handler and attach new one
                $("#form_edit_location").off('submit').on('submit', handleFormSubmit);
            } else {
                alert('Lokasi tidak ditemukan');
            }
        } catch (error) {
            console.error('Error loading location data:', error);
            alert('Terjadi kesalahan saat memuat data lokasi');
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const locationId = form.dataset.locationId;
        const formData = new FormData(form);

        const data = {};
        let csrfToken = '';
        formData.forEach((value, key) => {
            if (key === 'csrf_test_name') {
                csrfToken = value;
            } else {
                data[key] = value;
            }
        });

        console.log('Sending data:', data);
        console.log('Location ID:', locationId);

        try {
            const response = await fetch(`<?= base_url('admin/manage/location/edit/') ?>${locationId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Server response:', result);

            if (result.status === 200) {
                updateTableRow(locationId, result.data);
                $("#bs-example-modal-md").modal('hide');
                alert('Lokasi berhasil diupdate');
            } else if (result.errors) {
                displayErrors(result.errors);
            } else {
                alert(result.message || 'Terjadi kesalahan saat mengupdate lokasi');
            }
        } catch (error) {
            console.error('Error updating location:', error);
            alert('Terjadi kesalahan saat mengupdate lokasi');
        }
    }

    function displayErrors(errors) {
        $('.invalid-feedback').text('').hide();
        $('.form-control, .form-select').removeClass('is-invalid');

        for (const [field, message] of Object.entries(errors)) {
            $(`#error_${field}`).text(message).show();
            $(`#edit_${field}`).addClass('is-invalid');
        }
    }

    function updateTableRow(locationId, data) {
        const row = $(`button[onclick="showModal('${locationId}')"]`).closest('tr');
        row.find('td:eq(1)').text(data.name);

        const shiftClass = data.shift == 1 ? 'bg-info' : data.shift == 2 ? 'bg-success' : 'bg-warning';
        row.find('td:eq(2)').html(`<span class="badge ${shiftClass}">Shift ${data.shift}</span>`);
    }

    $('#bs-example-modal-md').on('hidden.bs.modal', function() {
        $(this).find('.modal-body').html('');
        $(this).find('#mediumModalLabel').text('Medium Modal');
    });

    function confirmDelete(locationId, locationName) {
        if (confirm(`Apakah Anda yakin ingin menghapus lokasi "${locationName}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
            deleteLocation(locationId);
        }
    }

    async function deleteLocation(locationId) {
        // Get fresh CSRF token from any form on the page
        const csrfToken = document.querySelector('input[name="csrf_test_name"]')?.value || '';

        try {
            const response = await fetch(`<?= base_url('admin/manage/location/delete/') ?>${locationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();

            if (result.status === 200) {
                removeTableRow(locationId);
                alert(result.message);
            } else {
                alert(result.message || 'Gagal menghapus lokasi');
            }
        } catch (error) {
            console.error('Error deleting location:', error);
            alert('Terjadi kesalahan saat menghapus lokasi');
        }
    }

    function removeTableRow(locationId) {
        const row = $(`button[onclick*="showModal('${locationId}')"]`).closest('tr');
        row.fadeOut(300, function() {
            $(this).remove();
            updateRowNumbers();
        });
    }

    function updateRowNumbers() {
        $('#table_data_location tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function showAddModal() {
        const formHtml = `<?= view('admin/modals/vw_add_location_form') ?>`;
        $("#bs-example-modal-lg #myLargeModalLabel").text('Tambah Lokasi Baru');
        $("#bs-example-modal-lg .modal-body").html(formHtml);
        $("#bs-example-modal-lg").modal('show');

        // Remove previous event handler and attach new one
        $("#form_add_location").off('submit').on('submit', handleAddFormSubmit);
    }

    async function handleAddFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const data = {};
        let csrfToken = '';
        formData.forEach((value, key) => {
            if (key === 'csrf_test_name') {
                csrfToken = value;
            } else {
                data[key] = value;
            }
        });

        try {
            const response = await fetch('<?= base_url('admin/manage/location/add') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 201) {
                addTableRow(result.data);
                $("#bs-example-modal-lg").modal('hide');
                alert(result.message);
                form.reset();
            } else {
                displayAddErrors(result.errors);
            }
        } catch (error) {
            console.error('Error adding location:', error);
            alert('Terjadi kesalahan saat menambahkan lokasi');
        }
    }

    function displayAddErrors(errors) {
        $('.invalid-feedback').text('').hide();
        $('.form-control, .form-select').removeClass('is-invalid');

        for (const [field, message] of Object.entries(errors)) {
            $(`#error_add_${field}`).text(message).show();
            $(`#add_${field}`).addClass('is-invalid');
        }
    }

    function addTableRow(location) {
        const rowCount = $('#table_data_location tbody tr').length + 1;
        const shiftClass = location.shift == 1 ? 'bg-info' : location.shift == 2 ? 'bg-success' : 'bg-warning';

        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>${escapeHtml(location.name)}</td>
                <td><span class="badge ${shiftClass}">Shift ${location.shift}</span></td>
                <td>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-warning d-flex align-items-center mx-1"
                            onclick="showModal('${location.id}')">
                            <iconify-icon icon="flowbite:edit-outline" width="24" height="24"></iconify-icon>
                            Edit
                        </button>
                        <button class="btn btn-danger d-flex align-items-center mx-1"
                            onclick="confirmDelete('${location.id}', '${escapeHtml(location.name)}')">
                            <iconify-icon icon="material-symbols:delete-outline-rounded" width="24"
                                height="24"></iconify-icon>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `;

        $('#table_data_location tbody').append(newRow);
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    $('#bs-example-modal-lg').on('hidden.bs.modal', function() {
        $(this).find('.modal-body').html('');
        $(this).find('#myLargeModalLabel').text('Large Modal');
    });
</script>
<?= $this->endSection() ?>

<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_user {
        width: 100%;

        & th {
            font-size: 18px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen User
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container p-3">
    <div class="d-flex justify-content-start my-3">
        <a href="/admin" class="btn btn-primary text-end d-flex align-items-center">
            <iconify-icon icon="formkit:arrowleft" width="20" height="20"></iconify-icon>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5>Manajemen User</h5>
            <button class="btn btn-primary ms-3 d-flex align-items-center" onclick="showAddModal()">
                <iconify-icon icon="mdi:user-add" class="me-2" width="24" height="24"></iconify-icon>
                Add User
            </button>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table table-striped text-center align-middle" id="table_data_user">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 0; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= ++$index; ?></td>
                            <td><?= $user['nama'] ?></td>
                            <td><?= $user['jabatan'] ?></td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-warning d-flex align-items-center mx-1"
                                        onclick="showEditModal('<?= $user['id'] ?>')">
                                        <iconify-icon icon="flowbite:edit-outline" width="24" height="24"></iconify-icon>
                                        Edit
                                    </button>
                                    <button class="btn btn-danger d-flex align-items-center mx-1"
                                        onclick="confirmDelete('<?= $user['id'] ?>', '<?= esc($user['nama']) ?>')">
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
    // Add
    function showAddModal() {
        // Load the add user form
        const formHtml = `<?= view('admin/modals/vw_add_user_form') ?>`;

        // Update modal title
        $("#bs-example-modal-lg #large_modal_title").text('Tambah User Baru');

        // Inject form HTML into modal body
        $("#bs-example-modal-lg #large_modal_body").html(formHtml);

        // Show modal
        $("#bs-example-modal-lg").modal('show');

        // Attach form submit handler
        $("#form_add_user").on('submit', handleAddFormSubmit);
    }

    async function handleAddFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            // Exclude CSRF token
            if (key !== 'csrf_test_name') {
                data[key] = value;
            }
        });

        try {
            const response = await fetch('<?= base_url('admin/manage/user/add') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash(); ?>'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 201) {
                // Success - add new row to table
                addTableRow(result.data);

                // Hide modal
                $("#bs-example-modal-lg").modal('hide');

                // Show success message
                Swal.fire({
                    title: "Good job!",
                    text: "User Berhasil Ditambahkan",
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });

                // Reset form
                form.reset();
            } else {
                // Validation errors
                displayAddErrors(result.errors);
            }
        } catch (error) {
            // console.error('Error adding user:', error);
            Swal.fire({
                title: "Failed!",
                text: "Terjadi kesalahan saat menambahkan user",
                icon: "error"
            })
            // alert('Terjadi kesalahan saat menambahkan user');
        }
    }

    function displayAddErrors(errors) {
        // Clear previous errors
        $('.invalid-feedback').text('').hide();
        $('.form-control, .form-select').removeClass('is-invalid');

        // Display new errors
        for (const [field, message] of Object.entries(errors)) {
            $(`#error_add_${field}`).text(message).show();
            $(`#add_${field}`).addClass('is-invalid');
        }
    }

    function addTableRow(user) {
        // Get current row count
        const rowCount = $('#table_data_user tbody tr').length + 1;

        // Create new row HTML
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>${escapeHtml(user.nama)}</td>
                <td>${escapeHtml(user.jabatan)}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-warning d-flex align-items-center mx-1"
                            onclick="showModal('${user.id}')">
                            <iconify-icon icon="flowbite:edit-outline" width="24" height="24"></iconify-icon>
                            Edit
                        </button>
                        <button class="btn btn-danger d-flex align-items-center mx-1"
                            onclick="confirmDelete('${user.id}', '${escapeHtml(user.nama)}')">
                            <iconify-icon icon="material-symbols:delete-outline-rounded" width="24"
                                height="24"></iconify-icon>
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `;

        // Append to table
        $('#table_data_user tbody').append(newRow);
    }

    // Edit/Update
    async function showEditModal(userId) {
        try {
            // Fetch user data and form HTML
            const response = await fetch(`<?= base_url('admin/manage/user/') ?>${userId}`);
            const result = await response.json();

            if (result.status === 200) {
                // Update modal title
                $("#bs-example-modal-md #medium_modal_title").text(`Edit User: ${result.user.nama}`);

                // Inject form HTML into modal body
                $("#bs-example-modal-md #medium_modal_body").html(result.html);

                // Show modal
                $("#bs-example-modal-md").modal('show');

                // Attach form submit handler
                $("#form_edit_user").on('submit', handleFormSubmit);
            } else {
                Swal.fire({
                    title: "Error!",
                    text: "User tidak ditemukan",
                    icon: "error"
                });
                // alert('User tidak ditemukan');
            }
        } catch (error) {
            // console.error('Error loading user data:', error);
            Swal.fire({
                title: "Error!",
                text: "Terjadi kesalahan saat memuat data user",
                icon: "error"
            });
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const userId = form.dataset.userId;
        const formData = new FormData(form);

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            if (key !== 'csrf_test_name') { // Exclude CSRF token
                data[key] = value;
            }
        });

        try {
            const response = await fetch(`<?= base_url('admin/manage/user/edit/') ?>${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash(); ?>'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 200) {
                // Success - update table row
                updateTableRow(userId, result.data);

                // Hide modal
                $("#bs-example-modal-md").modal('hide');

                // Show success message
                Swal.fire({
                    title: "Good job!",
                    text: "User berhasil diupdate",
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                // Validation errors
                displayErrors(result.errors);
            }
        } catch (error) {
            console.error('Error updating user:', error);
            Swal.fire({
                title: "Error!",
                text: "Terjadi kesalahan saat mengupdate user",
                icon: "error"
            });
        }
    }

    function displayErrors(errors) {
        // Clear previous errors
        $('.invalid-feedback').text('').hide();
        $('.form-control, .form-select').removeClass('is-invalid');

        // Display new errors
        for (const [field, message] of Object.entries(errors)) {
            $(`#error_${field}`).text(message).show();
            $(`#edit_${field}`).addClass('is-invalid');
        }
    }

    function updateTableRow(userId, data) {
        // Find and update the table row
        const row = $(`button[onclick="showModal('${userId}')"]`).closest('tr');

        // Update nama cell
        row.find('td:eq(1)').text(data.nama);

        // Update jabatan cell
        row.find('td:eq(2)').text(data.jabatan);
    }

    // Delete
    function confirmDelete(userId, userName) {
        // Show confirmation dialog
        Swal.fire({
            title: "Kamu Yakin?",
            text: "Aksi yang anda lakukan tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batalkan!",
        }).then((result) => {
            if (result.isConfirmed) {
                deleteUser(userId);
            }
        });
        // if (confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        //     deleteUser(userId);
        // }
    }

    async function deleteUser(userId) {
        try {
            const response = await fetch(`<?= base_url('admin/manage/user/delete/') ?>${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash(); ?>'
                }
            });

            const result = await response.json();

            if (result.status === 200) {
                // Success - remove table row
                removeTableRow(userId);

                Swal.fire({
                    title: "Dihapus!",
                    text: result.message,
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                // Error occurred
                alert(result.message || 'Gagal menghapus user');
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            alert('Terjadi kesalahan saat menghapus user');
        }
    }

    function removeTableRow(userId) {
        // Find and remove the table row with animation
        const row = $(`button[onclick*="showModal('${userId}')"]`).closest('tr');
        row.fadeOut(300, function () {
            $(this).remove();

            // Update row numbers
            updateRowNumbers();
        });
    }

    function updateRowNumbers() {
        // Update the # column after deletion
        $('#table_data_user tbody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
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

    // Clear modal MD content when closed
    $('#bs-example-modal-md').on('hidden.bs.modal', function () {
        $(this).find('.modal-body').html('');
        $(this).find('#mediumModalLabel').text('Medium Modal');
    });

    // Clear add modal content when closed
    $('#bs-example-modal-lg').on('hidden.bs.modal', function () {
        $(this).find('.modal-body').html('');
        $(this).find('#myLargeModalLabel').text('Large Modal');
    });
</script>
<?= $this->endSection() ?>
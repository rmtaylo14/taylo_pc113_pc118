<?php 
include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        const role = localStorage.getItem("role");
        if (!role || role !== "admin") {
            window.location.href = "unauthorized.php";
        }
    </script>
    <meta charset="UTF-8" />
    <title>User Management</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles/users.css?v=1.0.2" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <style>
        /* Add custom styling here if needed */
    </style>
</head>
<body>
<div style="width: 100%; max-width: 1200px; margin: 0; padding: 10px;">
    <div class="header-bar" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Users</h3>
        <div>
            <a href="#" id="openAddModal" class="user-button">Add User</a>
            <button id="importBtn" class="user-button" style="background-color: #4CAF50; border: none;">Import</button>
            <button id="exportBtn" class="user-button" style="background-color: #2196F3; border: none;">Export</button>
        </div>
    </div>

    <table id="usersTable" class="display nowrap" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone Number</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    const token = localStorage.getItem("token");
    if (!token) {
        window.location.href = "login.php";
        return;
    }

    let table;

    function loadUsers() {
        $.ajax({
            url: "http://127.0.0.1:8000/api/users/index",
            method: "GET",
            headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
            success: function (users) {
                if (table) table.destroy();
                table = $('#usersTable').DataTable({
                    data: users,
                    scrollX: true,
                    columns: [
                        { data: "id" },
                        { data: "firstname" },
                        { data: "lastname" },
                        { data: "email" },
                        { data: "address" },
                        { data: "phone_number" },
                        { data: "role" },
                        {
                            data: null,
                            render: function (data, type, row) {
                                return `
                                    <button class="edit-btn" data-id="${row.id}">Edit</button>
                                    <button class="delete-btn" data-id="${row.id}">Delete</button>
                                `;
                            }
                        }
                    ]
                });
            }
        });
    }

    loadUsers();

    handleAddUser(token, loadUsers);
    handleEditUser(token, loadUsers);
    handleDeleteUser(token, loadUsers);

// Import button handler
$('#importBtn').on('click', function () {
    Swal.fire({
        title: 'Import Users',
        html: `<input type="file" id="importFile" accept=".csv,.xlsx,.xls" class="swal2-input">`,
        confirmButtonText: 'Upload',
        showCancelButton: true,
        preConfirm: () => {
            const fileInput = document.getElementById('importFile');
            if (!fileInput.files[0]) {
                Swal.showValidationMessage('Please select a file to import.');
                return false;
            }
            const file = fileInput.files[0];

            const formData = new FormData();
            formData.append('file', file);

            return fetch('http://127.0.0.1:8000/api/users/import', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`  // Ensure `token` is defined elsewhere
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    Swal.showValidationMessage('Import completed with errors: ' + data.errors.join(', '));
                    return false;
                } else {
                    Swal.fire('Success', 'Users imported successfully.', 'success');
                    loadUsers();  // Reload the user table
                    return true;
                }
            })
            .catch(err => {
                Swal.showValidationMessage('Import failed: ' + err);
                return false;
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Success message already handled inside preConfirm
        }
    });
});


    // Export button handler
    $('#exportBtn').on('click', function () {
        const token = localStorage.getItem("token");

        fetch('http://127.0.0.1:8000/api/users/export', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'text/csv'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'usersexport.xlsx'; 
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error exporting users:', error);
            Swal.fire('Error', 'Failed to export user list.', 'error');
        });
    });

});

function handleAddUser(token, reloadCallback) {
    $('#openAddModal').on('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Add New User',
            html: `
                <input id="swalFirstName" class="swal2-input" placeholder="First Name" required>
                <input id="swalLastName" class="swal2-input" placeholder="Last Name" required>
                <input id="swalEmail" class="swal2-input" type="email" placeholder="Email" required>
                <input id="swalAddress" class="swal2-input" placeholder="Address">
                <input id="swalPhoneNumber" class="swal2-input" placeholder="Phone Number">
                <select id="swalRole" class="swal2-select" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user">User</option>
                </select>
                <input id="swalPassword" class="swal2-input" type="password" placeholder="Password" required>
            `,
            focusConfirm: false,
            preConfirm: () => {
                const firstName = $('#swalFirstName').val();
                const lastName = $('#swalLastName').val();
                const email = $('#swalEmail').val();
                const address = $('#swalAddress').val();
                const phone = $('#swalPhoneNumber').val();
                const role = $('#swalRole').val();
                const password = $('#swalPassword').val();

                if (!firstName || !lastName || !email || !role || !password) {
                    Swal.showValidationMessage('Please fill all required fields');
                    return false;
                }

                return { firstName, lastName, email, address, phone, role, password };
            },
            showCancelButton: true,
            confirmButtonText: 'Add User',
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: "http://127.0.0.1:8000/api/users/store/user",
                    type: "POST",
                    headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
                    data: {
                        firstname: result.value.firstName,
                        lastname: result.value.lastName,
                        email: result.value.email,
                        address: result.value.address,
                        phone_number: result.value.phone,
                        role: result.value.role,
                        password: result.value.password
                    },
                    success: function () {
                        Swal.fire('User Added', 'The user has been added successfully.', 'success');
                        reloadCallback();
                    },  
                    error: function (xhr) {
                        let errorMsg = "Failed to add user.";
                        if (xhr.responseJSON?.message) {
                            errorMsg += " " + xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }
        });
    });
}

function handleEditUser(token, reloadCallback) {
    $('#usersTable').on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `http://127.0.0.1:8000/api/users/find`,
            method: "POST",
            headers: { Authorization: `Bearer ${token}` },
            data: { id: id },
            success: function (response) {
                const user = response.user;

                Swal.fire({
                    title: 'Edit User',
                    html: `
                        <input id="editSwalFirstName" class="swal2-input" placeholder="First Name" value="${user.firstname}">
                        <input id="editSwalLastName" class="swal2-input" placeholder="Last Name" value="${user.lastname}">
                        <input id="editSwalEmail" class="swal2-input" placeholder="Email" value="${user.email}">
                        <input id="editSwalAddress" class="swal2-input" placeholder="Address" value="${user.address}">
                        <input id="editSwalPhoneNumber" class="swal2-input" placeholder="Phone Number" value="${user.phone_number}">
                        <select id="editSwalRole" class="swal2-select">
                            <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                            <option value="manager" ${user.role === 'manager' ? 'selected' : ''}>Manager</option>
                            <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                        </select>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Update User',
                    preConfirm: () => {
                        const firstname = $('#editSwalFirstName').val();
                        const lastname = $('#editSwalLastName').val();
                        const email = $('#editSwalEmail').val();
                        const address = $('#editSwalAddress').val();
                        const phone_number = $('#editSwalPhoneNumber').val();
                        const role = $('#editSwalRole').val();

                        if (!firstname || !lastname || !email || !role) {
                            Swal.showValidationMessage('Please fill all required fields.');
                            return false;
                        }

                        return {
                            id,
                            firstname,
                            lastname,
                            email,
                            address,
                            phone_number,
                            role
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: `http://127.0.0.1:8000/api/users/update`,
                            type: "POST",
                            headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
                            data: result.value,
                            success: function () {
                                Swal.fire('User Updated', 'User information has been successfully updated.', 'success');
                                reloadCallback();
                            },
                            error: function (xhr) {
                                let errMsg = "Failed to update user.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errMsg += " " + xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errMsg, 'error');
                            }
                        });
                    }
                });
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch user data.', 'error');
            }
        });
    });
}

function handleDeleteUser(token, reloadCallback) {
    $('#usersTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `http://127.0.0.1:8000/api/users/${id}`,
                    type: "DELETE",
                    headers: { Authorization: `Bearer ${token}` },
                    success: function () {
                        Swal.fire('Deleted!', 'User has been deleted.', 'success');
                        reloadCallback();
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to delete user.', 'error');
                    }
                });
            }
        });
    });
}
</script>
</body>
</html>

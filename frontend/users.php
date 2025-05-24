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
    <div class="header-bar">
        <h3>Users</h3>
        <a href="#" id="openAddModal" class="user-button">Add User</a>
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

<!-- Add User Modal -->
<script>
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
            const token = localStorage.getItem("token");
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
                    Swal.fire({
                        icon: 'success',
                        title: 'User Added',
                        text: 'The user has been added successfully.'
                    });
                    loadUsers();
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
</script>


<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <h2>Edit User</h2>
        <form id="editUserForm">
            <input type="text" id="editFirstName" placeholder="First Name" />
            <input type="text" id="editLastName" placeholder="Last Name" />
            <input type="email" id="editEmail" placeholder="Email" />
            <input type="text" id="editAddress" placeholder="Address" />
            <input type="text" id="editPhoneNumber" placeholder="Phone Number" />
            <select id="editRole">
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="user">User</option>
            </select>
            <button type="button" id="saveUserChanges">Update User</button>
        </form>
    </div>
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

    // Trigger Add Modal
    $('#openAddModal').on('click', function (e) {
        e.preventDefault();
        $('#addUserModal').css('display', 'flex');
    });

    // Close Modals
    $(window).on('click', function (e) {
        if ($(e.target).is('#addUserModal')) $('#addUserModal').hide();
        if ($(e.target).is('#editUserModal')) $('#editUserModal').hide();
    });

    // Load separate handlers
    handleAddUser(token, loadUsers);
    handleEditUser(token, loadUsers);
    handleDeleteUser(token, loadUsers);
});
</script>
<script>
function handleAddUser(token, reloadCallback) {
    $('#addUserBtn').on('click', function () {
        $.ajax({
            url: "http://127.0.0.1:8000/api/users",
            type: "POST",
            headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
            data: {
                firstname: $('#newFirstName').val(),
                lastname: $('#newLastName').val(),
                email: $('#newEmail').val(),
                address: $('#newAddress').val(),
                phone_number: $('#newPhoneNumber').val(),
                role: $('#newRole').val(),
                password: $('#newPassword').val(),
            },
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'User Added',
                    text: 'The user has been added successfully.',
                });
                $('#addUserModal').hide();
                $('#addUserForm')[0].reset();
                reloadCallback();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Add Failed',
                    text: 'Failed to add user. Please check the form and try again.',
                });
            }
        });
    });
}
</script>

<script>
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
                        const data = result.value;

                        $.ajax({
                            url: `http://127.0.0.1:8000/api/users/update`,
                            type: "POST",
                            headers: {
                                Authorization: `Bearer ${token}`,
                                Accept: "application/json"
                            },
                            data: data,
                            success: function () {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'User Updated',
                                    text: 'User information has been successfully updated.',
                                });
                                reloadCallback();
                            },
                            error: function (xhr) {
                                let errMsg = "Failed to update user.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errMsg += " " + xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Update Failed',
                                    text: errMsg,
                                });
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
</script>

<script>
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

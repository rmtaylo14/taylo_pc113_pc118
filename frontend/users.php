<?php 
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>User Management</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; }
        h1 { text-align: center; margin-bottom: 20px; }
        table.dataTable thead { background-color: #333; color: white; }
        table.dataTable tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table.dataTable tbody tr:hover { background-color: #f1f1f1; }
        .dt-buttons { margin-bottom: 10px; }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        .user-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .user-button:hover { background-color: #45a049; }

        #editUserModal, #addUserModal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;
        }
        .modal-content {
            background: white; padding: 20px; border-radius: 15px; width: 400px;
        }
        .modal-content input {
            width: 100%; padding: 10px; margin: 10px 0;
        }
        .modal-content button {
            width: 100%; padding: 10px; background: #333; color: white; border: none; cursor: pointer;
        }
    </style>
</head>
<body>
<div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 50px;">
    <div class="header-bar">
        <h3>Users</h3>
        <a href="#" id="openAddModal" class="user-button">Add User</a>
    </div>

    <table id="usersTable" class="display nowrap" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addUserModal">
    <div class="modal-content">
        <h2>Add User</h2>
        <input type="text" id="newUserName" placeholder="Name" />
        <input type="email" id="newUserEmail" placeholder="Email" />
        <input type="text" id="newUserRole" placeholder="Role" />
        <button id="addUserBtn">Add User</button>
    </div>
</div>

<!-- Edit Modal -->
<div id="editUserModal">
    <div class="modal-content">
        <h2>Edit User</h2>
        <input type="text" id="editUserName" placeholder="Name" />
        <input type="email" id="editUserEmail" placeholder="Email" />
        <input type="text" id="editUserRole" placeholder="Role" />
        <button id="saveUserChanges">Update User</button>
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

    var table = $('#usersTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ajax: {
            url: "http://127.0.0.1:8000/api/users",
            type: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json"
            },
            dataSrc: "users"
        },
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "email" },
            { data: "role" },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <button class="edit-btn" data-id="${row.id}">📝</button>
                        <button class="delete-btn" data-id="${row.id}">❌</button>
                    `;
                }
            }
        ]
    });

    $('#openAddModal').on('click', function (e) {
        e.preventDefault();
        $('#addUserModal').css('display', 'flex');
    });

    $('#addUserBtn').on('click', function () {
        $.ajax({
            url: "http://127.0.0.1:8000/api/users",
            type: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json"
            },
            data: {
                name: $('#newUserName').val(),
                email: $('#newUserEmail').val(),
                role: $('#newUserRole').val()
            },
            success: function () {
                alert("User added successfully");
                $('#addUserModal').hide();
                $('#newUserName, #newUserEmail, #newUserRole').val('');
                table.ajax.reload();
            },
            error: function () {
                alert("Failed to add user.");
            }
        });
    });

    $('#usersTable').on('click', '.edit-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: `http://127.0.0.1:8000/api/users/${id}`,
            type: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json"
            },
            success: function (data) {
                $('#editUserName').val(data.name);
                $('#editUserEmail').val(data.email);
                $('#editUserRole').val(data.role);
                $('#editUserModal').data('id', id).css('display', 'flex');
            },
            error: function () {
                alert("Failed to fetch user data.");
            }
        });
    });

    $('#saveUserChanges').on('click', function () {
        var id = $('#editUserModal').data('id');
        $.ajax({
            url: `http://127.0.0.1:8000/api/users/${id}`,
            type: "PUT",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                name: $('#editUserName').val(),
                email: $('#editUserEmail').val(),
                role: $('#editUserRole').val()
            }),
            success: function () {
                alert("User updated successfully");
                $('#editUserModal').hide();
                table.ajax.reload();
            },
            error: function () {
                alert("Failed to update user.");
            }
        });
    });

    $('#usersTable').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/users/${id}`,
                type: "DELETE",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json"
                },
                success: function () {
                    alert("User deleted successfully");
                    table.ajax.reload();
                },
                error: function () {
                    alert("Failed to delete user.");
                }
            });
        }
    });

    $(window).on('click', function (e) {
        if ($(e.target).is('#addUserModal')) $('#addUserModal').hide();
        if ($(e.target).is('#editUserModal')) $('#editUserModal').hide();
    });
});

</script>
</body>
</html>


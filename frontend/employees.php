<?php 
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 0px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table.dataTable thead { background-color: #333; color: white; }
        table.dataTable tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table.dataTable tbody tr:hover { background-color: #f1f1f1; }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-button:hover { background-color: #575757; }
        .dt-buttons { margin-bottom: 10px; }
        #editEmployeeModal, #addEmployeeModal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;
        }
        .modal-content {
            background-color: white; padding: 20px; border-radius: 15px; width: 400px;
        }
        .modal-content input {
            width: 100%; padding: 10px; margin: 10px 0;
        }
        .modal-content button {
            width: 100%; padding: 10px; background-color: #333; color: white; border: none; cursor: pointer;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .user-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .user-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 50px;">
    <div class="header-bar">
        <h3>Employees</h3>
    <a href="#" id="openAddModal" class="user-button">Add Employee</a>
</div>

        <div class="dt-buttons"></div>
        <table id="employeesTable" class="display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editEmployeeModal">
        <div class="modal-content">
            <h2>Edit Employee</h2>
            <input type="text" id="editEmployeeName" placeholder="Employee Name"  />
            <input type="email" id="editEmployeeEmail" placeholder="Email" />
            <input type="text" id="editEmployeePosition" placeholder="Position" />
            <input type="number" id="editEmployeeSalary" placeholder="Salary" />
            <button id="saveChanges">Update Employee</button>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal">
        <div class="modal-content">
            <h2>Add Employee</h2>
            <input type="text" id="newEmployeeName" placeholder="Employee Name" />
            <input type="email" id="newEmployeeEmail" placeholder="Email" />
            <input type="text" id="newEmployeePosition" placeholder="Position" />
            <input type="number" id="newEmployeeSalary" placeholder="Salary" />
            <button id="addEmployeeBtn">Add Employee</button>
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script>
    $(document).ready(function () {
        var table = $('#employeesTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ajax: {
                url: "http://127.0.0.1:8000/api/employees",
                type: "GET",
                dataSrc: ""
            },
            columns: [
                { data: "id" },
                { data: "name" },
                { data: "email" },
                { data: "position" },
                { data: "salary", render: $.fn.dataTable.render.number(',', '.', 2, '₱') },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="edit-btn" data-id="${row.id}">📝</button>
                            <button class="delete-btn" data-id="${row.id}">❌</button>
                        `;
                    }
                }
            ]
        });

        $('#openAddModal').on('click', function(e) {
            e.preventDefault();
            $('#addEmployeeModal').css('display', 'flex');
        });

        $('#addEmployeeBtn').on('click', function() {
            $.ajax({
                url: "http://127.0.0.1:8000/api/employees",
                type: "POST",
                data: {
                    name: $('#newEmployeeName').val(),
                    email: $('#newEmployeeEmail').val(),
                    position: $('#newEmployeePosition').val(),
                    salary: $('#newEmployeeSalary').val()
                },
                success: function () {
                    alert("Employee added successfully");
                    $('#addEmployeeModal').hide();
                    $('#newEmployeeName, #newEmployeeEmail, #newEmployeePosition, #newEmployeeSalary').val('');
                    table.ajax.reload();
                },
                error: function () {
                    alert("Failed to add employee.");
                }
            });
        });

        $('#employeesTable').on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            $.get(`http://127.0.0.1:8000/api/employees/${id}`, function(data) {
                $('#editEmployeeName').val(data.name);
                $('#editEmployeeEmail').val(data.email);
                $('#editEmployeePosition').val(data.position);
                $('#editEmployeeSalary').val(data.salary);
                $('#editEmployeeModal').data('id', id).css('display', 'flex');
            });
        });

        $('#saveChanges').on('click', function () {
            var id = $('#editEmployeeModal').data('id');
            $.ajax({
                url: `http://127.0.0.1:8000/api/employees/${id}`,
                type: "PUT",
                contentType: "application/json",
                data: JSON.stringify({
                    name: $('#editEmployeeName').val(),
                    email: $('#editEmployeeEmail').val(),
                    position: $('#editEmployeePosition').val(),
                    salary: $('#editEmployeeSalary').val()
                }),
                success: function () {
                    alert("Employee updated successfully");
                    $('#editEmployeeModal').hide();
                    table.ajax.reload();
                },
                error: function () {
                    alert("Failed to update employee.");
                }
            });
        });

        $('#employeesTable').on('click', '.delete-btn', function () {
            var id = $(this).data('id');
            if (confirm("Are you sure you want to delete this employee?")) {
                $.ajax({
                    url: `http://127.0.0.1:8000/api/employees/${id}`,
                    type: "DELETE",
                    success: function () {
                        alert("Employee deleted successfully");
                        table.ajax.reload();
                    },
                    error: function () {
                        alert("Failed to delete employee.");
                    }
                });
            }
        });

        $(window).on('click', function(e) {
            if ($(e.target).is('#addEmployeeModal')) $('#addEmployeeModal').hide();
            if ($(e.target).is('#editEmployeeModal')) $('#editEmployeeModal').hide();
        });
    });
</script>

</body>
</html>
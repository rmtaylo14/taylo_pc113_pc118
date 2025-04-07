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
        .dt-buttons { margin-bottom: 10px; } /* Align buttons with the table */
        /* Modal Styles */
        #editEmployeeModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background-color: white; padding: 20px; border-radius: 5px; width: 300px; }
        .modal-content input { width: 100%; padding: 10px; margin: 10px 0; }
        .modal-content button { width: 100%; padding: 10px; background-color: #333; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 50px;">
        <h3>Employees</h3>
        <a href="#" id="openAddModal" class="user-button" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; display: inline-block; vertical-align: middle;">
            Add User
        </a>

        <div class="dt-buttons"></div>
        <table id="employeesTable" class="display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th id="employee-id" name="employee-id">ID</th>
                    <th id="employee-name" name="employee-name">Name</th>
                    <th id="employee-position" name="employee-position">Position</th>
                    <th id="employee-salary" name="employee-salary">Salary</th>
                    <th>Actions</th> <!-- Added Actions column for Edit and Delete -->
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editEmployeeModal">
        <div class="modal-content">
            <h2>Edit Employee</h2>
            <input type="text" id="editEmployeeName" placeholder="Employee Name" />
            <input type="text" id="editEmployeePosition" placeholder="Position" />
            <input type="number" id="editEmployeeSalary" placeholder="Salary" />
            <button id="saveChanges">Save Changes</button>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
        <div class="modal-content">
            <h2>Add Employee</h2>
            <input type="text" id="newEmployeeName" placeholder="Employee Name" />
            <input type="number" id="newEmployeeSalary" placeholder="Salary" />
            <button id="addEmployeeBtn">Add Employee</button>
            <input type="text" id="newEmployeePosition" placeholder="Position" />
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script>
$(document).ready(function() {
    var table = $('#employeesTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        "ajax": {
            "url": "http://127.0.0.1:8000/api/employees",
            "type": "GET",
            "dataSrc": "employees"
        },
        "columns": [
            { "data": "id", "name": "employee-id" },
            { "data": "name", "name": "employee-name" },
            { "data": "position", "name": "employee-position" },
            { "data": "salary", "name": "employee-salary", "render": $.fn.dataTable.render.number(',', '.', 2, '$') },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button class="edit-btn" data-id="${row.id}" title="Edit">📝</button>
                        <button class="delete-btn" data-id="${row.id}" title="Delete">❌</button>
                    `;
                }
            }
        ]
    });

    // Show Add Modal
    $('#openAddModal').on('click', function(e) {
        e.preventDefault();
        $('#addEmployeeModal').show();
    });

    // Add Employee Submit
    $('#addEmployeeBtn').on('click', function() {
        var name = $('#newEmployeeName').val();
        var position = $('#newEmployeePosition').val();
        var salary = $('#newEmployeeSalary').val();

        $.ajax({
            url: "http://127.0.0.1:8000/api/employees",
            type: "POST",
            data: {
                name: name,
                position: position,
                salary: salary
            },
            success: function(response) {
                alert("Employee added successfully");
                $('#addEmployeeModal').hide();
                $('#employeesTable').DataTable().ajax.reload();
                $('#newEmployeeName').val('');
                $('#newEmployeePosition').val('');
                $('#newEmployeeSalary').val('');
            },
            error: function(error) {
                alert("Error adding employee.");
            }
        });
    });

    // Hide modals when clicking outside of content
    $(window).on('click', function(e) {
        if ($(e.target).is('#addEmployeeModal')) {
            $('#addEmployeeModal').hide();
        }
        if ($(e.target).is('#editEmployeeModal')) {
            $('#editEmployeeModal').hide();
        }
    });

    // Edit Button Handler
    $('#employeesTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        loadEmployeeData(id);
    });

    // Delete Button Handler
    $('#employeesTable').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if (confirm("Are you sure you want to delete this employee?")) {
            deleteEmployee(id);
        }
    });

    // Save changes on modal
    $('#saveChanges').on('click', function() {
        var id = $('#editEmployeeModal').data('id');
        var name = $('#editEmployeeName').val();
        var position = $('#editEmployeePosition').val();
        var salary = $('#editEmployeeSalary').val();
        updateEmployee(id, name, position, salary);
    });
});


        // Fetch Employee data for Edit
        function loadEmployeeData(id) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/employees/${id}`,
                type: 'GET',
                success: function(data) {
                    $('#editEmployeeName').val(data.name);
                    $('#editEmployeePosition').val(data.position);
                    $('#editEmployeeSalary').val(data.salary);
                    $('#editEmployeeModal').data('id', id).show();  // Show the modal and set the ID
                },
                error: function(error) {
                    alert("Error fetching employee data.");
                }
            });
        }

        // Update Employee
        function updateEmployee(id, name, position, salary) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/employees/${id}`,
                type: 'PUT',
                data: {
                    name: name,
                    position: position,
                    salary: salary
                },
                success: function(response) {
                    alert('Employee updated successfully');
                    $('#employeesTable').DataTable().ajax.reload();  // Reload table data
                    $('#editEmployeeModal').hide();  // Close the modal
                },
                error: function(error) {
                    alert('Error updating employee');
                }
            });
        }

        // Delete Employee
        function deleteEmployee(id) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/employees/${id}`,
                type: 'DELETE',
                success: function(response) {
                    alert('Employee deleted successfully');
                    $('#employeesTable').DataTable().ajax.reload();  // Reload table data
                },
                error: function(error) {
                    alert('Error deleting employee');
                }
            });
        }
    </script>
</body>
</html>

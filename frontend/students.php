<?php 
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student Management</title>
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

        #editStudentModal, #addStudentModal {
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
        <h3>Students</h3>
        <a href="#" id="openAddModal" class="user-button">Add Student</a>
        <input type="file" id="importCsv" accept=".csv" style="display:none;">
        <button id="importButton" class="user-button">Import CSV</button>
    </div>

    <table id="studentsTable" class="display nowrap" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Age</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modals omitted for brevity; unchanged -->

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

<script>
$(document).ready(function () {
    var table = $('#studentsTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ajax: {
            url: "http://127.0.0.1:8000/api/students",
            type: "GET",
            dataSrc: ""
        },
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "email" },
            { data: "age" },
            { data: "course" },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="edit-btn" data-id="${row.id}">📝</button>
                        <button class="delete-btn" data-id="${row.id}">❌</button>
                    `;
                }
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'csvHtml5',
            'excelHtml5',
            'print'
        ]
    });

    // Show import file dialog
    $('#importButton').on('click', function() {
        $('#importCsv').click();
    });

    // Handle CSV import
    $('#importCsv').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            Papa.parse(file, {
                header: true,
                complete: function(results) {
                    var data = results.data;
                    data.forEach(function(row) {
                        $.ajax({
                            url: "http://127.0.0.1:8000/api/students",
                            type: "POST",
                            data: {
                                name: row.name,
                                email: row.email,
                                age: row.age,
                                course: row.course
                            },
                            success: function () {
                                console.log("Student added: " + row.name);
                            }
                        });
                    });
                    alert("CSV import complete!");
                    table.ajax.reload();
                }
            });
        }
    });

    // Other CRUD code (Add/Edit/Delete) unchanged...
    $('#openAddModal').on('click', function(e) {
        e.preventDefault();
        $('#addStudentModal').css('display', 'flex');
    });
    // ... (Rest of CRUD code)
});
</script>
</body>
</html>

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
        </div>

        <div class="dt-buttons"></div>
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

    <!-- Add Modal -->
    <div id="addStudentModal">
        <div class="modal-content">
        <h2>Add Student</h2>
        <input type="text" id="newStudentName" placeholder="Name" />
        <input type="email" id="newStudentEmail" placeholder="Email" />
        <input type="number" id="newStudentAge" placeholder="Age" />
        <input type="text" id="newStudentCourse" placeholder="Course" />
        <button id="addStudentBtn">Add Student</button>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editStudentModal">
        <div class="modal-content">
        <h2>Edit Student</h2>
        <input type="text" id="editStudentName" placeholder="Name" />
        <input type="email" id="editStudentEmail" placeholder="Email" />
        <input type="number" id="editStudentAge" placeholder="Age" />
        <input type="text" id="editStudentCourse" placeholder="Course" />
        <button id="saveStudentChanges">Update Student</button>
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function () {
        var table = $('#studentsTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ajax: {
            url: "http://127.0.0.1:8000/api/students",
            type: "GET",
            dataSrc: "students"
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
            ]
        });

        $('#openAddModal').on('click', function(e) {
            e.preventDefault();
            $('#addStudentModal').css('display', 'flex');
        });

        $('#addStudentBtn').on('click', function() {
            $.ajax({
            url: "http://127.0.0.1:8000/api/students",
            type: "POST",
            data: {
                name: $('#newStudentName').val(),
                email: $('#newStudentEmail').val(),
                age: $('#newStudentAge').val(),
                course: $('#newStudentCourse').val()
            },
            success: function () {
                alert("Student added successfully");
                $('#addStudentModal').hide();
                $('#newStudentName, #newStudentEmail, #newStudentAge, #newStudentCourse').val('');
                table.ajax.reload();
            },
            error: function () {
                alert("Failed to add student.");
            }
            });
        });

        $('#studentsTable').on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            $.get(`http://127.0.0.1:8000/api/students/${id}`, function(data) {
            $('#editStudentName').val(data.name);
            $('#editStudentEmail').val(data.email);
            $('#editStudentAge').val(data.age);
            $('#editStudentCourse').val(data.course);
            $('#editStudentModal').data('id', id).css('display', 'flex');
            });
        });

        $('#saveStudentChanges').on('click', function () {
            var id = $('#editStudentModal').data('id');
            $.ajax({
            url: `http://127.0.0.1:8000/api/students/${id}`,
            type: "PUT",
            contentType: "application/json",
            data: JSON.stringify({
                name: $('#editStudentName').val(),
                email: $('#editStudentEmail').val(),
                age: $('#editStudentAge').val(),
                course: $('#editStudentCourse').val()
            }),
            success: function () {
                alert("Student updated successfully");
                $('#editStudentModal').hide();
                table.ajax.reload();
            },
            error: function () {
                alert("Failed to update student.");
            }
            });
        });

        $('#studentsTable').on('click', '.delete-btn', function () {
            var id = $(this).data('id');
            if (confirm("Are you sure you want to delete this student?")) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/students/${id}`,
                type: "DELETE",
                success: function () {
                alert("Student deleted successfully");
                table.ajax.reload();
                },
                error: function () {
                alert("Failed to delete student.");
                }
            });
            }
        });

        $(window).on('click', function(e) {
            if ($(e.target).is('#addStudentModal')) $('#addStudentModal').hide();
            if ($(e.target).is('#editStudentModal')) $('#editStudentModal').hide();
        });
        });
    </script>
    </body>
</html>

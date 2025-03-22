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
        body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
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
    </style>
</head>
<body>
    <h1>Employees List</h1>
    <div style="width: 100%; max-width: 1200px; margin: 0 auto;">
        <div class="dt-buttons"></div>
        <table id="employeesTable" class="display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th id="employee-id" name="employee-id">ID</th>
                    <th id="employee-name" name="employee-name">Name</th>
                    <th id="employee-position" name="employee-position">Position</th>
                    <th id="employee-salary" name="employee-salary">Salary</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <a href="dashboard.php" class="back-button"> Back </a>
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
                dom: '<"dt-buttons"B>frtip',
                buttons: [
                    'copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'print'
                ],
                "ajax": {
                    "url": "http://127.0.0.1:8000/api/employees",
                    "type": "GET",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "id", "name": "employee-id" },
                    { "data": "name", "name": "employee-name" },
                    { "data": "position", "name": "employee-position" },
                    { "data": "salary", "name": "employee-salary", "render": $.fn.dataTable.render.number(',', '.', 2, '$') }
                ]
            });
        });
    </script>
</body>
</html>

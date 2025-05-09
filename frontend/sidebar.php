<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>

    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href='https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex text-dark">

    <!-- Sidebar -->
    <aside class="bg-white shadow-lg p-4 d-none d-md-block" style="width: 250px;">
        <h1 class="h4 fw-bold mb-4">Admin Panel</h1>
        <nav class="nav flex-column">
            <a href="profile.php" class="nav-link text-dark py-2"><i class='bx bx-home me-2'></i>Profile</a>
            <a href="dashboard.php" class="nav-link text-dark py-2"><i class='bx bx-home me-2'></i>Dashboard</a>
            <a href="users.php" class="nav-link text-dark py-2"><i class='bx bx-user me-2'></i>Users</a>
            <a href="employees.php" class="nav-link text-dark py-2"><i class='bx bx-briefcase me-2'></i>Employees</a>
            <a href="students.php" class="nav-link text-dark py-2"><i class='bx bx-book me-2'></i>Students</a>
            <a href="#" id="logoutButton" class="nav-link text-danger py-2"><i class='bx bx-log-out me-2'></i>Logout</a>
        </nav>
    </aside>
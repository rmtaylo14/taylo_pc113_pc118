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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-light min-vh-100 d-flex text-dark">

    <!-- Sidebar -->
    <aside class="bg-white shadow-lg p-4 d-none d-md-block" style="width: 250px;">
    <h1 class="h4 fw-bold mb-4">
    <img src="assets/logo.png" alt="Logo" style="
        max-width: 200px;     /* adjust width */
        height: auto;         /* maintain aspect ratio */
        display: block;       /* block-level for alignment */
        margin: 0 auto 20px;  /* center horizontally, with bottom margin */
    ">

    </h1>

        <nav class="nav flex-column">
            <a href="profile.php" class="nav-link text-dark py-2"><i class='bx bx-home me-2'></i>Profile</a>
            <a href="dashboard.php" class="nav-link text-dark py-2"><i class='bx bx-home me-2'></i>Dashboard</a>
            <a href="menu.php" class="nav-link text-dark py-2"><i class='bx bx-home me-2'></i>Menu</a>
            <a href="orders.php" class="nav-link text-dark py-2"><i class='bx bx-home me-2'></i>Orders</a>
            <a href="myorders.php" class="nav-link text-dark py-2"><i class='bx bx-history me-2'></i>My Orders</a>
            <a href="users.php" class="nav-link text-dark py-2"><i class='bx bx-user me-2'></i>Users</a>
            <a href="reports.php" class="nav-link text-dark py-2"><i class='bx bx-user me-2'></i>Reports</a>
            <a href="settings.php" class="nav-link text-dark py-2"><i class='bx bx-user me-2'></i>Settings</a>
            <!-- <a href="employees.php" class="nav-link text-dark py-2"><i class='bx bx-briefcase me-2'></i>Employees</a>
            <a href="students.php" class="nav-link text-dark py-2"><i class='bx bx-book me-2'></i>Students</a> -->
            <a href="#" id="logoutButton" class="nav-link text-danger py-2"><i class='bx bx-log-out me-2'></i>Logout</a>
        </nav>
    </aside>

<script>
// Get the role from localStorage
const userRole = localStorage.getItem('role');
const sidebarLinks = document.querySelectorAll('aside nav a');

// Sidebar nav links
sidebarLinks.forEach(link => {
    const href = link.getAttribute('href');

    if (userRole === 'manager') {
        // Manager should not see users.php or orders.php
        if (href === 'users.php' || href === 'orders.php' || href === 'myorders.php') {
            link.style.display = 'none';
        }
    } else if (userRole === 'admin') {
        // Admin sees all except orders.php
        if (href === 'orders.php' || href === 'myorders.php') {
            link.style.display = 'none';
        }
    } else if (userRole === 'user' || !userRole) {
        // Regular user sees only dashboard and orders
        if (href !== 'dashboard.php' && href !== 'orders.php' && href !== 'myorders.php' && href !== '#') {
            link.style.display = 'none';
        }
    } else {
        // Unknown role: hide all
        link.style.display = 'none';
    }
});


// Logout function with SweetAlert2 confirmation
function logout() {
    Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem("token");
            localStorage.removeItem("role");
            window.location.href = "index.php"; // or login.php if you prefer
        }
    });
}

// Attach logout to logout button
document.getElementById('logoutButton').addEventListener('click', logout);

</script>

</script>


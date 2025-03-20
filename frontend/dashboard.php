<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard</title>

    <!-- Fonts and Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="dark:bg-gray-950 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-gray-900 shadow-md min-h-screen">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Admin Panel</h1>
            <nav>
                <a href="#" class="flex items-center p-3 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-md mb-2">
                    <i class='bx bx-home mr-2'></i> Users
                </a>
                <button id="logoutBtn" class="w-full flex items-center p-3 text-red-600 hover:bg-red-100 dark:hover:bg-red-800 rounded-md">
                    <i class='bx bx-log-out mr-2'></i> Logout
                </button>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-8">
        <h2 class="text-3xl font-bold mb-6 dark:text-white">Welcome, <span id="userName">Admin</span>!</h2>

        <!-- Latest Orders Table -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Latest Orders</h3>
            <table class="w-full table-auto">
                <!-- Table content goes here -->
            </table>
        </div>
    </main>

    <!-- Logout Script -->
    <script>
        // Handle logout button click
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (confirm('You have been successfully logged out!')) {
                localStorage.removeItem('token'); // Remove token
                window.location.href = 'index.php'; // Redirect to login page
            }
        });

        // Set user's name if available
        const userName = localStorage.getItem('userName') || 'Admin';
        document.getElementById('userName').textContent = userName;
    </script>

</body>
</html>

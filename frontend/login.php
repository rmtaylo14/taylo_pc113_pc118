<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>API Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h1 class="text-center mb-4">Welcome</h1>
        <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                <!-- <a href="#" class="small text-decoration-none">Forgot Password?</a> -->
            </div>
            <!-- <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" checked>
                <label class="form-check-label" for="remember">Remember me</label>
            </div> -->
            <!-- <div class="d-flex justify-content-between">
                <a href="#" class="small text-decoration-none">Create Account</a>
            </div> -->
            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Redirect if token exists
    if (localStorage.getItem('token')) {
        // Optional: redirect by role on page load
        const role = localStorage.getItem('role');
        if (role === 'admin' || role === 'manager') {
            window.location.href = 'login.php';
        } else {
            window.location.href = 'orders.php';
        }
    }

    document.getElementById('loginForm').addEventListener('submit', async function (event) {
        event.preventDefault();

        let email = document.getElementById('email').value.trim();
        let password = document.getElementById('password').value.trim();
        let errorMessage = document.getElementById('errorMessage');

        if (!email || !password) {
            errorMessage.textContent = "Both fields are required.";
            errorMessage.classList.remove('d-none');
            return;
        }

        errorMessage.classList.add('d-none');

        try {
            let response = await fetch('http://127.0.0.1:8000/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            let data = await response.json();

            if (!response.ok) {
                errorMessage.textContent = data.message || "Login failed. Please try again.";
                errorMessage.classList.remove('d-none');
                return;
            }

            localStorage.setItem('token', data.token);
            localStorage.setItem('role', data.user.role);


            if (data.user.role === 'admin' || data.user.role === 'manager') {
                window.location.href = 'dashboard.php';
            } else {
                window.location.href = 'orders.php';
            }

        } catch (error) {
            if (error.name === 'TypeError') {
                errorMessage.textContent = "Network error or server is unreachable.";
            } else {
                errorMessage.textContent = error.message || "An unexpected error occurred.";
            }
            console.error('Error:', error);
            errorMessage.classList.remove('d-none');
        }
    });
</script>

</body>
</html>

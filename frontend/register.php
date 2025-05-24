<!-- register.php -->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="bg-light">
    <div class="container mt-5">
    <h2 class="text-center mb-4">Register</h2>
    <form id="registerForm" class="card p-4 shadow-sm">
        <input type="text" id="firstName" class="form-control mb-2" placeholder="First Name" required>
        <input type="text" id="lastName" class="form-control mb-2" placeholder="Last Name" required>
        <input type="email" id="email" class="form-control mb-2" placeholder="Email" required>
        <input type="text" id="address" class="form-control mb-2" placeholder="Address">
        <input type="text" id="phone" class="form-control mb-2" placeholder="Phone Number">
        <input type="password" id="password" class="form-control mb-3" placeholder="Password" required>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    </div>

    <script>
    document.getElementById("registerForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const data = {
        firstname: document.getElementById("firstName").value,
        lastname: document.getElementById("lastName").value,
        email: document.getElementById("email").value,
        address: document.getElementById("address").value,
        phone_number: document.getElementById("phone").value,
        password: document.getElementById("password").value,
        role: 'user'  // regular user role
    };

    fetch("http://127.0.0.1:8000/api/register", {
        method: "POST",
        headers: {
        "Content-Type": "application/json",
        Accept: "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then( data => {
            Swal.fire({
            icon: 'success',
            title: 'Registration Successful',
            text: 'You have been registered successfully.',
            confirmButtonText: 'OK'
            }).then(() => {
            window.location.href = "login.php"; // Redirect to login page
            });
        })
    })

</script>
</body>
</html>

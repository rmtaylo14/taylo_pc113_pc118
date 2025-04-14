<?php 
include 'sidebar.php';

// Simulate user info (replace these with actual session data or database query)
$userName = "Clara Mendoza";
$userEmail = "clara.mendoza@example.com";
$userPassword = "password123"; // normally hashed — this is just for demo
$userProfilePicture = "https://via.placeholder.com/150"; // Placeholder, replace with actual profile picture URL
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Profile Management</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <!-- Profile Picture -->
                    <div class="text-center mb-3">
                        <img id="profilePicture" src="<?php echo $userProfilePicture; ?>" alt="Profile Picture" class="rounded-circle mb-3" width="150" height="150">
                    </div>
                    
                    <h4 class="text-center">User Information</h4>
                    <div id="profileDetails" class="mb-3">
                        <p><strong>Name:</strong> <?php echo $userName; ?></p>
                        <p><strong>Email:</strong> <?php echo $userEmail; ?></p>
                        <p><strong>Password:</strong> ********</p>
                    </div>
                    <button id="editProfileButton" class="btn btn-primary w-100">Edit Profile</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        $('#editProfileButton').on('click', function() {
            // Replace profileDetails div with a form, including a file input for profile picture
            $('#profileDetails').html(`
                <form id="profileForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" value="<?php echo $userName; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo $userEmail; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" value="<?php echo $userPassword; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Save Changes</button>
                </form>
            `);
        });

        // Handle form submission
        $(document).on('submit', '#profileForm', function(e) {
            e.preventDefault();

            const name = $('#name').val();
            const email = $('#email').val();
            const password = $('#password').val();
            const profilePicture = $('#profile_picture')[0].files[0]; // Get uploaded file

            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            if (profilePicture) {
                formData.append('profile_picture', profilePicture);
            }

            // Here you can send an AJAX request to update the profile on your server
            console.log("Updated Data:");
            console.log("Name:", name);
            console.log("Email:", email);
            console.log("Password:", password);

            // Send the form data (including profile picture) to your server
            $.ajax({
                url: 'http://your-laravel-app.test/api/user/update', // Replace with your API endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert("Profile updated successfully!");
                    // Optionally update profile picture preview
                    if (response.profile_picture) {
                        $('#profilePicture').attr('src', response.profile_picture);
                    }
                    // Replace form back with updated details after submit
                    $('#profileDetails').html(`
                        <p><strong>Name:</strong> ${name}</p>
                        <p><strong>Email:</strong> ${email}</p>
                        <p><strong>Password:</strong> ********</p>
                    `);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Failed to update profile.");
                }
            });
        });
    </script>
</body>
</html>

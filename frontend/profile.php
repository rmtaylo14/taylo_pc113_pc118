<?php 
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Update</title>
    <link rel="stylesheet" href="styles/profile.css?v=1.0.7" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .form-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 30px;
    background-color: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    font-family: Arial, sans-serif;
}

.form-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.form-container p#welcome-msg {
    text-align: center;
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input[disabled] {
    background-color: #f5f5f5;
    color: #888;
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 15px;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.profile-picture-container {
    text-align: center;
    margin-bottom: 20px;
}

.profile-picture-container img {
    border-radius: 50%;
    border: 2px solid #ccc;
    width: 150px;
    height: 150px;
    object-fit: cover;
    margin-bottom: 10px;
}

.profile-picture-container input[type="file"] {
    display: block;
    margin: 0 auto;
}

    </style>
</head>
<body>

<div class="form-container">
    <!-- Profile picture upload + display -->
    <div class="profile-picture-container">
        <img id="profileImage" src="" alt="Profile Picture" style="width: 150px  ">
        <input type="file" id="profile_picture" accept="image/*" >
    </div>

    <h2>Update Profile</h2>
    <p id="welcome-msg" style="font-style: italic; color: #555;"></p>

    <form id="profileForm">
        <input type="hidden" name="id" id="userId">

        <div class="form-row">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" placeholder="Enter first name" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" placeholder="Enter last name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter address">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" placeholder="Enter phone number">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" id="role" name="role" disabled readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password <small>(Leave blank to keep current password)</small></label>
            <input type="password" id="password" name="password" placeholder="New password">
        </div>

        <button type="submit">Update Profile</button>
    </form>
</div>

<script>
$(document).ready(function () {
    const token = localStorage.getItem("token");

    if (!token) {
        window.location.href = "login.php";
        return;
    }

    // Fetch user profile
    $.ajax({
        url: "http://127.0.0.1:8000/api/user/profile",
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json"
        },
        success: function (response) {
            console.log(response);
            const user = response.user; // <- Access 'user' from response

            // Fill the form fields with the user's data
            $("#userId").val(user.id);
            $("#firstname").val(user.firstname);
            $("#lastname").val(user.lastname);
            $("#email").val(user.email);
            $("#address").val(user.address);
            $("#phone_number").val(user.phone_number);
            $("#role").val(user.role);
            // $("#welcome-msg").text(`Logged in as: ${user.firstname} ${user.lastname} (${user.email})`);

            // If the user has a profile picture, update the image source
            if (user.profile_picture_url) {
                $("#profileImage").attr("src", user.profile_picture_url);
            }else {
                document.getElementById('profileImage').src = "http://127.0.0.1:8000/storage/profile_pictures/default.jpg"
            }
        },
        error: function (xhr) {
            console.error("Failed to load profile:", xhr.responseText);
            alert("Error loading profile. Please login again.");
            localStorage.removeItem("token");
            window.location.href = "login.php";
        }
    });

    // Update form submission
    $("#profileForm").submit(function (e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append("firstname", $("#firstname").val());
        formData.append("lastname", $("#lastname").val());
        formData.append("email", $("#email").val());
        formData.append("address", $("#address").val());
        formData.append("phone_number", $("#phone_number").val());
//       formData.append("role", $("#role").val());

        const password = $("#password").val();
        if (password) {
            formData.append("password", password);
        }

        const fileInput = document.getElementById('profile_picture').files[0];
        console.log(fileInput);
        if (fileInput) {
            formData.append("profile_picture", fileInput);
        }

        $.ajax({
            url: "http://127.0.0.1:8000/api/user/profile/update",
            method: "POST", // Laravel expects POST for file uploads unless customized
            processData: false,
            contentType: false,
            data: formData,
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json"
            },
            success: function (response) {
                alert("Profile updated successfully!");
                $("#password").val(""); // Clear the password field after update

                // Update the profile picture if there's a new one
                if (response.profile_picture_url) {
                    $("#profileImage").attr("src", response.profile_picture_url + '?' + new Date().getTime());
                }
            },
            error: function (xhr) {
                console.error("Update error:", xhr);
                let message = "Failed to update profile.";
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message += " " + response.message;
                    }
                } catch (e) {
                    message += " " + xhr.responseText;
                }
                alert(message);
            }
        });
    });
});
</script>


</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        const role = localStorage.getItem("role");
        if (role !== "admin" && role !== "manager") {
        window.location.href = "unauthorized.php";
        }
    </script>
    <meta charset="UTF-8">
    <title>Menu Management</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* Updated Add button (green) */
    .user-button {
        background-color: #4CAF50; /* Green */
        color: white;
        padding: 8px 12px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        display: inline-block;
    }

    .user-button:hover {
        opacity: 0.8;
    }

    /* Updated Edit button (blue) */
    .edit-btn {
        background-color: #2196F3; /* Blue */
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .edit-btn:hover, .delete-btn:hover {
        opacity: 0.8;
    }

    /* Keep delete button red */
    .delete-btn {
        background-color: #f44336; /* Red */
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Optional: DataTables adjustments */
    table.display td, table.display th {
        text-align: center;
        vertical-align: middle;
    }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div style="width: 100%; max-width: 1200px; margin: auto; padding: 10px;">
    <div class="header-bar">
        <h3>Menu Management</h3>
        <a href="#" id="openAddModal" class="user-button">Add Menu Item</a>
    </div>

    <table id="menuTable" class="display nowrap" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Available</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const token = localStorage.getItem("token");
    if (!token) {
        window.location.href = "login.php";
        return;
    }

    loadMenu();
    document.getElementById("openAddModal").addEventListener("click", createMenuItem);

    // ✅ Delegated Event Listener for Edit and Delete buttons
    document.querySelector("#menuTable tbody").addEventListener("click", (e) => {
        const target = e.target;
        const id = target.dataset.id;

        if (target.classList.contains("edit-btn")) {
            editMenuItem(id);
        } else if (target.classList.contains("delete-btn")) {
            deleteMenuItem(id);
        }
    });
});

async function loadMenu() {
    const token = localStorage.getItem("token");

    try {
        const res = await fetch("http://127.0.0.1:8000/api/index/menu", {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        const data = await res.json();

        const tbody = document.querySelector("#menuTable tbody");
        tbody.innerHTML = "";

        data.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.name}</td>
                <td>${item.description}</td>
                <td>${item.price}</td>
                <td>${item.is_available ? 'Yes' : 'No'}</td>
                <td><img src="http://127.0.0.1:8000/storage/${item.image_path}" width="50"></td>
                <td>
                    <button class="edit-btn" data-id="${item.id}">Edit</button>
                    <button class="delete-btn" data-id="${item.id}">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (err) {
        console.error("Failed to load menu:", err);
    }
}

function getMenuFormHtml(item = {}) {
    return `
        <input id="swal-name" class="swal2-input" placeholder="Name" value="${item.name || ''}">
        <textarea id="swal-description" class="swal2-textarea" placeholder="Description">${item.description || ''}</textarea>
        <input id="swal-price" class="swal2-input" type="number" placeholder="Price" value="${item.price || ''}">
        <select id="swal-available" class="swal2-select">
            <option value="true" ${item.is_available ? 'selected' : ''}>Yes</option>
            <option value="false" ${item.is_available === false ? 'selected' : ''}>No</option>
        </select>
        <input id="swal-image" class="swal2-input" type="file">
    `;
}

async function deleteMenuItem(id) {
    const token = localStorage.getItem("token");

    const { isConfirmed } = await Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the menu item.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f43f5e',
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-button',
            cancelButton: 'custom-swal-cancel'
        }
    });

    if (!isConfirmed) return;

    try {
        const res = await fetch(`http://127.0.0.1:8000/api/menu/${id}`, {
            method: "DELETE",
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        if (!res.ok) throw new Error("Delete failed");

        await Swal.fire("Deleted!", "Menu item deleted.", "success");
        loadMenu();
    } catch (e) {
        Swal.fire("Error", "Could not delete item.", "error");
    }
}

async function createMenuItem() {
    const { isConfirmed, value: formData } = await Swal.fire({
        title: 'Add Menu Item',
        html: getMenuFormHtml(),
        showCancelButton: true,
        confirmButtonText: 'Create',
        focusConfirm: false,
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-button',
            cancelButton: 'custom-swal-cancel'
        },
        preConfirm: () => {
            const name = document.getElementById("swal-name").value.trim();
            const description = document.getElementById("swal-description").value.trim();
            const price = parseFloat(document.getElementById("swal-price").value);
            const isAvailable = document.getElementById("swal-available").value === 'true';
            const imageInput = document.getElementById("swal-image").files[0];

            if (!name) {
                Swal.showValidationMessage("Name is required");
                return false;
            }
            if (isNaN(price) || price <= 0) {
                Swal.showValidationMessage("Price must be a positive number");
                return false;
            }

            const fd = new FormData();
            fd.append('name', name);
            fd.append('description', description);
            fd.append('price', price);
            fd.append('is_available', isAvailable ? 1 : 0);
            if (imageInput) {
                fd.append('image', imageInput);
            }
            return fd;
        }
    });

    if (!isConfirmed) return;

    const token = localStorage.getItem("token");

    try {
        const res = await fetch("http://127.0.0.1:8000/api/menu", {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`
            },
            body: formData
        });
        if (!res.ok) throw new Error("Create failed");
        await res.json();

        Swal.fire("Created!", "Menu item added.", "success");
        loadMenu();
    } catch {
        Swal.fire("Error", "Could not create item.", "error");
    }
}

async function editMenuItem(id) {
    const token = localStorage.getItem("token");

    try {
        // 🛠️ Fetch specific menu item data using the correct ID
        const res = await fetch(`http://127.0.0.1:8000/api/update/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        if (!res.ok) throw new Error("Failed to fetch menu item");
        const item = await res.json();

        // 🧠 Show SweetAlert2 modal pre-filled with item data
        const { isConfirmed, value: formData } = await Swal.fire({
            title: 'Edit Menu Item',
            html: getMenuFormHtml(item),
            showCancelButton: true,
            confirmButtonText: 'Update',
            focusConfirm: false,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                confirmButton: 'custom-swal-button',
                cancelButton: 'custom-swal-cancel'
            },
            preConfirm: () => {
                const name = document.getElementById("swal-name").value.trim();
                const description = document.getElementById("swal-description").value.trim();
                const price = parseFloat(document.getElementById("swal-price").value);
                const isAvailable = document.getElementById("swal-available").value === 'true';
                const imageInput = document.getElementById("swal-image").files[0];

                if (!name) {
                    Swal.showValidationMessage("Name is required");
                    return false;
                }
                if (isNaN(price) || price <= 0) {
                    Swal.showValidationMessage("Price must be a positive number");
                    return false;
                }

                const fd = new FormData();
                fd.append('name', name);
                fd.append('description', description);
                fd.append('price', price);
                fd.append('is_available', isAvailable ? 1 : 0);
                if (imageInput) {
                    fd.append('image', imageInput);
                }
                return fd;
            }
        });

        if (!isConfirmed) return;

        // ✅ Send PUT update to backend
        const updateRes = await fetch(`http://127.0.0.1:8000/api/menu/${id}`, {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${token}`,
                // 'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });
        if (!updateRes.ok) throw new Error("Update failed");
        await updateRes.json();

        Swal.fire("Updated!", "Menu item updated.", "success");
        loadMenu();
    } catch (error) {
        console.error(error);
        Swal.fire("Error", "Could not load or update item.", "error");
    }
}



async function deleteMenuItem(id) {
    const token = localStorage.getItem("token");

    const result = await Swal.fire({
        title: 'Delete Menu Item?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-button',
            cancelButton: 'custom-swal-cancel'
        }
    });

    if (!result.isConfirmed) return;

    try {
        const res = await fetch(`http://127.0.0.1:8000/api/menu/${id}`, {
            method: "DELETE",
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        if (!res.ok) throw new Error();

        await Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Menu item was successfully deleted.',
            customClass: {
                popup: 'custom-swal-popup'
            }
        });

        loadMenu();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to delete menu item.',
            customClass: {
                popup: 'custom-swal-popup'
            }
        });
    }
}
</script>
</body>
</html>

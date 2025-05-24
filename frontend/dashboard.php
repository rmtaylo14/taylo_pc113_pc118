<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .hoverable-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .hoverable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .custom-card-width {
            width: 250px;
        }
        .small-subtext {
            font-size: 0.8rem;
            color: #eee;
            display: block;
            margin-top: -6px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main id="mainContent" class="flex-grow-1 p-4">
        <h2 class="h3 mb-4">Dashboard</h2>
        <p>Loading stats...</p>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const mainContent = document.getElementById("mainContent");
        const token = localStorage.getItem("token");

        if (!token) {
            mainContent.innerHTML = `<p class="text-danger">User not authenticated.</p>`;
            return;
        }

        Promise.all([
            fetch("http://localhost:8000/api/users/index", {
                headers: { "Authorization": `Bearer ${token}` }
            }).then(res => {
                if (!res.ok) throw new Error("Failed to fetch users");
                return res.json();
            }),
            fetch("http://localhost:8000/api/index/menu", {
                headers: { "Authorization": `Bearer ${token}` }
            }).then(res => {
                if (!res.ok) throw new Error("Failed to fetch menu items");
                return res.json();
            }),
            fetch("http://localhost:8000/api/orders", {
                headers: { "Authorization": `Bearer ${token}` }
            }).then(res => {
                if (!res.ok) throw new Error("Failed to fetch orders");
                return res.json();
            }),
            fetch("http://127.0.0.1:8000/api/deliveries", {
                headers: { "Authorization": `Bearer ${token}` }
            }).then(res => {
                if (!res.ok) throw new Error("Failed to fetch delivery orders");
                return res.json();
            })
        ])
        .then(([usersData, menuData, ordersData, deliveryData]) => {
            const userCount = Array.isArray(usersData) ? usersData.length : 0;
            const menuCount = Array.isArray(menuData) ? menuData.length : 0;
            const orderCount = Array.isArray(ordersData) ? ordersData.length : 0;
            const deliveryCount = Array.isArray(deliveryData) ? deliveryData.length : 0;

            mainContent.innerHTML = `
                <h2 class="h3 mb-4">Dashboard</h2>
                <div class="d-flex flex-wrap justify-content-center gap-4">
                    <a href="users.php" class="text-decoration-none">
                        <div class="card text-white mb-3 hoverable-card custom-card-width" style="background-color: #FFC107;">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text fs-4">${userCount}</p>
                            </div>
                        </div>
                    </a>
                    <a href="menu.php" class="text-decoration-none">
                        <div class="card text-white mb-3 hoverable-card custom-card-width" style="background-color: #6c757d;">
                            <div class="card-body">
                                <h5 class="card-title">Available Food</h5>
                                <p class="card-text fs-4">${menuCount}</p>
                            </div>
                        </div>
                    </a>
                    <a href="deliveryorders.php" class="text-decoration-none">
                        <div class="card text-white mb-3 hoverable-card custom-card-width" style="background-color: #e6b800;">
                            <div class="card-body">
                                <h5 class="card-title">To Be Delivered</h5>
                                <p class="card-text fs-4">Total Orders: ${orderCount}</p>
                            </div>
                        </div>
                    </a>


                </div>
            `;
        })
        .catch(error => {
            console.error("Error loading dashboard:", error);
            mainContent.innerHTML = `
                <h2 class="h3 mb-4">Dashboard</h2>
                <p class="text-danger">Failed to load dashboard data.</p>
            `;
        });

        // Logout button if exists
        const logoutBtn = document.getElementById("logoutButton");
        if (logoutBtn) {
            logoutBtn.addEventListener("click", () => {
                if (confirm("Are you sure you want to logout?")) {
                    localStorage.removeItem("token");
                    window.location.href = "index.php";
                }
            });
        }
    });
    </script>
</body>
</html>

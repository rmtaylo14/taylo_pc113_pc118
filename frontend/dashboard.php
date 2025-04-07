<?php 
include 'sidebar.php';
?>

    <!-- Main Content -->
    <main id="mainContent" class="flex-grow-1 p-4">
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let lastPage = localStorage.getItem("lastPage") || "dashboard";  

            if (lastPage === "dashboard") {
                loadDashboard();
            }

            document.getElementById("logoutButton").addEventListener("click", () => {
                logout();
            });
        });

        function loadDashboard() {
            let mainContent = document.getElementById("mainContent"); 
            mainContent.innerHTML = ""; 
            setTimeout(() => {
                mainContent.innerHTML = `
                    <h2 class="h3 mb-4">Dashboard</h2>
                    <p>Welcome to the dashboard. Select an option from the sidebar.</p>
                `;
            }, 300);
        }

        function logout() {
            if (confirm("Are you sure you want to logout?")) {
                localStorage.removeItem("token");
                window.location.href = "index.php";
            }
        }
    </script>
</body>
</html>

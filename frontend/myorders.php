<?php 
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">My <span class="text-success">Orders</span></h1>

        <div class="d-flex justify-content-end mb-3">
            <button id="refresh-btn" class="btn btn-outline-primary">Refresh</button>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="orders-list"></tbody>
        </table>
    </div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem("token");
    if (!token) { window.location.href = "login.php"; return; }

    fetchOrders(token);

    document.getElementById('refresh-btn').addEventListener('click', () => fetchOrders(token));
});

// ---------------- API ----------------
function fetchOrders(token){
    fetch("http://127.0.0.1:8000/api/user/orders", { // Adjust API endpoint as needed
        headers: { Authorization: `Bearer ${token}` }
    })
    .then(response => response.json())
    .then(renderOrders)
    .catch(() => alert("Failed to load your orders."));
}

// ---------------- RENDER ----------------
function renderOrders(data){
    const list = document.getElementById('orders-list');
    list.innerHTML = '';

    if(data.length === 0){
        list.innerHTML = '<tr><td colspan="5" class="text-center">No orders found.</td></tr>';
        return;
    }

    data.forEach(order => {
        const date = new Date(order.created_at).toLocaleString();
        list.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${order.id}</td>
                <td>${date}</td>
                <td><span class="badge ${getStatusClass(order.status)}">${order.status}</span></td>
                <td>₱${(+order.total).toFixed(2)}</td>
                <td><a href="receipt.php?order_id=${order.id}" class="btn btn-sm btn-info">View Receipt</a></td>

            </tr>
        `);
    });
}

function getStatusClass(status){
    switch(status.toLowerCase()){
        case 'pending': return 'bg-warning text-dark';
        case 'completed': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
</script>
</body>
</html>

<?php /* orders.php */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<?php include 'sidebar.php'; ?>
<body class="bg-light">
<div class="container py-5">
    <h1 class="text-center mb-4">Order Summary</h1>

    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Image</th></tr>
        </thead>
        <tbody id="summary-body"></tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center">
        <strong>Total: ₱<span id="summary-total">0.00</span></strong>
        <button id="submit-order-btn" class="btn btn-success">Confirm</button>
    </div>
</div>

<script>
let items = JSON.parse(localStorage.getItem('pendingOrder') || '[]');

// ✅ Data Integrity Check
if (!Array.isArray(items) || items.length === 0 || items.some(i => !i.id || !i.price)) {
    alert("Invalid order data.");
    localStorage.removeItem('pendingOrder');
    window.location.href = 'orders.php';
}

document.addEventListener('DOMContentLoaded', () => {
    renderSummary();
    document.getElementById('submit-order-btn').addEventListener('click', submitOrder);
});

// ---------------- RENDER ----------------
function renderSummary() {
    const body = document.getElementById('summary-body');
    let total = 0;
    body.innerHTML = '';
    items.forEach(it => {
        total += +it.price;
        body.insertAdjacentHTML('beforeend', `
        <tr>
            <td>${it.id}</td><td>${it.name}</td><td>${it.description}</td>
            <td>₱${(+it.price).toFixed(2)}</td>
            <td><img src="http://127.0.0.1:8000/storage/${it.image_path}" width="50"></td>
        </tr>`);
    });
    document.getElementById('summary-total').textContent = total.toFixed(2);
}

// ---------------- SUBMIT ----------------
function submitOrder() {
    const token = localStorage.getItem('token');
    if (!token) { 
        Swal.fire('Unauthorized', 'Login required to place an order.', 'warning');
        return; 
    }
    const payload = { items: items.map(i => i.id) };

    fetch("http://127.0.0.1:8000/api/orders", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
        },
        body: JSON.stringify(payload)
    })
    .then(r => {
        if (!r.ok) throw new Error('Order failed');
        return r.json();
    })
    .then(o => {
        localStorage.removeItem('pendingOrder'); // clear saved cart
        Swal.fire({
            title: 'Order Placed!',
            text: `Your order #${o.id} has been submitted successfully.`,
            icon: 'success',
            confirmButtonText: 'View Receipt'
        }).then(() => {
            window.location.href = `receipt.php?order_id=${o.id}`;
        });
    })
    .catch(() => {
        Swal.fire('Error', 'Unable to place order. Please try again.', 'error');
    });
}
</script>
</body>
</html>

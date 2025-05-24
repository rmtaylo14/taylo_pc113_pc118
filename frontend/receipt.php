<?php
$orderId = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
        background-color: #f8f9fa;
        }
        .receipt-card {
        max-width: 800px;
        margin: auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        padding: 30px;
        }
        .receipt-header {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 15px;
        margin-bottom: 20px;
        }
        .receipt-header h3 {
        font-weight: 600;
        }
        .product-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        }
        .product-name {
        font-weight: 500;
        }
        .total-row {
        font-size: 1.2rem;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        }
        .btn-print {
        display: block;
        margin: 30px auto 0;
        }
        @media print {
        .btn-print, .sidebar {
            display: none !important;
        }
        body {
            background: white;
        }
        .receipt-card {
            box-shadow: none;
            border: none;
        }
        }
    </style>
    </head>
    <?php include 'sidebar.php'; ?>
    <body>
    <div class="container py-5">
        <div class="receipt-card" id="order-details">
        <!-- Receipt content will be injected here -->
        </div>
        <button class="btn btn-primary btn-print" onclick="window.print()">🖨️ Print Receipt</button>
        </div>

    <script>
    const orderId = "<?php echo htmlspecialchars($orderId); ?>";
    const token = localStorage.getItem("token");

    if (!orderId || !token) {
        Swal.fire('Error', 'Invalid access.', 'error').then(() => {
        window.location.href = "orders.php";
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        fetch(`http://127.0.0.1:8000/api/orders/${orderId}`, {
        headers: {
            Authorization: `Bearer ${token}`
        }
        })
        .then(r => {
        if (!r.ok) throw new Error("Failed to fetch order.");
        return r.json();
        })
        .then(order => renderOrder(order))
        .catch(() => {
        Swal.fire('Error', 'Unable to load order.', 'error').then(() => {
            window.location.href = "orderuser.php";
        });
        });
    });

    function renderOrder(order) {
        let html = `
        <div class="receipt-header">
            <h3>Order #${order.id}</h3>
            <p>Status: <strong>${order.status || "Pending"}</strong></p>
        </div>
        `;

        let total = 0;
        order.items.forEach(item => {
        const price = parseFloat(item.price);
        total += price;
        html += `
            <div class="product-item">
            <div>
                <div class="product-name">${item.name}</div>
                <div class="text-muted" style="font-size: 0.9rem;">${item.description}</div>
            </div>
            <div class="text-end">₱${price.toFixed(2)}</div>
            </div>
        `;
        });

        html += `
        <div class="total-row">
            <span>Total</span>
            <span>₱${total.toFixed(2)}</span>
        </div>
        `;

        document.getElementById('order-details').innerHTML = html;
    }
    </script>
</body>
</html>

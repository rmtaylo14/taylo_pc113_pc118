<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Delivery Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <style>
        .status-small {
            font-size: 0.8rem;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            text-transform: capitalize;
        }
        .status-pending {
            background-color: orange;
        }
        .status-to-be-delivered {
            background-color: red;
        }
        .status-delivered {
            background-color: green;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body class="bg-light">
    <div class="container py-5">
        <h3 class="mb-4">Delivery Orders</h3>
        <table class="table table-bordered table-striped" id="orders-table">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>CUSTOMER NAME</th>
                    <th>ADDRESS</th>
                    <th>AMOUNT (₱)</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody id="orders-body">
                <!-- Orders will be loaded here -->
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem('token');

        if (!token) {
            Swal.fire('Unauthorized', 'Please login first.', 'warning').then(() => {
                window.location.href = 'login.php';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadOrders();
        });

        async function loadOrders() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/delivery-grouped', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                if (!response.ok) throw new Error('Failed to fetch orders');
                const groupedData = await response.json();

                const tbody = document.getElementById('orders-body');
                tbody.innerHTML = '';

                if (!Array.isArray(groupedData) || groupedData.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center">No delivery orders found.</td></tr>`;
                    return;
                }

                groupedData.forEach(group => {
                    const user = group.user || {};
                    group.orders.forEach(order => {
                        const amount = order.items.reduce((sum, item) => sum + parseFloat(item.price), 0);
                        const status = order.status || 'pending';

                        if (status !== 'delivered' && status !== 'completed') {
                            const disableUpdate = status === 'to_be_delivered' ? 'disabled' : '';

                            tbody.insertAdjacentHTML('beforeend', `
                                <tr id="order-row-${order.id}">
                                    <td>${order.id}</td>
                                    <td>${user.name || 'N/A'}</td>
                                    <td>${user.address || 'N/A'}</td>
                                    <td>₱${amount.toFixed(2)}</td>
                                    <td id="status-${order.id}">
                                        <span class="status-small ${
                                            status === 'pending' ? 'status-pending' :
                                            status === 'to_be_delivered' ? 'status-to-be-delivered' :
                                            status === 'delivered' ? 'status-delivered' : ''
                                        }">${status}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="updateOrderStatus(${order.id})" ${disableUpdate}>Update</button>
                                        <button class="btn btn-sm btn-info" onclick="viewOrder(${order.id})">View</button>
                                    </td>
                                </tr>
                            `);
                        }
                    });
                });
            } catch (error) {
                console.error(error);
                Swal.fire('Error', error.message || 'Unable to load delivery orders.', 'error');
            }
        }

        async function updateOrderStatus(orderId) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/orders/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to update order status');
                }

                const updatedOrder = await response.json();
                const newStatus = updatedOrder.status || 'to_be_delivered';

                const statusCell = document.getElementById(`status-${orderId}`);
                statusCell.innerHTML = `
                    <span class="status-small ${
                        newStatus === 'pending' ? 'status-pending' :
                        newStatus === 'to_be_delivered' ? 'status-to-be-delivered' :
                        newStatus === 'delivered' ? 'status-delivered' : ''
                    }">${newStatus}</span>
                `;

                const updateBtn = document.querySelector(`#order-row-${orderId} button.btn-success`);
                if (newStatus === 'to_be_delivered') {
                    updateBtn.disabled = true;
                }

                if (newStatus === 'delivered' || newStatus === 'completed') {
                    document.getElementById(`order-row-${orderId}`).remove();
                    Swal.fire('Success', 'Order marked as delivered and removed from list.', 'success');
                } else {
                    Swal.fire('Success', `Order status updated to "${newStatus}".`, 'success');
                }
            } catch (error) {
                Swal.fire('Error', error.message || 'Failed to update order.', 'error');
            }
        }

        async function fetchOrderDetails(orderId, token) {
            try {
                const orderRes = await fetch(`http://127.0.0.1:8000/api/orders/${orderId}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                if (!orderRes.ok) throw new Error('Failed to fetch order details');

                const order = await orderRes.json();
                const user = order.user || {};

                return { order, user };
            } catch (error) {
                console.error('Error fetching order details:', error);
                throw error;
            }
        }

        function buildOrderQRContent({ order, user }) {
            const baseInfo = `Order ID: ${order.id}
Status: ${order.status}
Customer: ${user.name !== undefined && user.name !== null && user.name.trim() !== '' ? user.name : 'N/A'}
Address: ${user.address || 'N/A'}`;

            const itemsInfo = order.items.map(item => `
Item: ${item.name}
Price: ₱ ${parseFloat(item.price).toFixed(2)}`).join('\n');

            const totalAmount = order.items.reduce((sum, item) => sum + parseFloat(item.price), 0).toFixed(2);

            return `${baseInfo}\n${itemsInfo}\nTotal: ₱${totalAmount}`;
        }

        async function viewOrder(orderId) {
            const token = localStorage.getItem('token');

            try {
                const { order, user } = await fetchOrderDetails(orderId, token);
                const qrContent = buildOrderQRContent({ order, user });

                const qr = new QRious({
                    value: qrContent,
                    size: 500,
                    background: '#ffffff',
                    foreground: '#000000',
                    level: 'H'
                });

                Swal.fire({
                    title: `<span style="font-size: 20px;">Order #${order.id}</span>`,
                    html: `
                        <div style="text-align: center;">
                            <img src="${qr.toDataURL()}" 
                                alt="QR Code"
                                style="border: 8px solid #000000; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin-bottom: 10px; width: 300px; height: 300px;">
                            <pre style="text-align:left; white-space: pre-wrap; font-size: 14px; margin-top: 10px;">${qrContent}</pre>
                        </div>
                    `,
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: 400,
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                });

            } catch (error) {
                Swal.fire("Error", error.message || "Failed to load order details.", "error");
            }
        }
    </script>
</body>
</html>

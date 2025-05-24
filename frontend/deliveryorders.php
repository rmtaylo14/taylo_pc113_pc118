<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Delivery Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background-color: blue;
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

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="orderModalBody">
            <!-- Order details will be populated here -->
          </div>
        </div>
      </div>
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

                        // Show orders except those that are delivered or completed
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

                // Disable the Update button if newStatus is 'to_be_delivered'
                const updateBtn = document.querySelector(`#order-row-${orderId} button.btn-success`);
                if (newStatus === 'to_be_delivered') {
                    updateBtn.disabled = true;
                }

                // Remove row only if delivered or completed
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

        async function viewOrder(orderId) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/orders/${orderId}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                if (!response.ok) throw new Error('Failed to fetch order details');
                const order = await response.json();

                const modalBody = document.getElementById('orderModalBody');
                const itemsHtml = order.items.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>₱${parseFloat(item.price).toFixed(2)}</td>
                    </tr>
                `).join('');

                modalBody.innerHTML = `
                    <p><strong>Order ID:</strong> ${order.id}</p>
                    <p><strong>Status:</strong> ${order.status}</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                    <p><strong>Total Amount:</strong> ₱${order.items.reduce((sum, item) => sum + parseFloat(item.price), 0).toFixed(2)}</p>
                `;

                const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                orderModal.show();
            } catch (error) {
                console.error(error);
                Swal.fire('Error', error.message || 'Unable to load order details.', 'error');
            }
        }
    </script>
</body>
</html>

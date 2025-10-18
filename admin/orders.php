<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../conn.php';

// Fetch all orders with user details
$query = "SELECT o.*, u.name as customer_name, u.email, u.phone_number 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-100">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manage Orders</h1>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?php echo $order['order_id']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <p class="text-gray-600"><?php echo htmlspecialchars($order['email']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($order['phone_number']); ?></p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₱<?php echo number_format($order['total_amount'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        <?php
                                        switch ($order['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'processing':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            case 'shipped':
                                                echo 'bg-purple-100 text-purple-800';
                                                break;
                                            case 'delivered':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
                                </td>
                               
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <div class="flex items-center space-x-3">
        <button onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)"
            class="text-red-600 hover:text-red-900" title="View Details">
            <i class="fas fa-eye"></i>
        </button>
        
        <?php if ($order['status'] === 'pending'): ?>
            <button onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'processing')"
                class="text-green-600 hover:text-green-900" title="Mark as Processing">
                <i class="fas fa-cog"></i>
            </button>
        <?php endif; ?>
        
        <?php if ($order['status'] === 'processing'): ?>
            <button onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'shipped')"
                class="text-purple-600 hover:text-purple-900" title="Mark as Shipped">
                <i class="fas fa-truck"></i>
            </button>
        <?php endif; ?>
        
        <?php if ($order['status'] === 'shipped'): ?>
            <button onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'delivered')"
                class="text-green-600 hover:text-green-900" title="Mark as Delivered">
                <i class="fas fa-check-circle"></i>
            </button>
        <?php endif; ?>
        
        <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
            <button onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancelled')"
                class="text-red-600 hover:text-red-900" title="Cancel Order">
                <i class="fas fa-times"></i>
            </button>
        <?php endif; ?>
    </div>
</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function viewOrderDetails(orderId) {
            const modal = document.getElementById('orderModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div></div>';
            modal.classList.remove('hidden');
            
            fetch(`get_order_details.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    modalContent.innerHTML = `
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl font-bold">Order Details #${orderId}</h2>
                            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <h3 class="font-semibold mb-2">Customer Information</h3>
                                <p>${data.order.customer.name}</p>
                                <p>${data.order.customer.email}</p>
                                <p>${data.order.customer.phone}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-2">Order Information</h3>
                                <p>Date: ${data.order.created_at}</p>
                                <p>Status: ${data.order.status}</p>
                                <p>Payment Status: ${data.order.payment_status}</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    ${data.items.map(item => `
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <img class="h-10 w-10 rounded object-cover mr-3" src="../products/${item.image}" alt="${item.product_name}">
                                                    <div>${item.product_name}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">₱${item.price}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">${item.quantity}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">₱${item.subtotal}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-semibold">Total Amount:</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">₱${data.order.total_amount}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                })
                .catch(error => {
                    modalContent.innerHTML = `
                        <div class="text-center text-red-500">
                            <p>Error loading order details</p>
                        </div>
                    `;
                });
        }

      
function updateOrderStatus(orderId, status) {
    const statusMessages = {
        'processing': 'start processing',
        'shipped': 'mark as shipped',
        'delivered': 'mark as delivered',
        'cancelled': 'cancel'
    };

    if (!confirm(`Are you sure you want to ${statusMessages[status]} this order?`)) return;

    fetch('update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(`Order successfully ${status}`);
            location.reload();
        } else {
            alert(data.message || 'Error updating order status');
        }
    })
    .catch(error => {
        alert('Error updating order status');
        console.error('Error:', error);
    });
}


        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    <script>
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>
</body>
</html>
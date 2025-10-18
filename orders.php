<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data and orders
try {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $orders_query = "SELECT * FROM orders WHERE email = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $orders_query);
    mysqli_stmt_bind_param($stmt, "s", $user['email']);
    mysqli_stmt_execute($stmt);
    $orders = mysqli_stmt_get_result($stmt);
    $orders = mysqli_fetch_all($orders, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(25, 42, 86, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content-wrapper {
            min-height: calc(100vh - 80px);
            /* Adjust 80px based on your footer height */
            padding-bottom: 2rem;
        }
    </style>
</head>

<body class="bg-slate-900">
    <div class="content-wrapper">
        <div class="flex">
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex-1 ml-64 p-8">
                <!-- Header Section -->
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-slate-200">My Orders</h1>
                    <a href="index.php" class="flex items-center px-4 py-2 text-sm text-slate-200 hover:text-red-400 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Back to Home
                    </a>
                </div>

                <!-- Orders Grid - Adjusted for smaller cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order):
                            // Fetch the first product image for this order
                            $order_id = $order['order_id'];
                            $image_query = "SELECT p.image FROM order_items oi 
                              JOIN products p ON oi.product_id = p.product_id 
                              WHERE oi.order_id = ? LIMIT 1";
                            $stmt = mysqli_prepare($conn, $image_query);
                            mysqli_stmt_bind_param($stmt, "i", $order_id);
                            mysqli_stmt_execute($stmt);
                            $image_result = mysqli_stmt_get_result($stmt);
                            $product_image = mysqli_fetch_assoc($image_result)['image'] ?? 'default.jpg';
                        ?>
                            <div class="glass-card rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <!-- reduced image height -->
                                <div class="relative h-32">
                                    <img src="products/<?php echo htmlspecialchars($product_image); ?>"
                                        alt="Order Preview"
                                        class="w-full h-full object-cover">
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php
                                                                                                $statusColors = [
                                                                                                    'pending' => 'bg-yellow-500 text-white',
                                                                                                    'processing' => 'bg-red-500 text-white',
                                                                                                    'shipped' => 'bg-purple-500 text-white',
                                                                                                    'delivered' => 'bg-green-500 text-white',
                                                                                                    'cancelled' => 'bg-red-500 text-white'
                                                                                                ];
                                                                                                echo $statusColors[$order['status']] ?? 'bg-slate-500/10 text-slate-400';
                                                                                                ?>">
                                            <strong><?php echo ucfirst($order['status']); ?></strong>
                                        </span>
                                    </div>
                                </div>

                                <!-- reduced padding and spacing -->
                                <div class="p-4 space-y-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-base font-semibold text-slate-200">Order #<?php echo $order['order_id']; ?></h3>
                                            <p class="text-xs text-slate-400"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                        </div>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php
                                                                                                $paymentColors = [
                                                                                                    'pending' => 'bg-yellow-500 text-white',
                                                                                                    'paid' => 'bg-green-500/10 text-green-500',
                                                                                                    'failed' => 'bg-red-500/10 text-red-500'
                                                                                                ];
                                                                                                echo $paymentColors[$order['payment_status']] ?? 'bg-slate-500/10 text-slate-400';
                                                                                                ?>">
                                            <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center border-t border-slate-700 pt-3">
                                        <div class="text-slate-200">
                                            <span class="text-xs">Total:</span>
                                            <p class="text-sm text-yellow-400 font-bold">₱<?php echo number_format($order['total_amount'], 2); ?></p>
                                        </div>
                                        <!-- Replace the existing Details link with this -->
                                        <a href="javascript:void(0)"
                                            onclick="viewDetails(<?php echo $order['order_id']; ?>)"
                                            class="flex items-center text-sm text-red-500 hover:text-red-400 transition-colors">
                                            Details
                                            <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-16 glass-card rounded-xl">
                            <i class="fas fa-shopping-bag text-slate-400 text-6xl mb-4"></i>
                            <p class="text-slate-400 text-lg mb-4">No orders found</p>
                            <a href="Products.php" class="inline-flex items-center px-6 py-3 text-red-500 hover:text-red-400 transition-colors">
                                Start shopping now
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this before closing body tag -->
    <!-- Modal -->
    <div id="orderModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black opacity-50" id="modalBackdrop"></div>

            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full mx-auto z-50">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-semibold text-slate-200">
                            Order Details #<span id="modalOrderId"></span>
                        </h3>
                        <button class="text-slate-400 hover:text-slate-200" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div id="modalContent" class="space-y-4">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before closing body tag -->
    <script>
        function viewDetails(orderId) {
            const modal = document.getElementById('orderModal');
            const modalContent = document.getElementById('modalContent');
            const modalOrderId = document.getElementById('modalOrderId');

            // Show loading state
            modalContent.innerHTML = `
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div>
        </div>
    `;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Fetch order details
            fetch(`get_order_details.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    modalOrderId.textContent = orderId;

                    // Build the content
                    let html = `
                <div class="bg-white rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 gap-4 text-slate-300">
                        <div>
                            <p class="text-sm text-slate-400">Order Date</p>
                            <p class="font-medium">${data.order.created_at}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Status</p>
                            <span class="px-2 py-1 rounded-full text-xs font-medium inline-block
                                ${data.order.status === 'pending' ? 'bg-yellow-500 text-white' : 
                                  data.order.status === 'processing' ? 'bg-red-500 text-white' :
                                  data.order.status === 'shipped' ? 'bg-purple-500 text-white' :
                                  data.order.status === 'delivered' ? 'bg-green-500 text-white' :
                                  'bg-red-500 text-white'}">
                                ${data.order.status.toUpperCase()}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-slate-200">Order Items</h4>
                    <div class="divide-y divide-slate-700">
                        ${data.items.map(item => `
                            <div class="py-3 flex items-center space-x-4">
                                <img src="products/${item.image}" alt="${item.product_name}" 
                                     class="w-16 h-16 rounded object-cover">
                                <div class="flex-1">
                                    <h5 class="text-slate-200 font-medium">${item.product_name}</h5>
                                    <p class="text-slate-400 text-sm">Quantity: ${item.quantity}</p>
                                    <p class="text-slate-400 text-sm">₱${item.price}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="border-t border-slate-700 mt-4 pt-4">
                    <div class="flex justify-between text-slate-200">
                        <span class="font-medium">Total Amount:</span>
                        <span class="font-bold">₱${data.order.total_amount}</span>
                    </div>
                </div>
            `;

                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    modalContent.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <i class="fas fa-exclamation-circle text-xl mb-2"></i>
                    <p>Error loading order details.</p>
                </div>
            `;
                });
        }

        function closeModal() {
            const modal = document.getElementById('orderModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal when clicking outside
        document.getElementById('modalBackdrop').addEventListener('click', closeModal);

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

    <!-- Fixed Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 py-4 text-center w-full">
        <div class="container mx-auto max-w-7xl px-4">
            <p class="text-slate-400 text-sm">&copy; <?php echo date('Y'); ?> JMYBA. All Rights Reserved.</p>
            <div class="mt-2 space-x-4">
                <a href="#" class="text-slate-400 hover:text-red-500 transition-colors duration-200">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="text-slate-400 hover:text-red-500 transition-colors duration-200">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-slate-400 hover:text-red-500 transition-colors duration-200">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>
        </div>
    </footer>
</body>

</html>
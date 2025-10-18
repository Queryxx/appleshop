<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../conn.php';
// Get total products count
$query = "SELECT COUNT(*) as total FROM products";
$result = mysqli_query($conn, $query);
$products_count = mysqli_fetch_assoc($result)['total'];

// Get total customers count
$query = "SELECT COUNT(*) as total FROM users";
$result = mysqli_query($conn, $query);
$customers_count = mysqli_fetch_assoc($result)['total'];

// Get total orders count
$query = "SELECT COUNT(*) as total FROM orders";
$result = mysqli_query($conn, $query);
$orders_count = mysqli_fetch_assoc($result)['total'];
$query = "SELECT o.order_id, COALESCE(o.total_amount, 0) as total_amount, o.status, o.created_at, 
         u.name as customer_name
  FROM orders o 
  JOIN users u ON o.user_id = u.user_id 
  ORDER BY o.created_at DESC 
  LIMIT 5";

$recent_orders = mysqli_query($conn, $query);

// Calculate total revenue
$query = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE status != 'Canceled'";
$result = mysqli_query($conn, $query);
$total_revenue = mysqli_fetch_assoc($result)['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-slate-100">

    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 p-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold mt-5 text-gray-800">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
            <p class="text-gray-600">Here's what's happening with your store today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Products Card -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-500/10 text-red-500">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-semibold text-gray-500">Products</h3>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($products_count); ?></p>
                    </div>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500/10 text-green-500">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-semibold text-gray-500">Customers</h3>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($customers_count); ?></p>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-500/10 text-purple-500">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-semibold text-gray-500">Orders</h3>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($orders_count); ?></p>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-500/10 text-yellow-500">
                        <i class="fas fa-peso-sign text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-semibold text-gray-500">Revenue</h3>
                        <p class="text-2xl font-bold text-gray-900">₱<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                <a href="orders.php" class="text-red-600 hover:text-red-800 text-sm font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if (mysqli_num_rows($recent_orders) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #<?php echo $order['order_id']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($order['customer_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                        <?php
                                        switch ($order['status']) {
                                            case 'Pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'Shipped':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            case 'Delivered':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'Canceled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱<?php echo number_format($order['total_amount'] ?? 0, 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="view_order.php?id=<?php echo $order['order_id']; ?>"
                                            class="text-red-600 hover:text-red-800 font-medium">View Details</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
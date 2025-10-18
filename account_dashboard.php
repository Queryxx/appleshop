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

// Fetch user data
try {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $orders_query = "SELECT * FROM orders WHERE email = ? ORDER BY created_at DESC LIMIT 5";
    $stmt = mysqli_prepare($conn, $orders_query);
    mysqli_stmt_bind_param($stmt, "s", $user['email']);
    mysqli_stmt_execute($stmt);
    $recent_orders = mysqli_stmt_get_result($stmt);
    $recent_orders = mysqli_fetch_all($recent_orders, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: error.php");
    exit();
}

// Rest of your HTML remains the same
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard - JMYBA Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       .glass-card {
    background: rgba(25, 42, 86, 0.7); /* Dark Navy red */
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

    </style>
</head>

<body class="bg-slate-900">
    <div class="flex min-h-screen">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 ml-64 p-8">
            <!-- Header Section with Welcome and Back Button -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-700">Welcome back, <?php echo htmlspecialchars($user['name']); ?></h1>
                <a href="index.php" class="flex items-center px-4 py-2 text-sm text-slate-200 hover:text-red-800 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Back to Home
                </a>
            </div>
            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Orders Summary Card -->
                <div class="glass-card p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-200 text-lg font-medium">Recent Orders</h3>
                        <div class="p-2 bg-blue-500/10 rounded-lg">
                            <i class="fas fa-shopping-bag text-gray-200"></i>
                        </div>
                    </div>
                    <p class="text-white text-2xl font-bold"><?php echo count($recent_orders); ?></p>
                </div>

                <!-- Wishlist Items Card -->
                <div class="glass-card p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-200 text-lg font-medium">Wishlist Items</h3>
                        <div class="p-2 bg-blue-500/10 rounded-lg">
                            <i class="fas fa-heart text-gray-200"></i>
                        </div>
                    </div>
                    <p class="text-white text-2xl font-bold">0</p>
                </div>

                <!-- Profile Status Card -->
                <div class="glass-card p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-200 text-lg font-medium">Profile Status</h3>
                        <div class="p-2 bg-blue-500/10 rounded-lg">
                            <i class="fas fa-user-check text-gray-200"></i>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                        <p class="text-slate-200">Verified</p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="glass-card rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-slate-700">
                    <h2 class="text-xl font-medium text-white">Recent Orders</h2>
                </div>

                <div class="p-6">
                    <?php if (count($recent_orders) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-slate-300">
                                <thead class="text-slate-200 border-b border-slate-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium">Order ID</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium">Date</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium">Amount</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium">Status</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium">Payment</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700">
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr class="hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                                            <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td class="px-6 py-4">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                <?php
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
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                <?php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-500 text-white',
                                            'paid' => 'bg-green-500/10 text-green-500',
                                            'failed' => 'bg-red-500/10 text-red-500'
                                        ];
                                        echo $paymentColors[$order['payment_status']] ?? 'bg-slate-500/10 text-slate-400';
                                ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="order_details.php?id=<?php echo $order['order_id']; ?>"
                                                    class="text-gray-200 hover:text-red-200 transition-colors">
                                                    View Details →
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-slate-400 text-center py-8">No orders found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
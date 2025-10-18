<?php
include 'conn.php';
session_start();

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

$sql = "SELECT o.*, oi.*, p.product_name, p.image 
        FROM orders o 
        JOIN order_items oi ON o.order_id = oi.order_id 
        JOIN products p ON oi.product_id = p.product_id 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order = null;
$items = [];
while ($row = $result->fetch_assoc()) {
    if (!$order) {
        $order = [
            'order_id' => $row['order_id'],
            'total_amount' => $row['total_amount'],
            'shipping_address' => $row['shipping_address'],
            'status' => $row['status'],
            'created_at' => $row['created_at']
        ];
    }
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-50">
    <?php include 'nav.php'; ?>

    <div class="container mx-auto mt-8 p-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-8">
                <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                <h1 class="text-2xl font-bold text-gray-800">Order Confirmed!</h1>
                <p class="text-gray-600">Order #<?php echo $order_id; ?></p>
            </div>

            <div class="border-t border-b py-4 mb-4">
                <h2 class="font-semibold mb-2">Order Details</h2>
                <p class="text-sm text-gray-600">Date: <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                <p class="text-sm text-gray-600">Status: <?php echo ucfirst($order['status']); ?></p>
            </div>

            <div class="space-y-4 mb-4">
                <?php foreach ($items as $item): ?>
                    <div class="flex items-center">
                        <img src="products/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                             class="w-16 h-16 object-cover rounded">
                        <div class="ml-4">
                            <h3 class="font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p class="text-sm text-gray-600">
                                Quantity: <?php echo $item['quantity']; ?> × 
                                ₱<?php echo number_format($item['price'], 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center font-semibold">
                    <span>Total Amount:</span>
                    <span class="text-xl text-red-500">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="index.php" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script>
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>
</body>
</html>
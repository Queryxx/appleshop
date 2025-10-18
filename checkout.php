<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // redirect to login if not logged in
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? 1;

// Fetch cart items
$sql = "SELECT c.*, p.product_name, p.price, p.image, p.discount 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user data
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
$total = 0;
$items = [];

while ($item = $result->fetch_assoc()) {
    $price = $item['price'];
    if (!is_null($item['discount']) && $item['discount'] > 0) {
        $discount_amount = $price * ($item['discount'] / 100);
        $price = $price - $discount_amount;
    }
    $item_total = $price * $item['quantity'];
    $total += $item_total;
    $items[] = $item;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-50">
    <?php include 'nav.php'; ?>

    <div class="container mx-auto mt-8 p-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

        <?php if (!empty($items)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-4">
                        <?php foreach ($items as $item):
                            $price = $item['price'];
                            if (!is_null($item['discount']) && $item['discount'] > 0) {
                                $discount_amount = $price * ($item['discount'] / 100);
                                $price = $price - $discount_amount;
                            }
                            $item_total = $price * $item['quantity'];
                        ?>
                            <div class="flex items-center border-b pb-4">
                                <img src="products/<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                    class="w-16 h-16 object-cover rounded">
                                <div class="ml-4 flex-grow">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                    <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                                    <p class="text-sm font-medium">₱<?php echo number_format($item_total, 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center font-semibold">
                                <span>Total:</span>
                                <span class="text-xl text-red-500">₱<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                    <form id="checkoutForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="name" required
                                value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required
                                value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" required
                                value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                            <textarea name="address" required rows="3"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                        </div>
                        <!-- Replace the payment method section -->
                        <div class="border-t pt-4 mt-4">
                            <h3 class="text-lg font-semibold mb-4">Payment Method</h3>
                            <div class="space-y-4">
                                <div class="relative">
                                    <select name="payment_method" id="payment_method" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 appearance-none bg-white">
                                        <option value="cod">Cash on Delivery</option>
                                        <option value="gcash">GCash</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GCash Payment Details Section -->
                        <div id="paymentDetails" class="hidden space-y-4 border-t pt-4 mt-4">
                            <div id="gcashDetails">
                                <h3 class="text-lg font-semibold mb-4">GCash Payment Details</h3>
                                <div class="bg-red-50 p-4 rounded-lg">
                                    <p class="font-medium">GCash Number: 09123456789</p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        1. Send payment to the GCash number above<br>
                                        2. Take a screenshot of your payment<br>
                                        3. Upload the screenshot below
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload Payment Screenshot
                                    </label>
                                    <input type="file" name="payment_proof" accept="image/*"
                                        class="w-full px-4 py-2 border rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- Replace the payment method script -->
                        <script>
                            document.getElementById('payment_method').addEventListener('change', function() {
                                const paymentDetails = document.getElementById('paymentDetails');

                                // Show/hide payment details section based on selection
                                paymentDetails.classList.toggle('hidden', this.value === 'cod');
                            });
                        </script>

                        <button type="submit"
                            class="w-full bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors">
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <h2 class="text-2xl font-semibold text-gray-600 mb-4">Your cart is empty</h2>
                <a href="index.php" class="text-red-500 hover:text-red-700">
                    Continue Shopping<i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `order_confirmation.php?order_id=${data.order_id}`;
                    } else {
                        alert(data.message || 'Error processing order');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing order');
                });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>
<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['product_id'] ?? null;

if (!$product_id) {
    header('Location: index.php');
    exit();
}

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: index.php');
    exit();
}
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
$quantity = max(1, $quantity); // Ensure minimum quantity is 1

// Modify the price calculation to include quantity
$unit_price = $product['price'];
if (!is_null($product['discount']) && $product['discount'] > 0) {
    $discount_amount = $unit_price * ($product['discount'] / 100);
    $unit_price = $unit_price - $discount_amount;
}
$total_price = $unit_price * $quantity;
// Calculate price with discount
$price = $product['price'];
if (!is_null($product['discount']) && $product['discount'] > 0) {
    $discount_amount = $price * ($product['discount'] / 100);
    $price = $price - $discount_amount;
}

// Fetch user data
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Now - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-50">
    <?php include 'nav.php'; ?>

    <div class="container mx-auto mt-8 p-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Buy Now</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                <div class="space-y-4">
                    <!-- Replace the existing Order Summary content with this -->
                    <div class="flex items-center border-b pb-4">
                        <img src="products/<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                            class="w-16 h-16 object-cover rounded">
                        <div class="ml-4 flex-grow">
                            <h3 class="font-semibold"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <div class="flex items-center space-x-4 mt-2">
                                <div class="flex items-center border rounded-lg">
                                    <button type="button" onclick="updateQuantity(-1)"
                                        class="px-3 py-1 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" name="quantity" value="<?php echo $quantity; ?>"
                                        min="1" class="w-16 text-center border-x py-1"
                                        onchange="updateQuantity(0)">
                                    <button type="button" onclick="updateQuantity(1)"
                                        class="px-3 py-1 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <p class="text-sm font-medium">₱<?php echo number_format($unit_price, 2); ?> each</p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center font-semibold">
                            <span>Total:</span>
                            <span class="text-xl text-red-500">₱<span id="totalPrice"><?php echo number_format($total_price, 2); ?></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                <form id="buyNowForm" class="space-y-4">
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
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const paymentDetails = document.getElementById('paymentDetails');
            paymentDetails.classList.toggle('hidden', this.value === 'cod');
        });

        document.getElementById('buyNowForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('process_buy_now.php', {
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
    <script>
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>

    <?php include 'footer.php'; ?>
    <script>
        document.getElementById('buyNowForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Add product_id and quantity
            formData.append('product_id', '<?php echo $product_id; ?>');
            formData.append('quantity', document.getElementById('quantity').value);

            // Validate payment proof for GCash
            if (document.getElementById('payment_method').value === 'gcash') {
                const paymentProof = formData.get('payment_proof');
                if (!paymentProof || paymentProof.size === 0) {
                    alert('Please upload your GCash payment screenshot');
                    return;
                }
            }

            fetch('process_buy_now.php', {
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

        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            const currentQuantity = parseInt(quantityInput.value) || 1;
            const newQuantity = Math.max(1, currentQuantity + change);
            quantityInput.value = newQuantity;

            // Update total price
            const unitPrice = <?php echo $unit_price; ?>;
            const totalPrice = unitPrice * newQuantity;
            document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
        }

        // Modify the existing form submission handler
        document.getElementById('buyNowForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const quantity = document.getElementById('quantity').value;
            formData.append('quantity', quantity);

            fetch('process_buy_now.php', {
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

        // Add input validation for quantity
        document.getElementById('quantity').addEventListener('input', function(e) {
            let value = parseInt(this.value) || 1;
            value = Math.max(1, value);
            this.value = value;
            updateQuantity(0);
        });

        // Payment method toggle
        document.getElementById('payment_method').addEventListener('change', function() {
            const paymentDetails = document.getElementById('paymentDetails');
            const paymentProofInput = document.querySelector('input[name="payment_proof"]');

            paymentDetails.classList.toggle('hidden', this.value === 'cod');

            if (this.value === 'cod') {
                paymentProofInput.removeAttribute('required');
            } else {
                paymentProofInput.setAttribute('required', 'required');
            }
        });
    </script>
</body>

</html>
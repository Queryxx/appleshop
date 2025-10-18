<?php
include 'conn.php';
session_start();

// Fetch products from database
$sql = "SELECT * FROM products WHERE status = 'available'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form -  JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-50">
    <?php include 'nav.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Place Your Order</h1>

            <form id="orderForm" class="space-y-8">
                <!-- Customer Information -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" name="phone" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" required rows="2"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Product Selection -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Select Products</h2>
                    <div class="space-y-4">
                        <?php while ($product = $result->fetch_assoc()): 
                            $price = $product['price'];
                            if (!is_null($product['discount']) && $product['discount'] > 0) {
                                $discount_amount = $price * ($product['discount'] / 100);
                                $final_price = $price - $discount_amount;
                            } else {
                                $final_price = $price;
                            }
                        ?>
                            <div class="flex items-center border-b pb-4">
                                <img src="products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                     class="w-16 h-16 object-cover rounded">
                                <div class="ml-4 flex-grow">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <div class="text-sm">
                                        <?php if (!is_null($product['discount']) && $product['discount'] > 0): ?>
                                            <span class="text-gray-500 line-through">₱<?php echo number_format($price, 2); ?></span>
                                            <span class="text-red-500 ml-2">₱<?php echo number_format($final_price, 2); ?></span>
                                            <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs ml-2">-<?php echo $product['discount']; ?>%</span>
                                        <?php else: ?>
                                            <span class="text-gray-700">₱<?php echo number_format($price, 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <input type="number" name="quantity[<?php echo $product['product_id']; ?>]" 
                                           min="0" value="0" 
                                           class="w-20 px-3 py-2 border rounded-lg"
                                           onchange="updateTotal()">
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="flex justify-between items-center text-lg font-semibold">
                        <span>Total Amount:</span>
                        <span id="totalAmount" class="text-red-500">₱0.00</span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                        class="bg-red-500 text-white px-8 py-3 rounded-lg hover:bg-red-600 transition-colors">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function updateTotal() {
        const quantities = document.querySelectorAll('input[name^="quantity"]');
        let total = 0;

        quantities.forEach(input => {
            const productId = input.name.match(/\d+/)[0];
            const quantity = parseInt(input.value) || 0;
            const priceElement = input.closest('.flex').querySelector('.text-red-500, .text-gray-700');
            const price = parseFloat(priceElement.textContent.replace('₱', '').replace(',', ''));
            
            total += price * quantity;
        });

        document.getElementById('totalAmount').textContent = '₱' + total.toFixed(2);
    }

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('process_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully!');
                window.location.href = 'orders.php';
            } else {
                alert(data.message || 'Error placing order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error placing order');
        });
    });
    </script>
    <script>
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
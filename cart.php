<?php
include 'conn.php';
session_start();

// Initialize user_id (you'll need to set this when user logs in)
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for testing

// Fetch cart items
$sql = "SELECT c.*, p.product_name, p.price, p.image, p.discount, p.status 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add custom checkbox styles */
        input[type="checkbox"] {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'nav.php'; ?>

    <div class="container mx-auto mt-8 p-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="overflow-x-auto">

                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-4">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                                </th>
                                <th class="text-left py-4">Product</th>
                                <th class="text-center py-4">Price</th>
                                <th class="text-center py-4">Quantity</th>
                                <th class="text-center py-4">Total</th>
                                <th class="text-center py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $result->fetch_assoc()):
                                $price = $item['price'];
                                if (!is_null($item['discount']) && $item['discount'] > 0) {
                                    $discount_amount = $price * ($item['discount'] / 100);
                                    $price = $price - $discount_amount;
                                }
                                $item_total = $price * $item['quantity'];
                                $total += $item_total;
                            ?>
                                <tr class="border-b">
                                    <td class="py-4">
                                        <input type="checkbox"
                                            name="selected_items[]"
                                            value="<?php echo $item['cart_id']; ?>"
                                            data-price="<?php echo $item_total; ?>"
                                            class="product-checkbox rounded border-gray-300 text-red-500 focus:ring-red-500">
                                    </td>
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <img src="products/<?php echo htmlspecialchars($item['image']); ?>"
                                                alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                class="w-16 h-16 object-cover rounded">
                                            <div class="ml-4">
                                                <h3 class="font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                                <?php if (!is_null($item['discount']) && $item['discount'] > 0): ?>
                                                    <span class="text-sm text-red-500">-<?php echo $item['discount']; ?>% OFF</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">₱<?php echo number_format($price, 2); ?></td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center">
                                            <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'decrease')"
                                                class="bg-gray-200 px-2 py-1 rounded-l">-</button>
                                            <span class="px-4 py-1 bg-gray-100"><?php echo $item['quantity']; ?></span>
                                            <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'increase')"
                                                class="bg-gray-200 px-2 py-1 rounded-r">+</button>
                                        </div>
                                    </td>
                                    <td class="text-center">₱<?php echo number_format($item_total, 2); ?></td>
                                    <td class="text-center">
                                        <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)"
                                            class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right py-4 font-bold">Selected Total:</td>
                                <td class="text-center py-4 font-bold">₱<span id="selected-total">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Replace the checkout button section -->
                    <div class="mt-8 flex justify-between items-center">
                        <a href="index.php" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                        </a>
                        <button onclick="checkoutSelected()"
                            id="checkout-btn"
                            disabled
                            class="bg-red-500 text-white px-8 py-3 rounded-full hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Proceed to Checkout (<span id="selected-count">0</span> items)
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-16">
                    <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-semibold text-gray-600 mb-4">Your cart is empty</h2>
                    <a href="index.php" class="text-red-500 hover:text-red-700">
                        Start shopping now<i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            <?php endif; ?>
            </div>

            <script>
                function updateQuantity(cartId, action) {
                    fetch('update_cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `cart_id=${cartId}&action=${action}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        });
                }

                function removeFromCart(cartId) {
                    if (confirm('Are you sure you want to remove this item?')) {
                        fetch('update_cart.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `cart_id=${cartId}&action=remove`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert(data.message);
                                }
                            });
                    }
                }

                function checkoutSelected() {
                    const selectedItems = [...document.querySelectorAll('.product-checkbox:checked')]
                        .map(checkbox => checkbox.value);

                    if (selectedItems.length === 0) {
                        alert('Please select items to checkout');
                        return;
                    }

                    // Store selected items in session storage
                    sessionStorage.setItem('selectedItems', JSON.stringify(selectedItems));
                    window.location.href = 'checkout.php';
                }
                document.addEventListener('DOMContentLoaded', function() {
                    const selectAllCheckbox = document.getElementById('selectAll');
                    const productCheckboxes = document.querySelectorAll('.product-checkbox');
                    const checkoutBtn = document.getElementById('checkout-btn');
                    const selectedTotalSpan = document.getElementById('selected-total');
                    const selectedCountSpan = document.getElementById('selected-count');

                    function updateSelectedTotal() {
                        let total = 0;
                        let count = 0;
                        productCheckboxes.forEach(checkbox => {
                            if (checkbox.checked) {
                                total += parseFloat(checkbox.dataset.price);
                                count++;
                            }
                        });
                        selectedTotalSpan.textContent = total.toFixed(2);
                        selectedCountSpan.textContent = count;
                        checkoutBtn.disabled = count === 0;
                    }

                    selectAllCheckbox.addEventListener('change', function() {
                        productCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateSelectedTotal();
                    });

                    productCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            selectAllCheckbox.checked = [...productCheckboxes].every(c => c.checked);
                            updateSelectedTotal();
                        });
                    });

                   
                });
            </script>
</body>

</html>
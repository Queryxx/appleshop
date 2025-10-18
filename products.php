<section id="products" class="container mx-auto mt-8 p-7 products-iphone">
    
    <h2 class="text-3xl font-semibold text-gray-900 text-center mb-6">Featured iPhones</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
include 'conn.php';

// Fetch products from database
$sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 8";
$result = $conn->query($sql);

if ($result->num_rows > 0):
    while ($product = $result->fetch_assoc()):
        // Calculate discounted price if discount exists
        $price = $product['price'];
        $final_price = $price;
        if (!is_null($product['discount']) && $product['discount'] > 0) {
            $discount_amount = $price * ($product['discount'] / 100);
            $final_price = $price - $discount_amount;
        }
        $is_not_available = $product['status'] === 'not_available';
        $has_discount = !is_null($product['discount']) && $product['discount'] > 0;
?>
    <div class="bg-white p-4 rounded-2xl shadow-lg text-center group hover:shadow-2xl transition-shadow<?= $is_not_available ? ' opacity-50 grayscale' : '' ?> product-card">
        <div class="relative overflow-hidden">
            <img src="products/<?= $product["image"] ? htmlspecialchars($product["image"]) : 'placeholder.jpg' ?>" 
                 alt="<?= htmlspecialchars($product["product_name"]) ?>" 
                 class="w-full h-48 object-cover rounded transition-transform duration-300 group-hover:scale-105">
            
            <?php if ($has_discount): ?>
            <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-full text-sm">
                -<?= $product['discount'] ?>%
            </div>
            <?php endif; ?>

            <?php if ($is_not_available): ?>
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
               <i data-feather="package"></i>
                <span class="text-white text-lg font-bold absolute bottom-4">Out of Stock</span>
            </div>
            <?php endif; ?>
        </div>

        <h3 class="text-lg font-semibold mt-2 text-gray-900"><?= htmlspecialchars($product["product_name"]) ?></h3>
        
        <div class="mt-2">
            <?php if ($has_discount): ?>
            <span class="text-gray-500 line-through text-sm">₱<?= number_format($price, 2) ?></span>
            <?php endif; ?>
            <p class="text-gray-900 text-xl font-semibold">₱<?= number_format($final_price, 2) ?></p>
        </div>

        <p class="text-gray-600 text-sm mt-2 h-12 overflow-hidden"><?= htmlspecialchars($product["description"]) ?></p>

        <div class="flex gap-2 mt-3<?= $is_not_available ? ' opacity-50' : '' ?>">
        <?php
$loggedIn = isset($_SESSION['user_id']); // Change this based on your session variable for login
?>

<button onclick="<?php echo $loggedIn ? "window.location.href='checkoutform.php?product_id=" . $product["product_id"] . "'" : "window.location.href='login.php'"; ?>" 
    class="flex-1 bg-black text-white px-3 py-2 rounded-full shadow hover:bg-gray-900 transition-colors flex items-center justify-center space-x-2<?php echo $product['status'] === 'not_available' ? ' cursor-not-allowed' : ''; ?>"
    <?php echo $product['status'] === 'not_available' ? ' disabled' : ''; ?>>
            <i class="fas fa-credit-card"></i>
    <span class="text-sm">Buy</span>
</button>

<button onclick="<?php echo $loggedIn ? "addToCart(" . $product["product_id"] . ")" : "window.location.href='login.php'"; ?>" 
    class="flex-1 bg-gray-200 text-gray-900 px-3 py-2 rounded-full shadow hover:bg-gray-300 transition-colors flex items-center justify-center space-x-2<?php echo $product['status'] === 'not_available' ? ' cursor-not-allowed' : ''; ?>"
    <?php echo $product['status'] === 'not_available' ? ' disabled' : ''; ?>>
    <i class="fas fa-cart-plus"></i>
    <span class="text-sm">Add</span>
</button>

        </div>
    </div>
<?php 
    endwhile;
else: 
?>
    <div class="col-span-full text-center text-gray-500">No products found</div>
<?php 
endif; 
?>
        </div>
        <div class="mt-8 text-center ">
        <a href="all_products.php" class="bg-black text-white px-8 py-3 rounded-full hover:bg-gray-900 transition-colors inline-flex items-center space-x-2">
            <span>See All Products</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
 
    </section>
    <style>
    .grayscale { filter: grayscale(50%); }
    /* product section iPhone-like styles */
    .products-iphone, .products-iphone *{ font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
    .product-card{ border: 1px solid rgba(0,0,0,0.04); background: rgba(255,255,255,0.98); border-radius: 18px; }
    .product-card img{ border-top-left-radius: 12px; border-top-right-radius: 12px; }
    </style>
    <script>
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>

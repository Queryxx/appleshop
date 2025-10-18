<?php

include 'conn.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_info = null;

if ($user_id) {
    $user_query = $conn->query("SELECT name, profile_picture FROM users WHERE user_id = $user_id");
    $user_info = $user_query->fetch_assoc();
}
?>
<nav class="iphone-nav sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center p-4">
        <!-- Logo Section (unchanged) -->
        <div class="flex items-center group">
            <img src="./image/log.png" alt="iPhone Logo" class="h-14 w-14 mr-3 rounded-full object-cover transform transition-transform group-hover:scale-105 shadow-sm">
            <a href="index.php" class="text-2xl font-semibold text-gray-900 group-hover:text-gray-800 transition">
                iPhone <span class="text-gray-600">Shop</span>
            </a>
        </div>

        <!-- Navigation Links (Desktop) -->
        <ul class="hidden md:flex space-x-8 items-center">
            <li><a href="index.php" class="nav-link text-gray-600 hover:text-gray-800 transition font-medium relative flex items-center space-x-2"><i class="fas fa-home fa-lg"></i><span>Home</span></a></li>
            <li><a href="index.php#products" class="nav-link text-gray-600 hover:text-gray-800 transition font-medium relative flex items-center space-x-2"><i class="fas fa-store fa-lg"></i><span>Products</span></a></li>
            <li><a href="index.php#about" class="nav-link text-gray-600 hover:text-gray-800 transition font-medium relative flex items-center space-x-2"><i class="fas fa-info-circle fa-lg"></i><span>About</span></a></li>
            <li><a href="index.php#contact" class="nav-link text-gray-600 hover:text-gray-800 transition font-medium relative flex items-center space-x-2"><i class="fas fa-phone fa-lg"></i><span>Contact</span></a></li>
        </ul>

        <!-- Right Side Icons -->
        <div class="flex items-center space-x-6">
            <!-- Order Form and Cart (unchanged) -->
            <a href="order-form.php" class="text-gray-700 hover:text-gray-900 transition transform hover:scale-105 flex items-center space-x-2 bg-white/60 backdrop-blur-md px-4 py-2 rounded-full shadow-sm">
                <i class="fas fa-shop fa-lg"></i>
                <span class="hidden md:inline font-medium">Buy</span>
            </a>

            <!-- Cart -->
            <a href="<?php echo $user_id ? 'cart.php' : 'login.php'; ?>" class="text-gray-700 hover:text-gray-900 transition relative transform hover:scale-105 flex items-center"
                <?php if (!$user_id): ?>title="Please login to view cart" <?php endif; ?>>
                <i class="fas fa-shopping-cart fa-lg"></i>
                <?php if ($user_id): ?>
                    <?php
                    $cart_count = $conn->query("SELECT COUNT(DISTINCT product_id) as count FROM cart WHERE user_id = $user_id")->fetch_assoc()['count'] ?? 0;
                    ?>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            <!-- Replace the Profile Section with this code -->
            <?php if ($user_id && $user_info): ?>
                <!-- Profile Section (if logged in) -->
                <div class="hidden md:flex items-center space-x-2 relative">
                    <div class="relative">
                        <button id="profile-button" class="flex items-center space-x-2 focus:outline-none">
                            <img src="uploads/profile_pictures/<?php echo $user_info['profile_picture'] ? $user_info['profile_picture'] : 'default-profile.png'; ?>"
                                alt="Profile Picture"
                                class="w-10 h-10 rounded-full object-cover">
                            <span class="font-medium text-gray-700"><?php echo htmlspecialchars($user_info['name']); ?></span>
                            <i class="fas fa-chevron-down text-gray-500 text-sm ml-1"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white/90 backdrop-blur-lg rounded-xl shadow-xl border border-white py-1 hidden">
                            <a href="account_dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> My Profile
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Login Button (if not logged in) -->
                <a href="login.php" class="hidden md:flex bg-black text-white px-5 py-2 rounded-full transition duration-300 items-center transform hover:scale-105 hover:shadow-md">
                    <i class="fas fa-user mr-2"></i>
                    <span>Login</span>
                </a>
            <?php endif; ?>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-red-500 transition transform hover:scale-105">
                <i class="fas fa-bars fa-lg"></i>
            </button>
        </div>
    </div>

    <!-- Replace the Mobile Menu content section -->
    <div id="mobile-menu" class="hidden md:hidden fixed inset-0 z-50">
        <!-- Dark overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50" id="mobile-menu-overlay"></div>

        <!-- Menu content -->
        <div class="fixed right-0 top-0 h-full bg-white shadow-lg transform transition-transform duration-300 ease-in-out overflow-y-auto mobile-panel">
            <div class="p-4">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-gray-800">Menu</h2>
                    <button id="close-mobile-menu" class="text-gray-600 hover:text-red-500">
                        <i class="fas fa-times fa-lg"></i>
                    </button>
                </div>

                <!-- User Profile Section for Mobile -->
                <?php if ($user_id && $user_info): ?>
                    <div class="mb-6 border-b border-gray-200 pb-4">
                        <div class="flex items-center space-x-3 mb-4">
                            <img src="uploads/profile_pictures/<?php echo $user_info['profile_picture'] ? $user_info['profile_picture'] : 'default-profile.png'; ?>"
                                alt="Profile Picture"
                                class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <span class="font-medium text-gray-700 block"><?php echo htmlspecialchars($user_info['name']); ?></span>
                                <span class="text-sm text-gray-500">View Profile</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Mobile Navigation Links -->
                <ul class="space-y-4">
                    <li><a href="index.php" class="flex items-center text-gray-600 hover:text-red-500 py-2"><i class="fas fa-home fa-lg w-8"></i><span class="ml-2">Home</span></a></li>
                    <li><a href="Products.php" class="flex items-center text-gray-600 hover:text-gray-800 py-2"><i class="fas fa-store fa-lg w-8"></i><span class="ml-2">Products</span></a></li>
                    <li><a href="about.php" class="flex items-center text-gray-600 hover:text-red-500 py-2"><i class="fas fa-info-circle fa-lg w-8"></i><span class="ml-2">About</span></a></li>
                    <li><a href="contact.php" class="flex items-center text-gray-600 hover:text-red-500 py-2"><i class="fas fa-phone fa-lg w-8"></i><span class="ml-2">Contact</span></a></li>
                    <li><a href="order-form.php" class="flex items-center text-gray-600 hover:text-red-500 py-2"><i class="fas fa-shop fa-lg w-8"></i><span class="ml-2">Order Here</span></a></li>
                </ul>

                <!-- Mobile User Actions -->
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <?php if ($user_id && $user_info): ?>
                        <div class="space-y-3">
                            <a href="account_dashboard.php" class="flex items-center text-gray-600 hover:text-red-500 py-2">
                                <i class="fas fa-user fa-lg w-8"></i> My Profile
                            </a>
                            <a href="settings.php" class="flex items-center text-gray-600 hover:text-red-500 py-2">
                                <i class="fas fa-cog fa-lg w-8"></i> Settings
                            </a>
                            <a href="logout.php" class="flex items-center justify-center w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg mt-4">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="flex items-center justify-center w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-user mr-2"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</nav>

<script>
    // Mobile menu toggle functionality
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMobileMenu = document.getElementById('close-mobile-menu');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

    function toggleMobileMenu() {
        mobileMenu.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
        // slide in/out effect for mobile panel
        const panel = document.querySelector('.mobile-panel');
        if (panel) panel.classList.toggle('translate-x-full');
    }

    mobileMenuButton.addEventListener('click', toggleMobileMenu);
    closeMobileMenu.addEventListener('click', toggleMobileMenu);
    mobileMenuOverlay.addEventListener('click', toggleMobileMenu);

    // Cart count update (unchanged)
    <?php if ($user_id): ?>

        function updateCartCount() {
            fetch('check_cart_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.count !== undefined) {
                        document.getElementById('cart-count').textContent = data.count;
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }
        setInterval(updateCartCount, 1000);

        const profileButton = document.getElementById('profile-button');
        const profileDropdown = document.getElementById('profile-dropdown');

        if (profileButton && profileDropdown) {
            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileDropdown.contains(e.target) && !profileButton.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // Close dropdown when pressing Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    profileDropdown.classList.add('hidden');
                }
            });
        }
    <?php endif; ?>
</script>
<style>
    /* iPhone-like glass navbar */
    .iphone-nav { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0, 0, 0, 0.06); padding-top: env(safe-area-inset-top); }

    /* mobile sliding panel looks like iOS panel */
    .mobile-panel { width: 320px; border-radius: 0 18px 18px 0; right: 0; transform: translateX(0); }

    /* utility for hiding by translating full width */
    .translate-x-full {
        transform: translateX(100%);
    }

    .dropdown-active { display: block !important; }

    #profile-dropdown {
        z-index: 1000;
    }

    /* Ensure replaced Feather SVGs inside nav links align with text */
    .nav-link svg,
    .iphone-nav a svg,
    .iphone-nav i svg {
        width: 20px;
        height: 20px;
        vertical-align: middle;
        stroke-width: 1.5;
        color: inherit;
        display: inline-block;
        flex-shrink: 0;
    }

    /* Ensure anchors that have flex align center icon and text */
    .iphone-nav a.flex.items-center,
    .iphone-nav a.flex {
        align-items: center;
    }

    /* Utility: make sure inline icons have consistent spacing */
    .iphone-nav a span,
    .iphone-nav a svg+span,
    .iphone-nav a i+span {
        display: inline-block;
    }
</style>
<!-- Feather icons loader + iconReplace mapping -->
<script src="https://unpkg.com/feather-icons"></script>
<script>
    // Replace FontAwesome <i> elements with Feather icons using a mapping.
    function iconReplace() {
        const map = {
            // General navigation
            'fa-home': 'home',
            'fa-store': 'shopping-bag',
            'fa-shopping-bag': 'shopping-bag',
            'fa-info-circle': 'info',
            'fa-phone': 'phone',
            'fa-shop': 'shopping-bag',
            'fa-shopping-cart': 'shopping-cart',
            'fa-user': 'user',
            'fa-user-circle': 'user',
            'fa-heart': 'heart',
            'fa-user-check': 'user-check',

            // UI controls
            'fa-chevron-down': 'chevron-down',
            'fa-times': 'x',
            'fa-bars': 'menu',
            'fa-plus': 'plus',
            'fa-minus': 'minus',
            'fa-edit': 'edit',
            'fa-trash': 'trash-2',
            'fa-ban': 'slash',
            'fa-eye': 'eye',
            'fa-eye-slash': 'eye-off',
            'fa-check-circle': 'check-circle',
            'fa-check': 'check',
            'fa-camera': 'camera',

            // Commerce/actions
            'fa-credit-card': 'credit-card',
            'fa-cart-plus': 'shopping-cart',
            'fa-arrow-right': 'arrow-right',
            'fa-arrow-left': 'arrow-left',
            'fa-peso-sign': 'dollar-sign', // closest available
            'fa-image': 'image',

            // Features/benefits
            'fa-shipping-fast': 'truck',
            'fa-shield-alt': 'shield',
            'fa-headset': 'headphones',
            'fa-map-marker-alt': 'map-pin',
            'fa-envelope': 'mail',
            'fa-bell': 'bell',
            'fa-cog': 'settings',
            'fa-chart-line': 'trending-up',
            'fa-users': 'users',

            // Data state
            'fa-sign-out-alt': 'log-out',
            'fa-box': 'package',
            'fa-out-of-stock': 'x-circle',
            'fa-low-stock': 'alert-triangle',
            'fa-exclamation-circle': 'alert-circle'
        };

        // Approximate brand icon mappings (Feather doesn't include brand logos)
        const brandMap = {
            'fa-facebook': 'globe',
            'fa-facebook-f': 'globe',
            'fa-instagram': 'camera',
            'fa-tiktok': 'music',
            'fa-twitter': 'send'
        };

        document.querySelectorAll('i').forEach(el => {
            try {
                const classes = (el.className || '').split(/\s+/);

                // Check for explicit brand class first (e.g., fa-facebook)
                const brandClass = classes.find(c => brandMap[c]);
                if (brandClass) {
                    el.setAttribute('data-feather', brandMap[brandClass]);
                    const keepClasses = classes.filter(c => !/^fa[srb]?$/.test(c) && !c.startsWith('fa-'));
                    if (keepClasses.length) el.setAttribute('class', keepClasses.join(' ')); else el.removeAttribute('class');
                    return;
                }

                // Otherwise map standard fa- icons
                const faClass = classes.find(c => map[c]);
                if (faClass) {
                    el.setAttribute('data-feather', map[faClass]);
                    const keepClasses = classes.filter(c => !/^fa[srb]?$/.test(c) && !c.startsWith('fa-'));
                    if (keepClasses.length) el.setAttribute('class', keepClasses.join(' ')); else el.removeAttribute('class');
                }
            } catch (e) {
                // ignore
            }
        });

        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iconReplace);
    } else {
        iconReplace();
    }
</script>
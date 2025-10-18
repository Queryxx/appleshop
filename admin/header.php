
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel -  JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-slate-100">
    <!-- Top Navigation Bar -->
    <header class="fixed top-0 right-0 left-64 bg-white/70 backdrop-blur-md border-b border-gray-100 z-30">
        <div class="px-6 py-4 flex items-center justify-between">
            <!-- Page Title -->
            <h1 class="text-xl font-semibold text-gray-800">
              Admin Dashboard
            </h1>

            <!-- Right Side Items -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="p-2 text-gray-500 hover:text-gray-700 relative group">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>

                <!-- Settings -->
                <div class="relative">
                    <button class="p-2 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>

                <!-- Admin Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle text-gray-500 text-2xl"></i>
                            </div>
                            <span class="text-sm font-medium">
                                <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </header>
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

            // Approximate brand icon mappings
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
                    const brandClass = classes.find(c => brandMap[c]);
                    if (brandClass) {
                        el.setAttribute('data-feather', brandMap[brandClass]);
                        const keepClasses = classes.filter(c => !/^fa[srb]?$/.test(c) && !c.startsWith('fa-'));
                        if (keepClasses.length) el.setAttribute('class', keepClasses.join(' ')); else el.removeAttribute('class');
                        return;
                    }
                    const faClass = classes.find(c => map[c]);
                    if (faClass) {
                        el.setAttribute('data-feather', map[faClass]);
                        const keepClasses = classes.filter(c => !/^fa[srb]?$/.test(c) && !c.startsWith('fa-'));
                        if (keepClasses.length) el.setAttribute('class', keepClasses.join(' ')); else el.removeAttribute('class');
                    }
                } catch (e) {}
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
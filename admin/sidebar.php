<?php
include 'auth.php';
?>
<div class="fixed inset-y-0 left-0 w-64 bg-slate-900 border-r border-slate-800">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-slate-800">
            <a href="dashboard.php" class="text-xl font-bold text-red-500 hover:text-red-600 transition-colors">
                Admin Panel
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="dashboard.php" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-all duration-200
                <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php'
                    ? 'bg-red-500/10 text-red-500'
                    : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                <span>Dashboard</span>
            </a>

            <a href="manage_products.php" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-all duration-200
                <?php echo basename($_SERVER['PHP_SELF']) == 'products.php'
                    ? 'bg-red-500/10 text-red-500'
                    : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-box w-5 h-5 mr-3"></i>
                <span>Products</span>
            </a>

            <a href="orders.php" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-all duration-200
                <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php'
                    ? 'bg-red-500/10 text-red-500'
                    : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                <span>Orders</span>
            </a>

            <a href="customers.php" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-all duration-200
                <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php'
                    ? 'bg-red-500/10 text-red-500'
                    : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-users w-5 h-5 mr-3"></i>
                <span>Customers</span>
            </a>
            <div class="relative group">
                <a href="#" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-all duration-200
        <?php echo in_array(basename($_SERVER['PHP_SELF']), ['about.php', 'banner.php', 'contact.php'])
            ? 'bg-red-500/10 text-red-500'
            : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                    <i class="fas fa-box w-5 h-5 mr-3"></i>
                    <span>Manage</span>
                    <i class="fas fa-chevron-down ml-auto text-sm transition-transform group-hover:rotate-180"></i>
                </a>

                <!-- Dropdown Menu -->
                <div class="hidden group-hover:block absolute left-full top-0 ml-2 w-48 bg-white rounded-lg shadow-lg border border-white">
                    <a href="about.php" class="flex items-center px-4 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200">
                        <i class="fas fa-info-circle w-5 h-5 mr-3"></i>
                        <span>About</span>
                    </a>
                    <a href="banner.php" class="flex items-center px-4 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200">
                        <i class="fas fa-image w-5 h-5 mr-3"></i>
                        <span>Banner</span>
                    </a>
                    <a href="contact.php" class="flex items-center px-4 py-2.5 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200">
                        <i class="fas fa-envelope w-5 h-5 mr-3"></i>
                        <span>Contact</span>
                    </a>
                </div>
            </div>
            <a href="setting.php" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-all duration-200
                <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php'
                    ? 'bg-red-500/10 text-red-500'
                    : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-cog w-5 h-5 mr-3"></i>
                <span>Admin Setting</span>
            </a>
        </nav>

        <!-- Admin Profile -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle text-slate-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-200">
                            <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                        </p>
                    </div>
                </div>
                <a href="logout.php" class="p-2 text-slate-400 hover:text-slate-200 transition-colors">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<style>
    /* Add this to your CSS */
    .group:hover .group-hover\:block {
        display: block;
    }

    .group:hover .group-hover\:rotate-180 {
        transform: rotate(180deg);
    }
</style>
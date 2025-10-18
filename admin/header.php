
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel -  JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-slate-100">
    <!-- Top Navigation Bar -->
    <header class="fixed top-0 right-0 left-64 bg-white z-30">
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
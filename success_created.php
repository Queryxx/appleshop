<?php
session_start();

// If there's no success message in session, redirect to register page
if (!isset($_SESSION['success'])) {
    header("Location: register.php");
    exit();
}

// Get the success message
$message = $_SESSION['success'];
// Clear the success message from session
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(17, 25, 40, 0.75);
        }
    </style>
</head>

<body class="bg-slate-900 min-h-screen">
    <?php include 'nav.php'; ?>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full glass-effect p-10 rounded-2xl shadow-2xl border border-slate-700 animate-fadeIn">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-500/10 mb-8">
                    <i class="fas fa-check text-5xl text-red-500"></i>
                </div>

                <!-- Success Message -->
                <h2 class="text-3xl font-bold text-white mb-4">Welcome Aboard</h2>
                <p class="text-slate-300 mb-8 text-sm">
                    Your account has been successfully created. Experience premium shopping with us.
                </p>

                <!-- Login Button -->
                <a href="login.php"
                    class="group relative inline-flex w-full justify-center items-center py-3 px-4 
                          bg-red-500 hover:bg-red-600 text-white rounded-lg 
                          transition-all duration-300 ease-in-out font-medium text-center
                          shadow-lg hover:shadow-red-500/50">
                    <span class="mr-2">Continue to Login</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
                <!-- Progress Bar -->
                <div class="mt-8">
                    <div class="w-full bg-slate-700 rounded-full h-1">
                        <div class="bg-red-500 h-1 rounded-full animate-[grow_5s_linear]"></div>
                    </div>
                    <p class="text-slate-400 text-xs mt-2">redirecting to login...</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Auto redirect to login page after 5 seconds
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 5000);

        // Add animation keyframe for progress bar
        const style = document.createElement('style');
        style.textContent = `
            @keyframes grow {
                from { width: 0; }
                to { width: 100%; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>
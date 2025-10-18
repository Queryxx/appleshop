<?php
session_start();
include 'conn.php';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate email and password are not empty
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields";
    } else {
        // Prepare SQL using prepared statement
        $sql = "SELECT user_id, name, email, password FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Login successful - set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];

                    // redirect to dashboard
                    header("Location: account_dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Invalid email or password";
                }
            } else {
                $_SESSION['error'] = "Invalid email or password";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-100">
    <?php include 'nav.php'; ?>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Login</h2>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="text-red-500 bg-red-50 border border-red-100 px-4 py-3 rounded-lg mb-4">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="text-gray-700 font-medium">Email address</label>
                        <input id="email" name="email" type="email" required
                            class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                            text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                            placeholder-gray-400 transition duration-200">
                    </div>
                    <div>
                        <label for="password" class="text-gray-700 font-medium">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                                text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                                placeholder-gray-400 transition duration-200">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-red-500 focus:ring-red-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="text-sm text-red-500 hover:text-red-600 font-medium">
                        Forgot your password?
                    </a>
                </div>

                <div>
                    <button type="submit"
                        class="w-full py-2 px-4 bg-red-500 hover:bg-red-600 
                        text-white rounded-lg transition duration-200 font-medium">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Don't have an account?
                    <a href="register.php" class="text-red-500 hover:text-red-600 font-medium">
                        Sign up
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const iconContainer = passwordInput.nextElementSibling.querySelector('i, svg');
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // If it's a Feather SVG, swap between eye and eye-off
            if (iconContainer && iconContainer.tagName && iconContainer.tagName.toLowerCase() === 'svg') {
                iconContainer.outerHTML = (isPassword
                    ? feather.icons['eye-off'].toSvg()
                    : feather.icons['eye'].toSvg());
            } else if (iconContainer) {
                // Fallback for FontAwesome <i>
                iconContainer.classList.toggle('fa-eye');
                iconContainer.classList.toggle('fa-eye-slash');
            }
        }

        // Initial feather replacement
        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
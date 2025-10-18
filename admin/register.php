<?php
session_start();
include '../conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_code = $_POST['admin_code']; // Secret code to allow admin registration

    // Verify admin registration code
    $correct_admin_code = "ADMIN123"; // Change this to a secure code
    
    if ($admin_code !== $correct_admin_code) {
        $_SESSION['error'] = "Invalid admin registration code";
    } else if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
    } else {
        // Check if username exists
        $check_sql = "SELECT admin_id FROM Admin WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Username already exists";
        } else {
            // Hash password and insert new admin
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Admin (username, password) VALUES (?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $username, $hashed_password);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Admin account created successfully";
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Error creating account";
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-slate-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Admin Registration</h2>
                <p class="text-gray-600">Create a new admin account</p>
            </div>
            
            <form class="mt-8 space-y-6" action="" method="POST">
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="text-red-500 bg-red-50 border border-red-100 px-4 py-3 rounded-lg relative mb-4">';
                    echo htmlspecialchars($_SESSION['error']);
                    echo '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <div class="space-y-4">
                    <div>
                        <label for="username" class="text-gray-700 font-medium">Username</label>
                        <input id="username" name="username" type="text" required
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
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="text-gray-700 font-medium">Confirm Password</label>
                        <div class="relative">
                            <input id="confirm_password" name="confirm_password" type="password" required
                                class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                                text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                                placeholder-gray-400 transition duration-200">
                            <button type="button" onclick="togglePassword('confirm_password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="admin_code" class="text-gray-700 font-medium">Admin Registration Code</label>
                        <input id="admin_code" name="admin_code" type="password" required
                            class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                            text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                            placeholder-gray-400 transition duration-200">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full py-2 px-4 bg-red-500 hover:bg-red-600 
                        text-white rounded-lg transition duration-200 font-medium">
                        Create Account
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-red-500 hover:text-red-600 font-medium">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const iconContainer = passwordInput.nextElementSibling.querySelector('i, svg');
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            if (iconContainer && iconContainer.tagName && iconContainer.tagName.toLowerCase() === 'svg') {
                iconContainer.outerHTML = (isPassword
                    ? feather.icons['eye-off'].toSvg()
                    : feather.icons['eye'].toSvg());
            } else if (iconContainer) {
                iconContainer.classList.toggle('fa-eye');
                iconContainer.classList.toggle('fa-eye-slash');
            }
        }

        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    </script>
</body>
</html>
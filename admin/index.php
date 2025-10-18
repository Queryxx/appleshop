    <?php 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();
    include '../conn.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL using mysqli object-oriented style
        $sql = "SELECT admin_id, username, password FROM admin WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    // Login successful
                    $_SESSION['admin_id'] = $row['admin_id'];
                    $_SESSION['admin_username'] = $row['username'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Invalid username or password";
                }
            } else {
                $_SESSION['error'] = "Invalid username or password";
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - JMYBA</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>

    <body class="bg-slate-900">
        <!-- Add Back Button -->
        <a href="../index.php" class="fixed top-6 left-6 text-slate-400 hover:text-gray-800 transition-colors">
            <div class="flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Site</span>
            </div>
        </a>
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Admin Login</h2>
                    <p class="text-gray-600">Enter your credentials to access the admin panel</p>
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
                                <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full py-2 px-4 bg-red-500 hover:bg-red-600 
                            text-white rounded-lg transition duration-200 font-medium">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.querySelector('.fa-eye');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            }
        </script>
    </body>

    </html>
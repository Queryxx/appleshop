<?php
session_start();
require 'vendor/autoload.php';
include 'conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$step = isset($_SESSION['step']) ? $_SESSION['step'] : 1;

if (isset($_GET['step'])) {
    $_SESSION['step'] = (int) $_GET['step'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && $step === 1) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        // Check if email exists
        $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $message = "Email already exists. Please try a different one.";
        } else {
            if ($password === $confirmPassword) {
                $pin = random_int(100000, 999999);
                $_SESSION['pin'] = $pin;
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
                $_SESSION['phone'] = $phone;
                $_SESSION['address'] = $address;

                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'onlineshopjmy@gmail.com'; // Replace with your Gmail
                    $mail->Password = 'mrvq cweb xatg bwlu'; // Replace with your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('onlineshopjmy@gmail.com', 'JMYBA');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Registration PIN';
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; color: #333;'>
                            <h2 style='color: #B7791F;'>Your Registration PIN</h2>
                            <p>Dear $name,</p>
                            <p>Your registration PIN is: <strong>$pin</strong></p>
                            <p>Please enter this PIN to complete your registration.</p>
                            <p>Best regards,<br>JMYBA</p>
                        </div>
                    ";

                    $mail->send();
                    $message = "A PIN has been sent to your email. Enter the PIN to continue registration.";
                    $_SESSION['step'] = 2;
                    header('Location: register.php');
                    exit();
                } catch (Exception $e) {
                    $message = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $message = "Passwords do not match. Please try again.";
            }
        }
    } elseif (isset($_POST['pin']) && $step === 2) {
        $pin = $_POST['pin'];
        if ($_SESSION['pin'] == $pin) {
            $name = $_SESSION['name'];
            $email = $_SESSION['email'];
            $password = password_hash($_SESSION['password'], PASSWORD_DEFAULT);
            $phone = $_SESSION['phone'];
            $address = $_SESSION['address'];

            $query = "INSERT INTO users (name, email, password, phone_number, address) 
                     VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password, $phone, $address);
            
            if (mysqli_stmt_execute($stmt)) {
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: success_created.php");
                exit();
            } else {
                $message = "Error occurred during registration. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Invalid PIN. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - JMYBA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'nav.php'; ?>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">
                    <?php echo $step === 1 ? 'Create an account' : 'Verify Your Email'; ?>
                </h2>
            </div>

            <?php if (!empty($message)): ?>
                <div class="text-red-500 bg-red-50 border border-red-100 px-4 py-3 rounded-lg mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="register.php" method="POST">
                <?php if ($step === 1): ?>
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="text-gray-700 font-medium">Full Name</label>
                            <input id="name" name="name" type="text" required
                                class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                                text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                                placeholder-gray-400 transition duration-200">
                        </div>

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
                            <label for="phone" class="text-gray-700 font-medium">Phone Number</label>
                            <input id="phone" name="phone" type="tel" required
                                class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                                text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                                placeholder-gray-400 transition duration-200">
                        </div>

                       <div>
                            <label for="address" class="text-gray-700 font-medium">Address</label>
                            <textarea id="address" name="address" required rows="3"
                                class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                                text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                                placeholder-gray-400 transition duration-200"></textarea>
                        </div>
                    </div>
                <?php else: ?>
                    <div>
                        <label for="pin" class="text-gray-700 font-medium">Enter PIN</label>
                        <input id="pin" name="pin" type="text" required
                            class="w-full px-4 py-2 mt-2 bg-gray-50 border border-gray-200 rounded-lg 
                            text-gray-900 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500
                            placeholder-gray-400 transition duration-200"
                            placeholder="Enter the 6-digit PIN sent to your email">
                    </div>
                <?php endif; ?>

                <div>
                    <button type="submit"
                        class="w-full py-2 px-4 bg-red-500 hover:bg-red-600 
                        text-white rounded-lg transition duration-200 font-medium">
                        <?php echo $step === 1 ? 'Register' : 'Verify PIN'; ?>
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="login.php" class="text-red-500 hover:text-red-600 font-medium">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = passwordInput.nextElementSibling.querySelector('i');

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

    <?php include 'footer.php'; ?>
</body>
</html>
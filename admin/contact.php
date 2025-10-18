<?php
include '../conn.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $facebook_url = filter_var($_POST['facebook_url'], FILTER_SANITIZE_URL);
    $tiktok = filter_var($_POST['tiktok'], FILTER_SANITIZE_URL);
    $instagram_url = filter_var($_POST['instagram_url'], FILTER_SANITIZE_URL);

    // Check if content exists
    $check = $conn->query("SELECT id FROM contact_info LIMIT 1");
    if ($check->num_rows > 0) {
        $sql = "UPDATE contact_info SET 
                address = ?, phone = ?, email = ?,
                facebook_url = ?, tiktok = ?, instagram_url = ?
                WHERE id = 1";
    } else {
        $sql = "INSERT INTO contact_info (
                address, phone, email, facebook_url, tiktok, instagram_url
                ) VALUES (?, ?, ?, ?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", 
        $address, $phone, $email,
        $facebook_url, $tiktok, $instagram_url
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Contact information updated successfully";
    } else {
        $_SESSION['error'] = "Error updating contact information: " . $conn->error;
    }
    $stmt->close();
    header("Location: contact.php");
    exit();
}

// Fetch current content
$content = $conn->query("SELECT * FROM contact_info LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Section - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-slate-100">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 mt-5 p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Manage Contact Section</h1>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" class="space-y-6">
                <!-- Contact Information -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold mb-4">Contact Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="address" required
                                value="<?php echo htmlspecialchars($content['address'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" name="phone" required
                                value="<?php echo htmlspecialchars($content['phone'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required
                                value="<?php echo htmlspecialchars($content['email'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold mb-4">Social Media Links</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-facebook text-red-600 mr-2"></i>Facebook URL
                            </label>
                            <input type="url" name="facebook_url"
                                value="<?php echo htmlspecialchars($content['facebook_url'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-tiktok text-red-400 mr-2"></i>Tiktok URL
                            </label>
                            <input type="url" name="tiktok"
                                value="<?php echo htmlspecialchars($content['tiktok'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram URL
                            </label>
                            <input type="url" name="instagram_url"
                                value="<?php echo htmlspecialchars($content['instagram_url'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                        class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Preview</h2>
            <section id="contact" class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Contact Us</h2>
                <div class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="contact-info">
                            <h3 class="text-xl font-bold mb-4">Get in Touch</h3>
                            <ul class="space-y-4">
                                <li class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-red-500 w-8"></i>
                                    <span><?php echo htmlspecialchars($content['address'] ?? ''); ?></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-phone text-red-500 w-8"></i>
                                    <span><?php echo htmlspecialchars($content['phone'] ?? ''); ?></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-envelope text-red-500 w-8"></i>
                                    <span><?php echo htmlspecialchars($content['email'] ?? ''); ?></span>
                                </li>
                            </ul>
                            <div class="mt-6 flex space-x-4">
                                <?php if (!empty($content['facebook_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($content['facebook_url']); ?>" 
                                       class="text-red-500 hover:text-red-600" target="_blank">
                                        <i class="fab fa-facebook fa-2x"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($content['tiktok'])): ?>
                                    <a href="<?php echo htmlspecialchars($content['tiktok']); ?>" 
                                       class="text-red-500 hover:text-red-600" target="_blank">
                                        <i class="fab fa-tiktok fa-2x"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($content['instagram_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($content['instagram_url']); ?>" 
                                       class="text-red-500 hover:text-red-600" target="_blank">
                                        <i class="fab fa-instagram fa-2x"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
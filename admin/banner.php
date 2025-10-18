<?php
include '../conn.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                $subtitle = filter_var($_POST['subtitle'], FILTER_SANITIZE_STRING);
                $button_text = filter_var($_POST['button_text'], FILTER_SANITIZE_STRING);
                $button_link = filter_var($_POST['button_link'], FILTER_SANITIZE_STRING);

                // Handle image upload
                if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['background_image']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = 'banner_' . time() . '.' . $ext;
                        $upload_path = '../image/' . $new_filename;

                        if (move_uploaded_file($_FILES['background_image']['tmp_name'], $upload_path)) {
                            $image_path = $new_filename;
                        }
                    }
                }

                // Check if banner exists
                $check = $conn->query("SELECT id FROM banner LIMIT 1");
                if ($check->num_rows > 0) {
                    // Update existing banner
                    $sql = "UPDATE banner SET title = ?, subtitle = ?, button_text = ?, button_link = ?";
                    if (isset($image_path)) {
                        $sql .= ", background_image = ?";
                    }
                    $sql .= " WHERE id = 1";
                } else {
                    // Insert new banner
                    $sql = "INSERT INTO banner (title, subtitle, button_text, button_link" . (isset($image_path) ? ", background_image" : "") . ") 
                           VALUES (?, ?, ?, ?" . (isset($image_path) ? ", ?" : "") . ")";
                }

                $stmt = $conn->prepare($sql);
                if (isset($image_path)) {
                    $stmt->bind_param("sssss", $title, $subtitle, $button_text, $button_link, $image_path);
                } else {
                    $stmt->bind_param("ssss", $title, $subtitle, $button_text, $button_link);
                }

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Banner updated successfully";
                } else {
                    $_SESSION['error'] = "Error updating banner: " . $conn->error;
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch current banner settings
$banner = $conn->query("SELECT * FROM banner LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banner - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-slate-100">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 mt-5 p-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Manage Banner</h1>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Banner Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="update">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Background Image
                    </label>
                    <?php if (!empty($banner['background_image'])): ?>
                        <div class="mb-4">
                            <img src="../image/<?php echo htmlspecialchars($banner['background_image']); ?>" 
                                 alt="Current banner" class="h-48 object-cover rounded">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="background_image" accept="image/*"
                           class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Title
                    </label>
                    <input type="text" name="title" required
                           value="<?php echo htmlspecialchars($banner['title'] ?? ''); ?>"
                           class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Subtitle
                    </label>
                    <textarea name="subtitle" rows="3"
                              class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-red-500"><?php echo htmlspecialchars($banner['subtitle'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Button Text
                    </label>
                    <input type="text" name="button_text"
                           value="<?php echo htmlspecialchars($banner['button_text'] ?? 'Order Now'); ?>"
                           class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Button Link
                    </label>
                    <input type="text" name="button_link"
                           value="<?php echo htmlspecialchars($banner['button_link'] ?? 'choose.php'); ?>"
                           class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-red-500">
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Preview</h2>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-cover bg-center h-96" 
                     style="background-image: url('../image/<?php echo htmlspecialchars($banner['background_image'] ?? 'logo.png'); ?>');">
                    <div class="bg-black bg-opacity-50 h-full flex flex-col items-center justify-center text-center text-white p-8">
                        <h1 class="text-4xl font-extrabold mb-4">
                            <?php echo htmlspecialchars($banner['title'] ?? 'Welcome to JMYBA'); ?>
                        </h1>
                        <p class="text-xl mt-4 mb-6">
                            <?php echo htmlspecialchars($banner['subtitle'] ?? 'Find the best products at unbeatable prices'); ?>
                        </p>
                        <a href="<?php echo htmlspecialchars($banner['button_link'] ?? 'choose.php'); ?>" 
                           class="mt-8 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg text-lg flex items-center">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            <?php echo htmlspecialchars($banner['button_text'] ?? 'Order Now'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
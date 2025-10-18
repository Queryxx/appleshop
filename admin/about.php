<?php
include '../conn.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $main_title = filter_var($_POST['main_title'], FILTER_SANITIZE_STRING);
    $main_description = filter_var($_POST['main_description'], FILTER_SANITIZE_STRING);

    $feature1_title = filter_var($_POST['feature1_title'], FILTER_SANITIZE_STRING);
    $feature1_description = filter_var($_POST['feature1_description'], FILTER_SANITIZE_STRING);
    $feature1_icon = filter_var($_POST['feature1_icon'], FILTER_SANITIZE_STRING);

    $feature2_title = filter_var($_POST['feature2_title'], FILTER_SANITIZE_STRING);
    $feature2_description = filter_var($_POST['feature2_description'], FILTER_SANITIZE_STRING);
    $feature2_icon = filter_var($_POST['feature2_icon'], FILTER_SANITIZE_STRING);

    $feature3_title = filter_var($_POST['feature3_title'], FILTER_SANITIZE_STRING);
    $feature3_description = filter_var($_POST['feature3_description'], FILTER_SANITIZE_STRING);
    $feature3_icon = filter_var($_POST['feature3_icon'], FILTER_SANITIZE_STRING);

    // Check if content exists
    $check = $conn->query("SELECT id FROM about_content LIMIT 1");
    if ($check->num_rows > 0) {
        $sql = "UPDATE about_content SET 
                main_title = ?, main_description = ?,
                feature1_title = ?, feature1_description = ?, feature1_icon = ?,
                feature2_title = ?, feature2_description = ?, feature2_icon = ?,
                feature3_title = ?, feature3_description = ?, feature3_icon = ?
                WHERE id = 1";
    } else {
        $sql = "INSERT INTO about_content (
                main_title, main_description,
                feature1_title, feature1_description, feature1_icon,
                feature2_title, feature2_description, feature2_icon,
                feature3_title, feature3_description, feature3_icon
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssss",
        $main_title,
        $main_description,
        $feature1_title,
        $feature1_description,
        $feature1_icon,
        $feature2_title,
        $feature2_description,
        $feature2_icon,
        $feature3_title,
        $feature3_description,
        $feature3_icon
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "About content updated successfully";
    } else {
        $_SESSION['error'] = "Error updating content: " . $conn->error;
    }
    $stmt->close();
    header("Location: about.php");
    exit();
}
$icons = [
    'fas fa-shipping-fast' => 'Fast Shipping',
    'fas fa-shield-alt' => 'Security Shield',
    'fas fa-headset' => 'Support Headset',
    'fas fa-truck' => 'Delivery Truck',
    'fas fa-box' => 'Package Box',
    'fas fa-gift' => 'Gift Box',
    'fas fa-clock' => 'Clock',
    'fas fa-star' => 'Star',
    'fas fa-heart' => 'Heart',
    'fas fa-check-circle' => 'Check Circle',
    'fas fa-thumbs-up' => 'Thumbs Up',
    'fas fa-medal' => 'Medal',
    'fas fa-crown' => 'Crown',
    'fas fa-gem' => 'Gem',
    'fas fa-certificate' => 'Certificate'
];
// Fetch current content
$content = $conn->query("SELECT * FROM about_content LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Section - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-slate-100">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 mt-5 p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Manage About Section</h1>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" class="space-y-6">
                <!-- Main Content -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold mb-4">Main Content</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="main_title" required
                                value="<?php echo htmlspecialchars($content['main_title'] ?? 'About Us'); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="main_description" required rows="4"
                                class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($content['main_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Feature 1 -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold mb-4">Feature 1</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                            <div class="relative">
                                <select name="feature1_icon" required
                                    class="w-full px-3 py-2 border rounded-lg appearance-none bg-white">
                                    <?php foreach ($icons as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"
                                            <?php echo ($content['feature1_icon'] ?? 'fas fa-shipping-fast') === $value ? 'selected' : ''; ?>>
                                            <i class="<?php echo $value; ?> mr-2"></i> <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <i class="<?php echo htmlspecialchars($content['feature1_icon'] ?? 'fas fa-shipping-fast'); ?> text-2xl text-gray-700"></i>
                                <span class="ml-2 text-sm text-gray-600">Selected Icon Preview</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="feature1_title" required
                                value="<?php echo htmlspecialchars($content['feature1_title'] ?? 'Fast Delivery'); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" name="feature1_description" required
                                value="<?php echo htmlspecialchars($content['feature1_description'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold mb-4">Feature 2</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                            <div class="relative">
                                <select name="feature2_icon" required
                                    class="w-full px-3 py-2 border rounded-lg appearance-none bg-white">
                                    <?php foreach ($icons as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"
                                            <?php echo ($content['feature2_icon'] ?? 'fas fa-shield-alt') === $value ? 'selected' : ''; ?>>
                                            <i class="<?php echo $value; ?> mr-2"></i> <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <i class="<?php echo htmlspecialchars($content['feature2_icon'] ?? 'fas fa-shield-alt'); ?> text-2xl text-gray-700"></i>
                                <span class="ml-2 text-sm text-gray-600">Selected Icon Preview</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="feature2_title" required
                                value="<?php echo htmlspecialchars($content['feature2_title'] ?? 'Secure Shopping'); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" name="feature2_description" required
                                value="<?php echo htmlspecialchars($content['feature2_description'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold mb-4">Feature 3</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                            <div class="relative">
                                <select name="feature3_icon" required
                                    class="w-full px-3 py-2 border rounded-lg appearance-none bg-white">
                                    <?php foreach ($icons as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"
                                            <?php echo ($content['feature3_icon'] ?? 'fas fa-headset') === $value ? 'selected' : ''; ?>>
                                            <i class="<?php echo $value; ?> mr-2"></i> <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <i class="<?php echo htmlspecialchars($content['feature3_icon'] ?? 'fas fa-headset'); ?> text-2xl text-gray-700"></i>
                                <span class="ml-2 text-sm text-gray-600">Selected Icon Preview</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="feature3_title" required
                                value="<?php echo htmlspecialchars($content['feature3_title'] ?? '24/7 Support'); ?>"
                                class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" name="feature3_description" required
                                value="<?php echo htmlspecialchars($content['feature3_description'] ?? ''); ?>"
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
            <section id="about" class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">
                    <?php echo htmlspecialchars($content['main_title'] ?? 'About Us'); ?>
                </h2>
                <div class="max-w-3xl mx-auto">
                    <p class="text-gray-600 text-center text-lg mb-6">
                        <?php echo htmlspecialchars($content['main_description'] ?? ''); ?>
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                        <div class="text-center">
                            <i class="<?php echo htmlspecialchars($content['feature1_icon'] ?? 'fas fa-shipping-fast'); ?> text-4xl text-red-500 mb-4"></i>
                            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($content['feature1_title'] ?? 'Fast Delivery'); ?></h3>
                            <p class="text-gray-600"><?php echo htmlspecialchars($content['feature1_description'] ?? ''); ?></p>
                        </div>
                        <div class="text-center">
                            <i class="<?php echo htmlspecialchars($content['feature2_icon'] ?? 'fas fa-shield-alt'); ?> text-4xl text-red-500 mb-4"></i>
                            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($content['feature2_title'] ?? 'Secure Shopping'); ?></h3>
                            <p class="text-gray-600"><?php echo htmlspecialchars($content['feature2_description'] ?? ''); ?></p>
                        </div>
                        <div class="text-center">
                            <i class="<?php echo htmlspecialchars($content['feature3_icon'] ?? 'fas fa-headset'); ?> text-4xl text-red-500 mb-4"></i>
                            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($content['feature3_title'] ?? '24/7 Support'); ?></h3>
                            <p class="text-gray-600"><?php echo htmlspecialchars($content['feature3_description'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
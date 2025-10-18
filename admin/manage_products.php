<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../conn.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
                $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
                $discount = !empty($_POST['discount']) ? filter_var($_POST['discount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : NULL;

                // Handle image upload
                $image = NULL;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $target_dir = "../products/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $allowed_types = array('jpg', 'jpeg', 'png');
                    if (in_array($imageFileType, $allowed_types)) {
                        $image_name = uniqid() . '.' . $imageFileType;
                        $target_file = $target_dir . $image_name;

                        if ($_FILES["image"]["size"] <= 5000000) {
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                                $image = $image_name;
                                echo "Image uploaded successfully: " . $image;
                            } else {
                                $_SESSION['error'] = "Failed to move uploaded file.";
                            }
                        } else {
                            $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
                        }
                    } else {
                        $_SESSION['error'] = "Sorry, only JPG, JPEG, & PNG files are allowed.";
                    }
                } else {
                    echo "No image uploaded or upload error.";
                }
                // Debug: Check the final value of $image
                echo "Image value before insertion: " . $image; // Debug statement

                $sql = "INSERT INTO products (product_name, price, description, image, discount) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdssd", $name, $price, $description, $image, $discount);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Product added successfully";
                } else {
                    $_SESSION['error'] = "Error adding product: " . $conn->error;
                }
                $stmt->close();
                header("Location: manage_products.php");
                exit();

            case 'edit':
                $id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
                $name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
                $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
                $discount = !empty($_POST['discount']) ? filter_var($_POST['discount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : NULL;

                $sql = "UPDATE products SET product_name = ?, price = ?, description = ?, discount = ? WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdsdi", $name, $price, $description, $discount, $id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Product updated successfully";
                } else {
                    $_SESSION['error'] = "Error updating product: " . $conn->error;
                }
                $stmt->close();
                header("Location: manage_products.php");
                exit();
            case 'toggle_status':
                $id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
                $sql = "UPDATE products SET status = CASE 
                            WHEN status = 'available' THEN 'not_available' 
                            ELSE 'available' END 
                            WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Product status updated successfully";
                } else {
                    $_SESSION['error'] = "Error updating product status";
                }
                $stmt->close();
                header("Location: manage_products.php");
                exit();

            case 'delete':
                $id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    // Start transaction
                    $conn->begin_transaction();

                    // First delete related order items
                    $sql = "DELETE FROM order_items WHERE product_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();

                    // Then delete the product
                    $sql = "DELETE FROM products WHERE product_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();

                    // Commit transaction
                    $conn->commit();

                    $_SESSION['success'] = "Product and related orders deleted successfully";
                } catch (Exception $e) {
                    // Rollback on error
                    $conn->rollback();
                    $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
                }
                $stmt->close();
                break;
            case 'restore':
                $id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
                $sql = "UPDATE products SET deleted = 0 WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Product restored successfully";
                } else {
                    $_SESSION['error'] = "Error restoring product";
                }
                $stmt->close();
                break;
        }

        header("Location: manage_products.php");
        exit();
    }
}
$show_deleted = isset($_GET['show_deleted']) ? (bool)$_GET['show_deleted'] : false;

$sql = "SELECT * FROM products WHERE deleted = ? ORDER BY product_id DESC";
$stmt = $conn->prepare($sql);
$deleted = $show_deleted ? 1 : 0;
$stmt->bind_param("i", $deleted);
$stmt->execute();
$result = $stmt->get_result();
// Fetch all products
$sql = "SELECT * FROM products WHERE deleted = 0 ORDER BY product_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-slate-100">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 mt-5 p-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Manage Products</h1>
            <button onclick="openAddModal()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                <i class="fas fa-plus mr-2"></i> Add Product
            </button>
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

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #<?php echo $row['product_id']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($row['image']): ?>
                                    <img src="../products/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                                        class="h-12 w-12 object-cover rounded">
                                <?php else: ?>
                                    <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₱<?php echo number_format($row['price'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $row['discount'] ? $row['discount'] . '%' : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick='openEditModal(<?php echo json_encode([
                                                                    "product_id" => $row["product_id"],
                                                                    "product_name" => $row["product_name"],
                                                                    "price" => $row["price"],
                                                                    "description" => $row["description"],
                                                                    "image" => $row["image"],
                                                                    "discount" => $row["discount"]
                                                                ]); ?>)' class="text-red-600 hover:text-red-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="confirmDelete(<?php echo $row['product_id']; ?>)"
                                    class="text-red-600 hover:text-red-900 mr-3">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <button type="submit"
                                        class="<?php echo $row['status'] === 'available' ? 'text-green-600 hover:text-green-900' : 'text-red-600 hover:text-red-900'; ?>">
                                        <i class="fas fa-ban"></i> <?php echo $row['status'] === 'available' ? 'Unavailable' : 'Available'; ?>
                                    </button>
                                </form>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <l type="submit" class="<?php echo $row['status'] === 'available' ? 'bg-green-500' : 'bg-red-500'; ?> text-white px-3 py-1 rounded-full text-xs">
                                        <?php echo ucfirst($row['status']); ?>
                                    </l>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Product</h3>
                <form method="POST" class="mt-4 text-left" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="product_name">
                            Product Name
                        </label>
                        <input type="text" name="product_name" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                            Price
                        </label>
                        <input type="number" name="price" step="0.01" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                            Product Image
                        </label>
                        <input type="file" name="image" accept="image/jpeg,image/jpg,image/png"
                            class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-red-500">
                        <p class="text-sm text-gray-500 mt-1">Allowed formats: JPG, JPEG, PNG. Max size: 5MB</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="discount">
                            Discount (%)
                        </label>
                        <input type="number" name="discount" step="0.01" min="0" max="100"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeAddModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Product</h3>
                <form method="POST" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_product_name">
                            Product Name
                        </label>
                        <input type="text" name="product_name" id="edit_product_name" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_price">
                            Price
                        </label>
                        <input type="number" name="price" id="edit_price" step="0.01" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">
                            Description
                        </label>
                        <textarea name="description" id="edit_description" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_discount">
                            Discount (%)
                        </label>
                        <input type="number" name="discount" id="edit_discount" step="0.01" min="0" max="100"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeEditModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="product_id" id="delete_product_id">
    </form>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(product) {
            // Set values for all form fields
            document.getElementById('edit_product_id').value = product.product_id;
            document.getElementById('edit_product_name').value = product.product_name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_discount').value = product.discount || '';

            // Show the modal
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                document.getElementById('delete_product_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('addModal')) {
                closeAddModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
<script>
    if (window.feather) {
        feather.replace({ 'aria-hidden': 'true' });
    }
</script>
</body>

</html>
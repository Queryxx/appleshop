<?php
include '../conn.php';


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $username, $password);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Admin added successfully";
                } else {
                    $_SESSION['error'] = "Error adding admin: " . $conn->error;
                }
                $stmt->close();
                header("Location: setting.php");
                exit();

            case 'edit':
                $id = filter_var($_POST['admin_id'], FILTER_SANITIZE_NUMBER_INT);
                $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);

                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $sql = "UPDATE admin SET username = ?, password = ? WHERE admin_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $username, $password, $id);
                } else {
                    $sql = "UPDATE admin SET username = ? WHERE admin_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $username, $id);
                }

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Admin updated successfully";
                } else {
                    $_SESSION['error'] = "Error updating admin: " . $conn->error;
                }
                $stmt->close();
                header("Location: setting.php");
                exit();

            case 'delete':
                // Prevent deleting the last admin
                $count = $conn->query("SELECT COUNT(*) as count FROM admin")->fetch_assoc()['count'];
                if ($count <= 1) {
                    $_SESSION['error'] = "Cannot delete the last admin account";
                    header("Location: setting.php");
                    exit();
                }

                $id = filter_var($_POST['admin_id'], FILTER_SANITIZE_NUMBER_INT);

                // Prevent deleting own account
                if ($id == $_SESSION['admin_id']) {
                    $_SESSION['error'] = "Cannot delete your own account";
                    header("Location: setting.php");
                    exit();
                }

                $sql = "DELETE FROM admin WHERE admin_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Admin deleted successfully";
                } else {
                    $_SESSION['error'] = "Error deleting admin";
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch all admins
$sql = "SELECT admin_id, username, created_at FROM admin ORDER BY admin_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-slate-100">
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="ml-64 pt-16 mt-5 p-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Admin Settings</h1>
            <button onclick="openAddModal()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                <i class="fas fa-plus mr-2"></i> Add Admin
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

        <!-- Admins Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php if ($row['admin_id'] == 1): ?>
                                    <span class="text-red-600 font-semibold">Main Admin</span>
                                <?php else: ?>
                                    #<?php echo $row['admin_id']; ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($row['username']); ?>
                                <?php if ($row['admin_id'] == $_SESSION['admin_id']): ?>
                                    <span class="ml-2 text-xs text-red-500">(You)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo date('F j, Y g:i a', strtotime($row['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                    class="text-red-600 hover:text-red-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <?php if ($row['admin_id'] != $_SESSION['admin_id'] && $row['admin_id'] != 1): ?>
                                    <button onclick="confirmDelete(<?php echo $row['admin_id']; ?>)"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Admin</h3>
                <form method="POST" class="mt-4 text-left">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                        <input type="text" name="username" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                        <input type="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeAddModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Admin</h3>
                <form method="POST" class="mt-4 text-left">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="admin_id" id="edit_admin_id">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                        <input type="text" name="username" id="edit_username" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            New Password (leave blank to keep current)
                        </label>
                        <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="admin_id" id="delete_admin_id">
    </form>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(admin) {
            document.getElementById('edit_admin_id').value = admin.admin_id;
            document.getElementById('edit_username').value = admin.username;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this admin?')) {
                document.getElementById('delete_admin_id').value = id;
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
</body>

</html>
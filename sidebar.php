<?php

include 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user = [];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
mysqli_stmt_close($stmt);

// Handle profile picture upload
if (isset($_FILES['profile_picture'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
        $_SESSION['error'] = "Invalid file type. Please upload JPEG, PNG, or GIF.";
    } elseif ($_FILES['profile_picture']['size'] > $max_size) {
        $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
    } else {
        $uploadDir = 'uploads/profile_pictures/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('profile_') . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
            $query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $newFileName, $_SESSION['user_id']);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Profile picture updated successfully.";
            } else {
                $_SESSION['error'] = "Error updating profile picture in database.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['error'] = "Error uploading file.";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle profile picture removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_picture'])) {
    include 'conn.php';
    
    // First get the current profile picture filename
    $query = "SELECT profile_picture FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $old_picture = $row['profile_picture'];
    mysqli_stmt_close($stmt);

    // Update database to set profile_picture to NULL
    $stmt = mysqli_prepare($conn, "UPDATE users SET profile_picture = NULL WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    
    if (mysqli_stmt_execute($stmt)) {
        // Delete the old profile picture file if it exists
        if ($old_picture && $old_picture != 'default.jpg') {
            $file_path = 'uploads/profile_pictures/' . $old_picture;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $_SESSION['success'] = "Profile picture removed successfully.";
    } else {
        $_SESSION['error'] = "Error removing profile picture.";
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!-- Modal for Profile Picture -->
<div id="profileModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" id="modal-backdrop"></div>

    <!-- Modal Content -->
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-xl p-6 w-96 max-w-[90%]">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Profile Picture</h3>
            <button class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Current Profile Picture -->
        <div class="flex justify-center mb-6">
            <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>"
                alt="Profile Picture"
                class="w-32 h-32 rounded-full object-cover border-2 border-gray-200 hover:border-red-500 transition-colors"
                id="profile-picture">
        </div>

        <!-- Upload Form -->
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Update Profile Picture</label>
                <input type="file" name="profile_picture" accept="image/*"
                    class="block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-lg file:border-0
                           file:text-sm file:font-semibold
                           file:bg-red-50 file:text-red-700
                           hover:file:bg-red-100">
            </div>
            <button type="submit"
                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                Upload New Picture
            </button>
        </form>

        <!-- Remove Picture Form -->
        <form action="" method="POST" class="mt-4">
            <input type="hidden" name="remove_picture" value="1">
            <button type="submit"
                class="w-full bg-red-50 text-red-600 py-2 px-4 rounded-lg hover:bg-red-100 transition-colors">
                Remove Picture
            </button>
        </form>
    </div>
</div>
<!-- Modified Sidebar User Profile Section -->
<div class="bg-slate-900 w-64 fixed h-full left-0 top-0 border-r border-slate-800 shadow-lg">
    <div class="px-6 py-8 border-b border-slate-800">
        <div class="flex items-center space-x-3">
            <button onclick="openModal()" class="relative group">
                <div class="w-12 h-12 rounded-full overflow-hidden">
                    <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>"
                        alt="Profile Picture"
                        class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                    <i class="fas fa-camera text-white text-sm"></i>
                </div>
            </button>
            <span class="text-slate-200 font-medium">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>
        </div>
    </div>
    <!-- Navigation Menu -->
    <nav class="px-3 py-6 space-y-1">
        <a href="account_dashboard.php"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-all duration-200 group
                  <?php echo basename($_SERVER['PHP_SELF']) == 'account_dashboard.php'
                        ? 'bg-red-500/10 text-red-500'
                        : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
            <i class="fas fa-home w-5 h-5 mr-3"></i>
            <span>Dashboard</span>
        </a>

        <a href="orders.php"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                  text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 group">
            <i class="fas fa-shopping-bag w-5 h-5 mr-3"></i>
            <span>My Orders</span>
        </a>

        <a href="profile.php"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                  text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 group">
            <i class="fas fa-user w-5 h-5 mr-3"></i>
            <span>Profile</span>
        </a>

        <a href="address.php"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                  text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 group">
            <i class="fas fa-map-marker-alt w-5 h-5 mr-3"></i>
            <span>Addresses</span>
        </a>

        <a href="wishlist.php"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                  text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 group">
            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
            <span>Cart</span>
        </a>
        <a href="settingss.php"
            class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                  text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 group">
            <i class="fas fa-cog w-5 h-5 mr-3"></i>
            <span>Setting</span>
        </a>
    </nav>

    <!-- Logout Button -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-800">
        <a href="logout.php"
            class="flex items-center justify-center px-3 py-2.5 rounded-lg font-medium
                  text-slate-400 hover:bg-red-500/10 hover:text-red-500 transition-all duration-200">
            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('profileModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('profileModal').classList.add('hidden');
}
</script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    // Replace FontAwesome icons in the account sidebar context for an iOS feel
    function iconReplace() {
        const map = {
            'fa-home': 'home',
            'fa-shopping-bag': 'shopping-bag',
            'fa-user': 'user',
            'fa-map-marker-alt': 'map-pin',
            'fa-shopping-cart': 'shopping-cart',
            'fa-cog': 'settings',
            'fa-sign-out-alt': 'log-out',
            'fa-times': 'x',
            'fa-camera': 'camera'
        };

        document.querySelectorAll('i').forEach(el => {
            try {
                const classes = (el.className || '').split(/\s+/);
                const faClass = classes.find(c => map[c]);
                if (faClass) {
                    el.setAttribute('data-feather', map[faClass]);
                    const keepClasses = classes.filter(c => !/^fa[srb]?$/.test(c) && !c.startsWith('fa-'));
                    if (keepClasses.length) el.setAttribute('class', keepClasses.join(' ')); else el.removeAttribute('class');
                }
            } catch (e) {}
        });

        if (window.feather) {
            feather.replace({ 'aria-hidden': 'true' });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iconReplace);
    } else {
        iconReplace();
    }
</script>
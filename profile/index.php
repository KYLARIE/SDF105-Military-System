<?php
ob_start();
require_once '../config/db.php';
require_once '../config/auth.php';
include_once '../includes/header.php';

// Get user data
$user_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        // First update basic info
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, email = ?, phone = ?, department = ? WHERE id = ?");
        $stmt->execute([$username, $email, $phone, $department, $user_id]);

        // Handle password change if requested
        if (!empty($current_password) && !empty($new_password)) {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute([$user_id]);
            $stored_hash = $stmt->fetchColumn();

            if (password_verify($current_password, $stored_hash)) {
                // Check if new password and confirm password match
                if ($new_password === $confirm_password) {
                    // Update password
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hash, $user_id]);
                    $_SESSION['success_message'] = "Profile and password updated successfully!";
                } else {
                    $_SESSION['error_message'] = "New password and confirm password do not match.";
                    header("Location: index.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Current password is incorrect.";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['success_message'] = "Profile updated successfully!";
        }

        // Update session data
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
    }
}

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $target_dir = "../uploads/";

    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
    $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    // Check if image file is a actual image
    $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if ($check !== false) {
        // Check file size (max 5MB)
        if ($_FILES["profile_photo"]["size"] > 5000000) {
            $_SESSION['error_message'] = "Sorry, your file is too large. Max size is 5MB.";
        } else {
            // Allow certain file formats
            if ($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif") {
                $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                    // Update database with new photo path
                    $stmt = $pdo->prepare("UPDATE admins SET profile_photo = ? WHERE id = ?");
                    $stmt->execute(["../uploads/" . $new_filename, $user_id]);

                    // Update session
                    $_SESSION['profile_photo'] = "../uploads/" . $new_filename;
                    $_SESSION['success_message'] = "Profile photo updated successfully!";
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
                }
            }
        }
    } else {
        $_SESSION['error_message'] = "File is not an image.";
    }
}
?>

<div class="profile-container">
    <div class="profile-sidebar">
        <div class="profile-info">
            <div class="profile-avatar">
                <img src="<?= htmlspecialchars($user['profile_photo'] ?? '../images/default.jpg') ?>" alt="Profile Picture">
            </div>
            <h2 class="profile-name"><?= htmlspecialchars(strtoupper($user['username'])) ?></h2>
            <p class="profile-role"><?= htmlspecialchars($user['role'] ?? 'Administrator') ?></p>

            <form action="" method="post" enctype="multipart/form-data" id="photo-form">
                <input type="file" name="profile_photo" id="profile_photo" style="display: none;" onchange="document.getElementById('photo-form').submit();">
                <button type="button" class="upload-btn" onclick="document.getElementById('profile_photo').click();">
                    <i class="fas fa-camera"></i> Upload Photo
                </button>
            </form>
        </div>

        <ul class="profile-sidebar-nav">
            <li>
                <a href="#" class="active">
                    <i class="fas fa-user"></i> Account Overview
                </a>
            </li>
            <li>
                <a href="#" id="edit-profile-link">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </li>
        </ul>
    </div>

    <div class="profile-content">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="profile-section">
            <h3 class="profile-section-title">
                <i class="fas fa-user-shield"></i> User Role & Access
            </h3>
            <div class="profile-data-row">
                <div class="profile-data-label">Username</div>
                <div class="profile-data-value"><?= htmlspecialchars($user['username'] ?? 'N/A') ?></div>
            </div>
            <div class="profile-data-row">
                <div class="profile-data-label">Role</div>
                <div class="profile-data-value"><?= htmlspecialchars($user['role'] ?? 'Administrator') ?></div>
            </div>
            <div class="profile-data-row">
                <div class="profile-data-label">Department</div>
                <div class="profile-data-value"><?= htmlspecialchars($user['department'] ?? 'IT Department') ?></div>
            </div>
            <div class="profile-data-row">
                <div class="profile-data-label">Status</div>
                <div class="profile-data-value">Active</div>
            </div>
        </div>

        <div class="profile-section">
            <h3 class="profile-section-title">
                <i class="fas fa-id-card"></i> Contact Information
            </h3>
            <div class="profile-data-row">
                <div class="profile-data-label">Email Address</div>
                <div class="profile-data-value"><?= htmlspecialchars($user['email'] ?? 'Not set') ?></div>
            </div>
            <div class="profile-data-row">
                <div class="profile-data-label">Phone Number</div>
                <div class="profile-data-value"><?= htmlspecialchars($user['phone'] ?? 'Not set') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Profile Information</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="" method="post" id="edit-profile-form">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" id="department" name="department" class="form-control" value="<?= htmlspecialchars($user['department'] ?? 'IT Department') ?>">
                </div>

                <hr class="my-4">
                <h4 class="mb-3">Change Password</h4>
                <p class="text-muted mb-4">Leave blank if you don't want to change your password</p>

                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
            <button type="submit" form="edit-profile-form" name="update_profile" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal functionality
        const modal = document.getElementById('editProfileModal');
        const editBtn = document.getElementById('edit-profile-link');
        const closeBtn = document.querySelector('.modal-close');
        const closeBtnFooter = document.querySelector('.modal-close-btn');

        function openModal() {
            modal.classList.add('show');
        }

        function closeModal() {
            modal.classList.remove('show');
        }

        editBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });

        closeBtn.addEventListener('click', closeModal);
        closeBtnFooter.addEventListener('click', closeModal);

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Password validation
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const form = document.getElementById('edit-profile-form');

        form.addEventListener('submit', function(e) {
            if (newPasswordInput.value && newPasswordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('New password and confirm password do not match.');
            }
        });
    });
</script>

<style>
    .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .text-muted {
        color: #6c757d;
    }

    /* Adjust spacing between profile card and info sections */
    .profile-container {
        display: flex;
        gap: 20px;
        /* Reduce the gap between sidebar and content */
        padding-left: 30px;
        /* Add padding to increase distance from dashboard menu */
        padding-right: 30px;
        padding-top: 20px;
    }

    .profile-sidebar {
        flex: 0 0 300px;
    }

    .profile-content {
        flex: 1;
    }

    /* Add some spacing between sections */
    .profile-section {
        margin-bottom: 20px;
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Make profile card more prominent */
    .profile-info {
        background-color: #1e4620;
        border-radius: 8px 8px 0 0;
        padding: 20px;
        text-align: center;
        color: white;
    }

    /* Better styling for the sidebar navigation */
    .profile-sidebar-nav {
        background-color: #1a3d1c;
        border-radius: 0 0 8px 8px;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .profile-sidebar-nav li a {
        display: block;
        padding: 15px 20px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .profile-sidebar-nav li a:hover,
    .profile-sidebar-nav li a.active {
        background-color: #2a582c;
    }
</style>

<?php
ob_end_flush();
include_once '../includes/footer.php';
?>
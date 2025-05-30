<?php
// Get user's profile photo from database if not already set in session
if (!isset($_SESSION['profile_photo']) && isset($_SESSION['admin_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT profile_photo FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();
        if ($user && $user['profile_photo']) {
            $_SESSION['profile_photo'] = $user['profile_photo'];
        } else {
            $_SESSION['profile_photo'] = '../images/default.jpg';
        }
    } catch (PDOException $e) {
        $_SESSION['profile_photo'] = '../images/default.jpg';
    }
}

// Get username if not already set
if (!isset($_SESSION['user_name']) && isset($_SESSION['admin_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT username, role FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_role'] = $user['role'] ?? 'Administrator';
        }
    } catch (PDOException $e) {
        // Silently fail
    }
}

// Determine current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>

<link rel="stylesheet" href="../css/layout.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="admin-navigation-container">
    <!-- Sidebar Navigation -->
    <nav class="dashboard-sidenav" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <a href="../dashboard/index.php">
                <i class="fas fa-shield-alt"></i>
                <span>Military System</span>
            </a>
        </div>
        
        <!-- Sidebar Menu -->
        <ul>
            <li>
                <a href="../dashboard/index.php" class="<?= $current_page == 'index.php' && $current_dir == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="../people/index.php" class="<?= $current_dir == 'people' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Manage People
                </a>
            </li>
            <li>
                <a href="../ranks/index.php" class="<?= $current_dir == 'ranks' ? 'active' : '' ?>">
                    <i class="fas fa-medal"></i> Manage Ranks
                </a>
            </li>
            <li>
                <a href="../units/index.php" class="<?= $current_dir == 'units' ? 'active' : '' ?>">
                    <i class="fas fa-sitemap"></i> Manage Units
                </a>
            </li>
            <li>
                <a href="../reports/index.php" class="<?= $current_dir == 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li>
                <a href="../profile/index.php" class="<?= $current_dir == 'profile' ? 'active' : '' ?>">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </li>
            <li>
                <a href="../admin/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
            <li><a href="../training/index.php" title="Training"><span>Manage Trainees</span></a></li        </ul>
    </nav>

    <!-- Top Header -->
    <header class="dashboard-header">
        <!-- Mobile Menu Toggle -->
        <div class="mobile-menu-toggle" id="mobile-toggle">
            <i class="fas fa-bars"></i>
        </div>
        
        <!-- Dashboard Title -->
        <div class="dashboard-title">
            <?php
            switch($current_dir) {
                case 'dashboard':
                    echo 'Dashboard';
                    break;
                case 'people':
                    echo 'Manage People';
                    break;
                case 'ranks':
                    echo 'Manage Ranks';
                    break;
                case 'units':
                    echo 'Manage Units';
                    break;
                case 'reports':
                    echo 'Reports';
                    break;
                case 'profile':
                    echo 'My Profile';
                    break;
                default:
                    echo 'Military Dashboard';
            }
            ?>
        </div>

        <div class="header-right">
            <!-- Profile Link -->
            <a href="../profile/index.php" class="profile-link">
                <img src="<?= htmlspecialchars($_SESSION['profile_photo'] ?? '../images/default.jpg') ?>" 
                    alt="Profile Picture" 
                    class="profile-pic"
                    id="header-profile-pic">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin User') ?></span>
                    <span class="user-role"><?= htmlspecialchars($_SESSION['user_role'] ?? 'Administrator') ?></span>
                </div>
            </a>
        </div>
    </header>
</div>

<script>
    // Mobile menu toggle functionality
    document.getElementById('mobile-toggle').addEventListener('click', function() {
        document.body.classList.toggle('sidebar-visible');
    });
</script>
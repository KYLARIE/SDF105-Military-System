<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . getBaseUrl() . "admin/login.php");
    exit();
}

// Function to get base URL
function getBaseUrl() {
    $base_dir = dirname(dirname($_SERVER['PHP_SELF']));
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
    
    if($base_dir != '/' && $base_dir != '\\') {
        $base_url .= $base_dir;
    }
    
    return $base_url . '/';
}

// Get the current page for menu highlighting
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Military System - <?php echo ucfirst($current_dir); ?></title>
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>css/layout.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Libre+Franklin:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include dirname(__DIR__) . '/dashboard/layout.php'; ?>
    
    <div class="dashboard-content">
        <!-- Page content will go here -->
    </div>
</body>
</html> 
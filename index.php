<?php
// index.php
session_start();

// Check if user is already logged in
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_username'])) {
    // User is logged in, redirect to dashboard
    header('Location: dashboard/index.php');
} else {
    // User is not logged in, redirect to login
    header('Location: admin/login.php');
}

exit(); // Ensure no further code is executed

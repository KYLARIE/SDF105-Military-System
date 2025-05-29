<?php
// config/auth.php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to login page
    header('Location: ../admin/login.php');
    exit();
}

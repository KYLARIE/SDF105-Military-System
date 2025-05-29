<?php
// config/db.php

// Database configuration
$host = 'localhost';     // database host (usually localhost)
$dbname = 'military';  // your database name
$username = 'root';  // your database username
$password = '';  // your database password

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connection success
    // echo "Database connected successfully.";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

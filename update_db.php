<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting database update process...\n";

// Simple database connection
$host = 'localhost';
$dbname = 'military';
$username = 'root';
$password = '';

try {
    echo "Connecting to MySQL server...\n";
    // Connect to MySQL without database first to check if database exists
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists, if not create it
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Database checked/created successfully.\n";
    
    // Connect to the specific database
    echo "Connecting to '$dbname' database...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if admins table exists
    echo "Checking if 'admins' table exists...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() == 0) {
        // Create admins table if it doesn't exist
        echo "Creating 'admins' table...\n";
        $pdo->exec("CREATE TABLE `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(100) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `profile_photo` VARCHAR(255) DEFAULT NULL,
            `role` VARCHAR(100) DEFAULT 'Administrator',
            `email` VARCHAR(255) DEFAULT NULL,
            `phone` VARCHAR(50) DEFAULT NULL,
            `department` VARCHAR(100) DEFAULT 'IT Department',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "Admins table created successfully.\n";
    } else {
        echo "Admins table already exists.\n";
        
        // Add columns if they don't exist
        echo "Checking for missing columns...\n";
        $columns = [
            'profile_photo' => 'VARCHAR(255) DEFAULT NULL',
            'role' => 'VARCHAR(100) DEFAULT \'Administrator\'',
            'email' => 'VARCHAR(255) DEFAULT NULL',
            'phone' => 'VARCHAR(50) DEFAULT NULL',
            'department' => 'VARCHAR(100) DEFAULT \'IT Department\''
        ];
        
        foreach ($columns as $column => $definition) {
            // Check if column exists
            echo "Checking for column '$column'...\n";
            $stmt = $pdo->query("SHOW COLUMNS FROM `admins` LIKE '$column'");
            if ($stmt->rowCount() == 0) {
                echo "Column '$column' doesn't exist. Adding it...\n";
                $pdo->exec("ALTER TABLE `admins` ADD COLUMN `$column` $definition");
                echo "Added column '$column' to admins table.\n";
            } else {
                echo "Column '$column' already exists.\n";
            }
        }
    }
    
    echo "Database update completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    
    // Additional debugging information
    echo "Error code: " . $e->getCode() . "\n";
    echo "Error occurred in file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
?> 
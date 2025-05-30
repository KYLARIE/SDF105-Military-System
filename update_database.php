<?php
// Include database configuration
require_once 'config/db.php';

echo "Starting database update...\n";

try {
    // Check if profile_photo column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'profile_photo'");
    if ($stmt->rowCount() == 0) {
        echo "Adding profile_photo column...\n";
        $pdo->exec("ALTER TABLE admins ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL");
    }
    
    // Check if role column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'");
    if ($stmt->rowCount() == 0) {
        echo "Adding role column...\n";
        $pdo->exec("ALTER TABLE admins ADD COLUMN role VARCHAR(100) DEFAULT 'Administrator'");
    }
    
    // Check if email column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'email'");
    if ($stmt->rowCount() == 0) {
        echo "Adding email column...\n";
        $pdo->exec("ALTER TABLE admins ADD COLUMN email VARCHAR(255) DEFAULT NULL");
    }
    
    // Check if phone column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'phone'");
    if ($stmt->rowCount() == 0) {
        echo "Adding phone column...\n";
        $pdo->exec("ALTER TABLE admins ADD COLUMN phone VARCHAR(50) DEFAULT NULL");
    }
    
    // Check if department column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'department'");
    if ($stmt->rowCount() == 0) {
        echo "Adding department column...\n";
        $pdo->exec("ALTER TABLE admins ADD COLUMN department VARCHAR(100) DEFAULT 'IT Department'");
    }
    
    echo "Database update completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?> 
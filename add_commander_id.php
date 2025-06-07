<?php
// Include database configuration
require_once 'config/db.php';

// Check if commander_id column exists
$columnExists = false;
try {
    $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'commander_id'");
    $columnExists = ($check->rowCount() > 0);
} catch (PDOException $e) {
    echo "Error checking column: " . $e->getMessage() . "\n";
    exit(1);
}

// If column doesn't exist, add it
if (!$columnExists) {
    try {
        // Add commander_id column
        $pdo->exec("ALTER TABLE units ADD COLUMN commander_id INT DEFAULT NULL");
        
        // Add foreign key constraint
        $pdo->exec("ALTER TABLE units
                    ADD CONSTRAINT fk_commander
                    FOREIGN KEY (commander_id) REFERENCES people(id)
                    ON DELETE SET NULL");
        
        echo "Successfully added commander_id column to units table.\n";
    } catch (PDOException $e) {
        echo "Error adding column: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "commander_id column already exists in units table.\n";
}

echo "Done.\n";
?> 
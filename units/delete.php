<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "No unit ID provided.";
    header("Location: index.php");
    exit();
}

$unit_id = (int)$_GET['id'];

try {
    // First, check if the unit exists
    $checkStmt = $pdo->prepare("SELECT id FROM units WHERE id = ?");
    $checkStmt->execute([$unit_id]);
    
    if ($checkStmt->rowCount() === 0) {
        $_SESSION['error_message'] = "Unit not found.";
        header("Location: index.php");
        exit();
    }
    
    // Update any personnel in this unit to have no unit
    $updateStmt = $pdo->prepare("UPDATE people SET unit_id = NULL WHERE unit_id = ?");
    $updateStmt->execute([$unit_id]);
    
    // Delete the unit
    $deleteStmt = $pdo->prepare("DELETE FROM units WHERE id = ?");
    $deleteStmt->execute([$unit_id]);
    
    $_SESSION['success_message'] = "Unit deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error deleting unit: " . $e->getMessage();
}

// Redirect back to index page
header("Location: index.php");
exit();

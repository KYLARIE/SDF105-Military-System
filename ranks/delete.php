<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "No rank ID provided.";
    header("Location: index.php");
    exit();
}

$rank_id = (int)$_GET['id'];

try {
    // First, check if the rank exists
    $checkStmt = $pdo->prepare("SELECT id FROM ranks WHERE id = ?");
    $checkStmt->execute([$rank_id]);
    
    if ($checkStmt->rowCount() === 0) {
        $_SESSION['error_message'] = "Rank not found.";
        header("Location: index.php");
        exit();
    }
    
    // Check if any personnel are using this rank
    $personStmt = $pdo->prepare("SELECT COUNT(*) FROM people WHERE rank_id = ?");
    $personStmt->execute([$rank_id]);
    $count = $personStmt->fetchColumn();
    
    if ($count > 0) {
        $_SESSION['error_message'] = "Cannot delete rank: It is assigned to {$count} personnel. Please reassign them first.";
        header("Location: index.php");
        exit();
    }
    
    // Delete the rank
    $deleteStmt = $pdo->prepare("DELETE FROM ranks WHERE id = ?");
    $deleteStmt->execute([$rank_id]);
    
    $_SESSION['success_message'] = "Rank deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error deleting rank: " . $e->getMessage();
}

// Redirect back to index page
header("Location: index.php");
exit();

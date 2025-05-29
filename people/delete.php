<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Check if person is a superior to others
$stmt = $pdo->prepare("SELECT COUNT(*) FROM people WHERE superior_id = ?");
$stmt->execute([$id]);
$isSuperior = $stmt->fetchColumn();

if ($isSuperior > 0) {
    header("Location: index.php?error=superior");
    exit();
}

$stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php?deleted=1");
exit();

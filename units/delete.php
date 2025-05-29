<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM units WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php?deleted=1");
exit();

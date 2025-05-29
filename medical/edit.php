<?php
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['health_status_name'];
    $stmt = $pdo->prepare("UPDATE health SET health_status_name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM health WHERE id = ?");
$stmt->execute([$id]);
$health = $stmt->fetch();

if (!$health) {
    echo "Health status not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Health Status</title>
</head>
<body>
    <h1>Edit Health Status</h1>
    <form method="POST">
        <label>Status Name:</label>
        <input type="text" name="health_status_name" value="<?= htmlspecialchars($health['health_status_name']) ?>" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>

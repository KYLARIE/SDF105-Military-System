<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['health_status_name'];
    $stmt = $pdo->prepare("INSERT INTO health (health_status_name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Health Status</title>
</head>
<body>
    <h1>Add Health Status</h1>
    <form method="POST">
        <label>Status Name:</label>
        <input type="text" name="health_status_name" required>
        <button type="submit">Save</button>
    </form>
</body>
</html>

<?php
require '../config/auth.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status_name = trim($_POST['status_name']);
    $description = trim($_POST['description'] ?? '');

    if (!empty($status_name)) {
        $stmt = $pdo->prepare("INSERT INTO statuses (status_name, description) VALUES (?, ?)");
        $stmt->execute([$status_name, $description]);
        header("Location: index.php?added=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Military Status</title>
    <link rel="stylesheet" href="../css/addBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <h1>Add Military Status</h1>

        <label>Status Name*:</label><br>
        <input type="text" name="status_name" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="3"></textarea><br><br>

        <button type="submit">Save Status</button>
    </form>
</body>

</html>
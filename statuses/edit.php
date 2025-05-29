<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM statuses WHERE id = ?");
$stmt->execute([$id]);
$status = $stmt->fetch();

if (!$status) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status_name = trim($_POST['status_name']);
    $description = trim($_POST['description'] ?? '');

    $stmt = $pdo->prepare("UPDATE statuses SET status_name = ?, description = ? WHERE id = ?");
    $stmt->execute([$status_name, $description, $id]);
    header("Location: index.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Military Status</title>
    <link rel="stylesheet" href="../css/editBtn.css">

</head>

<body>
    <a href="index.php" class="back-button" s>← Back</a>

    <form method="post">
        <h1>Edit Status</h1>
        <label>Status Name*:</label><br>
        <input type="text" name="status_name" value="<?= htmlspecialchars($status['status_name']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="3"><?= htmlspecialchars($status['description'] ?? '') ?></textarea><br><br>

        <button type="submit">Update Status</button>
    </form>
</body>

</html>
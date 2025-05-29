<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM units WHERE id = ?");
$stmt->execute([$id]);
$unit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$unit) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_name = trim($_POST['unit_name']);

    if (!empty($unit_name)) {
        $stmt = $pdo->prepare("UPDATE units SET unit_name = ? WHERE id = ?");
        $stmt->execute([$unit_name, $id]);
        header("Location: index.php?updated=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Unit</title>
    <link rel="stylesheet" href="../css/editBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <h1>Edit Unit</h1>
        <label>Unit Name:</label><br>
        <input type="text" name="unit_name" value="<?= htmlspecialchars($unit['unit_name']) ?>" required><br><br>
        <button type="submit">Update Unit</button>
    </form>
</body>

</html>
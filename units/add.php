<?php
require '../config/auth.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_name = trim($_POST['unit_name']);

    if (!empty($unit_name)) {
        $stmt = $pdo->prepare("INSERT INTO units (unit_name) VALUES (?)");
        $stmt->execute([$unit_name]);
        header("Location: index.php?added=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add New Unit</title>
    <link rel="stylesheet" href="../css/addBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <h1>Add New Unit</h1>

        <label>Unit Name:</label><br>
        <input type="text" name="unit_name" required><br><br>
        <button type="submit">Save Unit</button>
    </form>
</body>

</html>
<?php
require '../config/auth.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rank_name = trim($_POST['rank_name']);

    if (!empty($rank_name)) {
        $stmt = $pdo->prepare("INSERT INTO ranks (rank_name) VALUES (?)");
        $stmt->execute([$rank_name]);
        header("Location: index.php?added=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/addBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <title>Add New Rank</title>

        <h1>Add New Rank</h1>
        <label>Rank Name:</label><br>
        <input type="text" name="rank_name" required><br><br>
        <button type="submit">Save Rank</button>
    </form>
</body>

</html>
<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM ranks WHERE id = ?");
$stmt->execute([$id]);
$rank = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rank) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rank_name = trim($_POST['rank_name']);

    if (!empty($rank_name)) {
        $stmt = $pdo->prepare("UPDATE ranks SET rank_name = ? WHERE id = ?");
        $stmt->execute([$rank_name, $id]);
        header("Location: index.php?updated=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Rank</title>
    <link rel="stylesheet" href="../css/editBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <h1>Edit Rank</h1>
        <label>Rank Name:</label><br>
        <input type="text" name="rank_name" value="<?= htmlspecialchars($rank['rank_name']) ?>" required><br><br>
        <button type="submit">Update Rank</button>
    </form>
</body>

</html>
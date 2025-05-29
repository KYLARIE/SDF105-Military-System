<?php
require '../config/auth.php';
require '../config/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ranks WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

// Get all ranks
$stmt = $pdo->query("SELECT * FROM ranks ORDER BY rank_name");
$ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Ranks</title>
    <link rel="stylesheet" href="../css/styles.css">

</head>

<body>
    <h1>Manage Ranks</h1>
    <a href="../dashboard/index.php" class="button-link">← Dashboard</a>

    <?php if (isset($_GET['deleted'])): ?>
        <p>Rank deleted successfully!</p>
    <?php endif; ?>

    <a href="add.php" class="button-link">Add New Rank</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Rank Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($ranks as $rank): ?>
            <tr>
                <td><?= htmlspecialchars($rank['id']) ?></td>
                <td><?= htmlspecialchars($rank['rank_name']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $rank['id'] ?>" class="table-btn edit-btn">Edit</a>
                    <a href="index.php?delete=<?= $rank['id'] ?>"
                        onclick="return confirm('Delete this rank?')" class="table-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
<?php
require '../config/auth.php';
require '../config/db.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM statuses WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

$stmt = $pdo->query("SELECT * FROM statuses ORDER BY status_name");
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Military Statuses</title>
    <link rel="stylesheet" href="../css/styles.css">

</head>

<body>
    <h1>Military Statuses</h1>
    <a href="../dashboard/index.php" class="button-link">← Dashboard</a>

    <?php if (isset($_GET['deleted'])): ?>
        <p>Status deleted!</p>
    <?php endif; ?>

    <a href="add.php" class="button-link">+ Add Status</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Status Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($statuses as $status): ?>
            <tr>
                <td><?= htmlspecialchars($status['id']) ?></td>
                <td><?= htmlspecialchars($status['status_name']) ?></td>
                <td><?= htmlspecialchars($status['description'] ?? 'N/A') ?></td>
                <td>
                    <a href="edit.php?id=<?= $status['id'] ?>" class="table-btn edit-btn">Edit</a>
                    <a href="index.php?delete=<?= $status['id'] ?>" onclick="return confirm('Delete this status?')" class="table-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
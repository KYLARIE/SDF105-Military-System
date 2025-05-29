<?php
require '../config/db.php';

$stmt = $pdo->query("SELECT * FROM health ORDER BY id");
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Health Status List</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Health Status</h1>
    <a href="../dashboard/index.php" class="button-link">← Dashboard</a>
    <a href="add.php" class="button-link">+ Add Health Status</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Status Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($statuses as $status): ?>
            <tr>
                <td><?= htmlspecialchars($status['id']) ?></td>
                <td><?= htmlspecialchars($status['health_status_name']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $status['id'] ?>" class="table-btn edit-btn">Edit</a>
                    <a href="delete.php?id=<?= $status['id'] ?>" onclick="return confirm('Delete this status?')" class="table-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

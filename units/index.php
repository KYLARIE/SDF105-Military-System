<?php
require '../config/auth.php';
require '../config/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM units WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

// Get all units
$stmt = $pdo->query("SELECT * FROM units ORDER BY unit_name");
$units = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Units</title>
    <link rel="stylesheet" href="../css/styles.css">

</head>

<body>
    <h1>Manage Units</h1>
    <a href="../dashboard/index.php" class="button-link">← Dashboard</a>

    <?php if (isset($_GET['deleted'])): ?>
        <p>Unit deleted successfully!</p>
    <?php endif; ?>

    <a href="add.php" class="button-link">Add New Unit</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Unit Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($units as $unit): ?>
            <tr>
                <td><?= htmlspecialchars($unit['id']) ?></td>
                <td><?= htmlspecialchars($unit['unit_name']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $unit['id'] ?>" class="table-btn edit-btn">Edit</a>
                    <a href="index.php?delete=<?= $unit['id'] ?>"
                        onclick="return confirm('Delete this unit?')" class="table-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
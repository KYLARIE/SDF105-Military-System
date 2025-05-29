<?php
require '../config/auth.php';
require '../config/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

// Get all people with joined data
$stmt = $pdo->query("
    SELECT p.*, 
           r.rank_name,
           u.unit_name,
           s.name as superior_name
    FROM people p
    LEFT JOIN ranks r ON p.rank_id = r.id
    LEFT JOIN units u ON p.unit_id = u.id
    LEFT JOIN people s ON p.superior_id = s.id
    ORDER BY p.name
");
$people = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Military Personnel</title>
    <link rel="stylesheet" href="../css/styles.css">

</head>

<body>
    <h1>Military Personnel</h1>
    <a href="../dashboard/index.php" class="button-link">← Dashboard</a>

    <?php if (isset($_GET['deleted'])): ?>
        <p class="message">Record deleted successfully!</p>
    <?php endif; ?>

    <a href="add.php" class="button-link">+ Add Personnel</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Contact</th>
            <th>Rank</th>
            <th>Unit</th>
            <th>Status</th>
            <th>Superior</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($people as $person): ?>
            <tr>
                <td><?= htmlspecialchars($person['id']) ?></td>
                <td><?= htmlspecialchars($person['name']) ?></td>
                <td><?= htmlspecialchars($person['age']) ?></td>
                <td><?= htmlspecialchars($person['contact'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['unit_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['military_status']) ?></td>
                <td><?= htmlspecialchars($person['superior_name'] ?? 'None') ?></td>
                <td>
                    <a href="edit.php?id=<?= $person['id'] ?>" class="table-btn edit-btn">Edit</a>
                    <a href="index.php?delete=<?= $person['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
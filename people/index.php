<?php
require '../config/auth.php';
require '../config/db.php';
require 'filter_functions.php'; // ✅ include filter logic

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

// Filters
$filters = [
    'rank_id' => $_GET['rank_id'] ?? null,
    'unit_id' => $_GET['unit_id'] ?? null,
    'military_status' => $_GET['military_status'] ?? null,
    'health_id' => $_GET['health_id'] ?? null,
    'min_age' => $_GET['min_age'] ?? null,
    'max_age' => $_GET['max_age'] ?? null,
    'superior_id' => $_GET['superior_id'] ?? null,
    'has_email' => $_GET['has_email'] ?? null,
    'has_file' => $_GET['has_file'] ?? null,
    'name' => $_GET['name'] ?? null, 
];

$people = getFilteredPeople($pdo, $filters);

// Get dropdown options
$ranks = $pdo->query("SELECT id, rank_name FROM ranks ORDER BY rank_name")->fetchAll(PDO::FETCH_ASSOC);
$units = $pdo->query("SELECT id, unit_name FROM units ORDER BY unit_name")->fetchAll(PDO::FETCH_ASSOC);
$statuses = $pdo->query("SELECT id, status_name FROM statuses ORDER BY status_name")->fetchAll(PDO::FETCH_ASSOC);
$healthStatuses = $pdo->query("SELECT id, health_status_name FROM health ORDER BY health_status_name")->fetchAll(PDO::FETCH_ASSOC);
$superiors = $pdo->query("SELECT id, name FROM people ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Military Personnel</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Military Personnel</h1>
    <a href="../dashboard/index.php" class="button-link">&larr; Dashboard</a>

    <?php if (isset($_GET['deleted'])): ?>
        <p class="message">Record deleted successfully!</p>
    <?php endif; ?>

    <a href="add.php" class="button-link">+ Add Personnel</a>

    <form method="GET" class="filter-form">
        <h3>Filter Results</h3>
        <label>Search by Name:
    <input type="text" name="name" value="<?= htmlspecialchars($filters['name'] ?? '') ?>">
</label>

        
        <label>Rank:
            <select name="rank_id">
                <option value="">-- All --</option>
                <?php foreach ($ranks as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $filters['rank_id'] == $r['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['rank_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Unit:
            <select name="unit_id">
                <option value="">-- All --</option>
                <?php foreach ($units as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $filters['unit_id'] == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['unit_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Status:
            <select name="military_status">
                <option value="">-- All --</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filters['military_status'] == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['status_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Medical Status:
            <select name="health_id">
                <option value="">-- All --</option>
                <?php foreach ($healthStatuses as $h): ?>
                    <option value="<?= $h['id'] ?>" <?= $filters['health_id'] == $h['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($h['health_status_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Age Range:
            Min <input type="number" name="min_age" value="<?= htmlspecialchars($filters['min_age'] ?? '') ?>" style="width: 50px">
            Max <input type="number" name="max_age" value="<?= htmlspecialchars($filters['max_age'] ?? '') ?>" style="width: 50px">
        </label>

        <label>Superior:
            <select name="superior_id">
                <option value="">-- All --</option>
                <?php foreach ($superiors as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filters['superior_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label><input type="checkbox" name="has_email" value="1" <?= $filters['has_email'] ? 'checked' : '' ?>> Has Email</label>
        <label><input type="checkbox" name="has_file" value="1" <?= $filters['has_file'] ? 'checked' : '' ?>> Has Document</label>

        <button type="submit">Apply Filters</button>
        <a href="index.php">Reset</a>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Rank</th>
            <th>Unit</th>
            <th>Status</th>
            <th>Medical Status</th>
            <th>Superior</th>
            <th>Documents</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($people as $person): ?>
            <tr>
                <td><?= htmlspecialchars($person['id']) ?></td>
                <td><?= htmlspecialchars($person['name']) ?></td>
                <td><?= htmlspecialchars($person['age']) ?></td>
                <td><?= htmlspecialchars($person['contact'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['email'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['unit_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['military_status_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['health_status_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($person['superior_name'] ?? 'None') ?></td>
                <td>
                    <?php if (!empty($person['file_upload'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($person['file_upload']) ?>" target="_blank">View Document</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit.php?id=<?= $person['id'] ?>" class="table-btn edit-btn">Edit</a>
                    <a href="index.php?delete=<?= $person['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

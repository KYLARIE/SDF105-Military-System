<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Get person data
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM people WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch();

if (!$person) {
    header("Location: index.php");
    exit();
}

// Get dropdown data
$ranks = $pdo->query("SELECT * FROM ranks ORDER BY rank_name")->fetchAll();
$units = $pdo->query("SELECT * FROM units ORDER BY unit_name")->fetchAll();

// FIX: prepare + execute for personnel
$stmt = $pdo->prepare("SELECT * FROM people WHERE id != ? ORDER BY name");
$stmt->execute([$id]);
$personnel = $stmt->fetchAll();

// FIX: load statuses from database table
$statuses = $pdo->query("SELECT * FROM statuses ORDER BY status_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'age' => (int)$_POST['age'],
        'contact' => trim($_POST['contact'] ?? null),
        'rank_id' => !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
        'unit_id' => !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
        'military_status' => $_POST['military_status'],
        'superior_id' => !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null,
        'id' => $id
    ];

    $stmt = $pdo->prepare("
        UPDATE people SET 
        name = ?, age = ?, contact = ?, rank_id = ?, 
        unit_id = ?, military_status = ?, superior_id = ?
        WHERE id = ?
    ");
    $stmt->execute(array_values($data));
    header("Location: index.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Personnel Record</title>
    <link rel="stylesheet" href="../css/editBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <h1>Edit Personnel</h1>
        <label>Full Name*:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($person['name']) ?>" required><br><br>

        <label>Age*:</label><br>
        <input type="number" name="age" value="<?= htmlspecialchars($person['age']) ?>" min="18" max="70" required><br><br>

        <label>Contact Info:</label><br>
        <input type="text" name="contact" value="<?= htmlspecialchars($person['contact'] ?? '') ?>"><br><br>

        <label>Rank:</label><br>
        <select name="rank_id">
            <option value="">-- Select Rank --</option>
            <?php foreach ($ranks as $rank): ?>
                <option value="<?= $rank['id'] ?>" <?= $rank['id'] == $person['rank_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($rank['rank_name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Unit:</label><br>
        <select name="unit_id">
            <option value="">-- Select Unit --</option>
            <?php foreach ($units as $unit): ?>
                <option value="<?= $unit['id'] ?>" <?= $unit['id'] == $person['unit_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($unit['unit_name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Military Status*:</label><br>
        <select name="military_status" required>
            <option value="">-- Select Status --</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= htmlspecialchars($status['status_name']) ?>" <?= $status['status_name'] == $person['military_status'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($status['status_name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Superior Officer:</label><br>
        <select name="superior_id">
            <option value="">-- Select Superior --</option>
            <?php foreach ($personnel as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $p['id'] == $person['superior_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Update Record</button>
    </form>
</body>

</html>
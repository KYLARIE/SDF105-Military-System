<?php
require '../config/auth.php';
require '../config/db.php';

// Get data for dropdowns
$ranks = $pdo->query("SELECT * FROM ranks ORDER BY rank_name")->fetchAll();
$units = $pdo->query("SELECT * FROM units ORDER BY unit_name")->fetchAll();
$personnel = $pdo->query("SELECT * FROM people ORDER BY name")->fetchAll();
$statuses = ['Active Duty', 'Reserve', 'National Guard', 'Veteran', 'Retired', 'Dishonorably Discharged', 'AWOL'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'age' => (int)$_POST['age'],
        'contact' => trim($_POST['contact'] ?? null),
        'rank_id' => !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
        'unit_id' => !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
        'military_status' => $_POST['military_status'],
        'superior_id' => !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null
    ];

    $stmt = $pdo->prepare("
        INSERT INTO people 
        (name, age, contact, rank_id, unit_id, military_status, superior_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute(array_values($data));
    header("Location: index.php?added=1");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Military Personnel</title>
    <link rel="stylesheet" href="../css/addBtn.css">

</head>

<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post">
        <h1>Add New Personnel</h1>

        <label>Full Name*:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Age*:</label><br>
        <input type="number" name="age" min="18" max="70" required><br><br>

        <label>Contact Info:</label><br>
        <input type="text" name="contact"><br><br>

        <label>Rank:</label><br>
        <select name="rank_id">
            <option value="">-- Select Rank --</option>
            <?php foreach ($ranks as $rank): ?>
                <option value="<?= $rank['id'] ?>"><?= htmlspecialchars($rank['rank_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Unit:</label><br>
        <select name="unit_id">
            <option value="">-- Select Unit --</option>
            <?php foreach ($units as $unit): ?>
                <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['unit_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Military Status*:</label><br>
        <select name="military_status" required>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>"><?= $status ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Superior Officer:</label><br>
        <select name="superior_id">
            <option value="">-- Select Superior --</option>
            <?php foreach ($personnel as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Save Record</button>
    </form>
</body>

</html>
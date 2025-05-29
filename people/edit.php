<?php
require '../config/auth.php';
require '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Get person data
$stmt = $pdo->prepare("SELECT * FROM people WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch();

if (!$person) {
    header("Location: index.php");
    exit();
}

// Dropdowns
$ranks = $pdo->query("SELECT * FROM ranks ORDER BY rank_name")->fetchAll();
$units = $pdo->query("SELECT * FROM units ORDER BY unit_name")->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM people WHERE id != ? ORDER BY name");
$stmt->execute([$id]);
$personnel = $stmt->fetchAll();

$statuses = $pdo->query("SELECT * FROM statuses ORDER BY status_name")->fetchAll();
$health_statuses = $pdo->query("SELECT * FROM health ORDER BY health_status_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filePath = $person['file_upload'];

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = time() . '_' . basename($_FILES['file_upload']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetPath)) {
            $filePath = $targetPath;
        }
    }

    $data = [
        'name' => trim($_POST['name']),
        'age' => (int)$_POST['age'],
        'email' => trim($_POST['email'] ?? null),
        'contact' => trim($_POST['contact'] ?? null),
        'rank_id' => !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
        'unit_id' => !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
        'military_status' => $_POST['military_status'],
        'health_id' => !empty($_POST['health_id']) ? (int)$_POST['health_id'] : null,
        'superior_id' => !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null,
        'file_upload' => $filePath,
        'id' => $id
    ];

    $stmt = $pdo->prepare("
        UPDATE people SET 
            name = ?, age = ?, email = ?, contact = ?, rank_id = ?, 
            unit_id = ?, military_status = ?, health_id = ?, 
            superior_id = ?, file_upload = ?
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

<form method="post" enctype="multipart/form-data">
    <h1>Edit Personnel</h1>

    <label>Full Name*:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($person['name']) ?>" required><br><br>

    <label>Age*:</label><br>
    <input type="number" name="age" value="<?= htmlspecialchars($person['age']) ?>" min="18" max="70" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($person['email'] ?? '') ?>"><br><br>

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

    <label>Health Status:</label><br>
    <select name="health_id">
        <option value="">-- Select Health Status --</option>
        <?php foreach ($health_statuses as $health): ?>
            <option value="<?= $health['id'] ?>" <?= $health['id'] == $person['health_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($health['health_status_name']) ?>
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

    <label>Upload Document (optional):</label><br>
    <input type="file" name="file_upload"><br><br>

    <?php if (!empty($person['file_upload'])): ?>
        <p>Current File: <a href="<?= htmlspecialchars($person['file_upload']) ?>" target="_blank">View Document</a></p>
    <?php endif; ?>

    <button type="submit">Update Record</button>
</form>
</body>
</html>

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

$statuses = ['Active Duty', 'Reserve', 'National Guard', 'Veteran', 'Retired', 'Dishonorably Discharged', 'AWOL'];
$health_statuses = $pdo->query("SELECT * FROM health ORDER BY health_status_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profileImageName = $person['profile_image'];
    $filePath = $person['file_upload'];

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profile_pics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $profileImageName = time() . '_' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $profileImageName);
    }

    // Handle document upload
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = time() . '_' . basename($_FILES['file_upload']['name']);
        move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadDir . $fileName);
        $filePath = $fileName;
    }

    $data = [
        'name' => trim($_POST['name']),
        'age' => (int)$_POST['age'],
        'contact' => trim($_POST['contact'] ?? null),
        'email' => trim($_POST['email'] ?? null),
        'rank_id' => !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
        'unit_id' => !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
        'military_status' => $_POST['military_status'],
        'health_id' => !empty($_POST['health_id']) ? (int)$_POST['health_id'] : null,
        'superior_id' => !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null,
        'profile_image' => $profileImageName,
        'file_upload' => $filePath,
        'id' => $id
    ];

    $stmt = $pdo->prepare("
        UPDATE people SET 
            name = ?, age = ?, contact = ?, email = ?, rank_id = ?, 
            unit_id = ?, military_status = ?, health_id = ?, 
            superior_id = ?, profile_image = ?, file_upload = ?
        WHERE id = ?
    ");
    $stmt->execute(array_values($data));
    header("Location: index.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Military Personnel</title>
    <link rel="stylesheet" href="../css/editBtn.css">
    <style>
        /* Profile image box styling */
        #profileImageBox {
            width: 150px;
            height: 150px;
            margin: 20px auto 10px auto;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 14px;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            border: 3px dashed #666;
            box-shadow:
                0 0 0 1px white,
                1px 1px 0 1px #666 inset,
                -1px -1px 0 1px #666 inset;
            user-select: none;
            transition: background-color 0.3s;
        }
        #profileImageBox:hover {
            background-color: #f0f0f0;
        }
        .initials-preview {
            font-size: 48px;
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button">← Back</a>

    <form method="post" enctype="multipart/form-data" id="editPersonnelForm">
        <h1>Edit Personnel Record</h1>

        <!-- Profile Picture -->
        <label for="profileImageInput">Profile Picture (JPG, PNG):</label><br>
        <div id="profileImageBox">
            <?php if (!empty($person['profile_image'])): ?>
                <img src="../uploads/profile_pics/<?= htmlspecialchars($person['profile_image']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:5px;">
            <?php else: ?>
                <div class="initials-preview"><?= !empty($person['name']) ? substr(trim($person['name']), 0, 1) : '?' ?></div>
            <?php endif; ?>
        </div>
        <input type="file" name="profile_image" id="profileImageInput" accept="image/png, image/jpeg" style="display:none;"><br><br>

        <label>Full Name*:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($person['name']) ?>" required><br><br>

        <label>Age*:</label><br>
        <input type="number" name="age" value="<?= htmlspecialchars($person['age']) ?>" min="18" max="70" required><br><br>

        <label>Contact Info:</label><br>
        <input type="text" name="contact" value="<?= htmlspecialchars($person['contact'] ?? '') ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($person['email'] ?? '') ?>"><br><br>

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
            <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $status == $person['military_status'] ? 'selected' : '' ?>>
                    <?= $status ?>
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

        <label>Upload Document (PDF, DOCX, etc):</label><br>
        <input type="file" name="file_upload"><br><br>

        <?php if (!empty($person['file_upload'])): ?>
            <p>Current File: <a href="../uploads/documents/<?= htmlspecialchars($person['file_upload']) ?>" target="_blank">View Document</a></p>
        <?php endif; ?>

        <button type="submit">Update Record</button>
    </form>

    <script>
        const profileBox = document.getElementById('profileImageBox');
        const fileInput = document.getElementById('profileImageInput');

        profileBox.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;

            // Check file type
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert('Only JPG and PNG files are allowed.');
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                profileBox.innerHTML = ''; // Clear previous content
                const img = document.createElement('img');
                img.src = event.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '5px';
                profileBox.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
<?php
require_once '../config/db.php';
require_once '../config/auth.php';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "No personnel ID provided.";
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Get person data
try {
$stmt = $pdo->prepare("SELECT * FROM people WHERE id = ?");
$stmt->execute([$id]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$person) {
        $_SESSION['error_message'] = "Personnel not found.";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error retrieving personnel: " . $e->getMessage();
    header("Location: index.php");
    exit();
}

// Get dropdowns data
$ranks = $pdo->query("SELECT * FROM ranks ORDER BY rank_name")->fetchAll();
$units = $pdo->query("SELECT * FROM units ORDER BY unit_name")->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM people WHERE id != ? ORDER BY name");
$stmt->execute([$id]);
$personnel = $stmt->fetchAll();
$statuses = ['Active Duty', 'Reserve', 'National Guard', 'Veteran', 'Retired', 'Dishonorably Discharged', 'AWOL'];
$health_statuses = $pdo->query("SELECT * FROM health ORDER BY health_status_name")->fetchAll();

// Handle update personnel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personnel'])) {
    // Initialize variables with default values
    $profileImageName = null;

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $profileImageName = time() . '_' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $profileImageName);
    }

    try {
        // Check if profile_image column exists in the people table
        $checkColumn = $pdo->query("SHOW COLUMNS FROM people LIKE 'profile_image'");
        $columnExists = $checkColumn->rowCount() > 0;
        
        if ($columnExists) {
            // If column exists, include it in the update
            $stmt = $pdo->prepare("
                UPDATE people SET 
                    name = ?, age = ?, contact = ?, email = ?, rank_id = ?, 
                    unit_id = ?, military_status = ?, health_id = ?, 
                    superior_id = ?, profile_image = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                trim($_POST['name']),
                (int)$_POST['age'],
                trim($_POST['contact'] ?? null),
                trim($_POST['email'] ?? null),
                !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
                !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
                $_POST['military_status'],
                !empty($_POST['health_id']) ? (int)$_POST['health_id'] : null,
                !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null,
                $profileImageName ?: $person['profile_image'],
                $id
            ]);
        } else {
            // If column doesn't exist, exclude it from the update
    $stmt = $pdo->prepare("
        UPDATE people SET 
            name = ?, age = ?, contact = ?, email = ?, rank_id = ?, 
            unit_id = ?, military_status = ?, health_id = ?, 
                    superior_id = ?
        WHERE id = ?
    ");
            
            $stmt->execute([
                trim($_POST['name']),
                (int)$_POST['age'],
                trim($_POST['contact'] ?? null),
                trim($_POST['email'] ?? null),
                !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
                !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
                $_POST['military_status'],
                !empty($_POST['health_id']) ? (int)$_POST['health_id'] : null,
                !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null,
                $id
            ]);
            
            // Add the profile_image column to the people table
            $pdo->exec("ALTER TABLE people ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER health_id");
            
            // If we have a profile image to upload, update it now that the column exists
            if ($profileImageName) {
                $stmt = $pdo->prepare("UPDATE people SET profile_image = ? WHERE id = ?");
                $stmt->execute([$profileImageName, $id]);
            }
        }
        
        // Set success message
        $_SESSION['success_message'] = "Personnel updated successfully!";
        header("Location: index.php");
    exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating personnel: " . $e->getMessage();
    }
}

include_once '../includes/header.php';
?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Edit Personnel</h1>
    </div>
    <div class="page-actions">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Personnel
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error_message'] ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<!-- Edit Personnel Form -->
<section class="content-section">
    <div class="section-header">
        <h2>Personnel Details</h2>
    </div>
    <div class="section-body">
        <form action="" method="post" id="edit-personnel-form" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="profileImageInput">Profile Picture (click the square to upload)</label>
                    <div id="profileImageBox">
                        <?php if (isset($person['profile_image']) && !empty($person['profile_image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($person['profile_image']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:5px;">
                        <?php else: ?>
                            <div class="initials-preview"><?= !empty($person['name']) ? substr(trim($person['name']), 0, 1) : '?' ?></div>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="profile_image" id="profileImageInput" accept="image/png, image/jpeg" style="display:none;">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Full Name*</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?= htmlspecialchars($person['name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="age">Age*</label>
                    <input type="number" id="age" name="age" class="form-control" 
                           value="<?= htmlspecialchars($person['age'] ?? '') ?>" min="18" max="70" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="contact">Contact Info</label>
                    <input type="text" id="contact" name="contact" class="form-control" 
                           value="<?= htmlspecialchars($person['contact'] ?? '') ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($person['email'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="rank_id">Rank</label>
                    <select id="rank_id" name="rank_id" class="form-control">
                        <option value="">-- Select Rank --</option>
                        <?php foreach ($ranks as $rank): ?>
                            <option value="<?= $rank['id'] ?>" <?= ($person['rank_id'] ?? '') == $rank['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rank['rank_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="unit_id">Unit</label>
                    <select id="unit_id" name="unit_id" class="form-control">
                        <option value="">-- Select Unit --</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['id'] ?>" <?= ($person['unit_id'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($unit['unit_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="military_status">Military Status*</label>
                    <select id="military_status" name="military_status" class="form-control" required>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= ($person['military_status'] ?? '') == $status ? 'selected' : '' ?>>
                                <?= $status ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="health_id">Health Status</label>
                    <select id="health_id" name="health_id" class="form-control">
                        <option value="">-- Select Health Status --</option>
                        <?php foreach ($health_statuses as $health): ?>
                            <option value="<?= $health['id'] ?>" <?= ($person['health_id'] ?? '') == $health['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($health['health_status_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="superior_id">Superior Officer</label>
                    <select id="superior_id" name="superior_id" class="form-control">
                        <option value="">-- Select Superior --</option>
                        <?php foreach ($personnel as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($person['superior_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group col-md-12 text-center mt-4">
                <button type="submit" name="update_personnel" class="btn btn-primary">Update Personnel</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</section>

    <style>
.form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
    margin-bottom: 20px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 15px;
    padding-left: 15px;
    box-sizing: border-box;
}

.col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    box-sizing: border-box;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

        /* Profile image box styling */
        #profileImageBox {
            width: 150px;
            height: 150px;
    margin: 10px 0;
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

.current-file {
    margin-top: 8px;
    font-size: 14px;
}

.current-file a {
    color: #1e4620;
    text-decoration: none;
}

.current-file a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>

<?php include_once '../includes/footer.php'; ?>
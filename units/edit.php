<?php
require_once '../config/db.php';
require_once '../config/auth.php';
include_once '../includes/header.php';

// Check if commander_id column exists in units table
$hasCommanderId = false;
try {
    $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'commander_id'");
    $hasCommanderId = ($check->rowCount() > 0);
} catch (PDOException $e) {
    // Column check failed, assume no commander_id column
}

// Check if location column exists
$hasLocation = false;
try {
    $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'location'");
    $hasLocation = ($check->rowCount() > 0);
} catch (PDOException $e) {
    // Column check failed, assume no location column
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "No unit ID provided.";
    header("Location: index.php");
    exit();
}

$unit_id = (int)$_GET['id'];

// Get unit data
try {
    $stmt = $pdo->prepare("SELECT * FROM units WHERE id = ?");
    $stmt->execute([$unit_id]);
    $unit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$unit) {
        $_SESSION['error_message'] = "Unit not found.";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error retrieving unit: " . $e->getMessage();
    header("Location: index.php");
    exit();
}

// Handle update unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_unit'])) {
    $unit_name = trim($_POST['unit_name']);
    $location = trim($_POST['location'] ?? '');
    $commander_id = !empty($_POST['commander_id']) ? (int)$_POST['commander_id'] : null;
    
    try {
        if ($hasLocation && $hasCommanderId) {
            $stmt = $pdo->prepare("UPDATE units SET unit_name = ?, location = ?, commander_id = ? WHERE id = ?");
            $stmt->execute([$unit_name, $location, $commander_id, $unit_id]);
        } else if ($hasLocation) {
            $stmt = $pdo->prepare("UPDATE units SET unit_name = ?, location = ? WHERE id = ?");
            $stmt->execute([$unit_name, $location, $unit_id]);
        } else if ($hasCommanderId) {
            $stmt = $pdo->prepare("UPDATE units SET unit_name = ?, commander_id = ? WHERE id = ?");
            $stmt->execute([$unit_name, $commander_id, $unit_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE units SET unit_name = ? WHERE id = ?");
            $stmt->execute([$unit_name, $unit_id]);
        }
        
        // Handle personnel assignments if provided
        if (isset($_POST['personnel']) && is_array($_POST['personnel'])) {
            // First reset all personnel for this unit
            $resetStmt = $pdo->prepare("UPDATE people SET unit_id = NULL WHERE unit_id = ?");
            $resetStmt->execute([$unit_id]);
            
            // Then assign selected personnel to this unit
            $assignStmt = $pdo->prepare("UPDATE people SET unit_id = ? WHERE id = ?");
            foreach ($_POST['personnel'] as $person_id) {
                $assignStmt->execute([$unit_id, $person_id]);
            }
        }
        
        // Set success message
        $_SESSION['success_message'] = "Unit updated successfully!";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating unit: " . $e->getMessage();
    }
}

// Get all potential commanders
$commanders = [];
try {
    $cmdStmt = $pdo->query("SELECT p.id, p.first_name, p.last_name, p.rank_id, r.rank_name 
                          FROM people p 
                          LEFT JOIN ranks r ON p.rank_id = r.id
                          ORDER BY r.rank_name DESC, p.last_name ASC");
    while ($cmd = $cmdStmt->fetch(PDO::FETCH_ASSOC)) {
        $rank = !empty($cmd['rank_name']) ? $cmd['rank_name'] . ' ' : '';
        $commanders[$cmd['id']] = $rank . $cmd['first_name'] . ' ' . $cmd['last_name'];
    }
} catch (PDOException $e) {
    // Failed to get commanders
}

// Get all personnel for assignment
$allPersonnel = [];
try {
    $personStmt = $pdo->query("SELECT p.id, p.first_name, p.last_name, p.unit_id, r.rank_name 
                             FROM people p 
                             LEFT JOIN ranks r ON p.rank_id = r.id
                             ORDER BY r.rank_name DESC, p.last_name ASC");
    while ($person = $personStmt->fetch(PDO::FETCH_ASSOC)) {
        $rank = !empty($person['rank_name']) ? $person['rank_name'] . ' ' : '';
        $allPersonnel[$person['id']] = [
            'name' => $rank . $person['first_name'] . ' ' . $person['last_name'],
            'unit_id' => $person['unit_id']
        ];
    }
} catch (PDOException $e) {
    // Failed to get personnel
}

// Get all units for reference
$units = [];
try {
    $unitStmt = $pdo->query("SELECT id, unit_name FROM units ORDER BY unit_name");
    while ($u = $unitStmt->fetch(PDO::FETCH_ASSOC)) {
        $units[$u['id']] = $u['unit_name'];
    }
} catch (PDOException $e) {
    // Failed to get units
}
?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Edit Unit</h1>
    </div>
    <div class="page-actions">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Units
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

<!-- Edit Unit Form -->
<section class="content-section">
    <div class="section-header">
        <h2>Unit Details</h2>
    </div>
    <div class="section-body">
        <form action="" method="post" id="edit-unit-form">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="unit_name" class="form-label">Unit Name</label>
                    <input type="text" id="unit_name" name="unit_name" class="form-control" 
                           value="<?= htmlspecialchars($unit['unit_name']) ?>" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           value="<?= htmlspecialchars($unit['location'] ?? '') ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label for="commander_id" class="form-label">Commander</label>
                    <select id="commander_id" name="commander_id" class="form-control">
                        <option value="">-- Select Commander --</option>
                        <?php foreach ($commanders as $id => $name): ?>
                        <option value="<?= $id ?>" <?= (isset($unit['commander_id']) && $unit['commander_id'] == $id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h3>Assign Personnel</h3>
            <p class="text-muted mb-4">Select personnel to assign to this unit</p>
            
            <div class="form-group">
                <div class="personnel-table-container">
                    <table class="data-table" id="personnelTable">
                        <thead>
                            <tr>
                                <th width="40px"><input type="checkbox" id="select-all"></th>
                                <th>Name</th>
                                <th>Current Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allPersonnel as $id => $person): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="personnel[]" value="<?= $id ?>" class="personnel-checkbox" 
                                           <?= ($person['unit_id'] == $unit_id) ? 'checked' : '' ?>>
                                </td>
                                <td><?= htmlspecialchars($person['name']) ?></td>
                                <td class="current-unit">
                                    <?php 
                                    if (!empty($person['unit_id']) && isset($units[$person['unit_id']])) {
                                        echo htmlspecialchars($units[$person['unit_id']]);
                                    } else {
                                        echo 'Not Assigned';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="update_unit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</section>

<style>
.personnel-table-container {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
}

#personnelTable th:first-child,
#personnelTable td:first-child {
    text-align: center;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 15px;
    padding-left: 15px;
    box-sizing: border-box;
}

.my-4 {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.text-muted {
    color: #6c757d;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        const personnelCheckboxes = document.querySelectorAll('.personnel-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            personnelCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Update select all checkbox state based on individual checkboxes
        function updateSelectAllCheckbox() {
            let allChecked = true;
            personnelCheckboxes.forEach(function(checkbox) {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });
            selectAllCheckbox.checked = allChecked;
        }
        
        personnelCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateSelectAllCheckbox);
        });
        
        // Initialize select all checkbox state
        updateSelectAllCheckbox();
    });
</script>

<?php include_once '../includes/footer.php'; ?>
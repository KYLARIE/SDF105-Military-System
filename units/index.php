<!DOCTYPE html>
<html>
<head>
    <!-- Add export CSS -->
    <link rel="stylesheet" href="../css/export.css">
</head>
<body>
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

// Handle add new unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit'])) {
    $unit_name = trim($_POST['unit_name']);
    $location = trim($_POST['location'] ?? '');
    $commander_id = !empty($_POST['commander_id']) ? (int)$_POST['commander_id'] : null;

    try {
        // Check if location column exists
        try {
            $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'location'");
            $locationExists = ($check->rowCount() > 0);

            // Insert unit with commander_id
            if ($locationExists) {
                $stmt = $pdo->prepare("INSERT INTO units (unit_name, location, commander_id) VALUES (?, ?, ?)");
                $stmt->execute([$unit_name, $location, $commander_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO units (unit_name, commander_id) VALUES (?, ?)");
                $stmt->execute([$unit_name, $commander_id]);
            }
        } catch (PDOException $e) {
            // If error checking columns, just insert unit_name
            $stmt = $pdo->prepare("INSERT INTO units (unit_name) VALUES (?)");
            $stmt->execute([$unit_name]);
        }

        // Set success message
        $_SESSION['success_message'] = "Unit added successfully!";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error adding unit: " . $e->getMessage();
    }
}

// Handle update unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_unit'])) {
    $unit_id = (int)$_POST['unit_id'];
    $unit_name = trim($_POST['unit_name']);
    $location = trim($_POST['location'] ?? '');
    $commander_id = !empty($_POST['commander_id']) ? (int)$_POST['commander_id'] : null;

    try {
        // Check if location column exists
        try {
            $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'location'");
            $locationExists = ($check->rowCount() > 0);

            if ($locationExists) {
                $stmt = $pdo->prepare("UPDATE units SET unit_name = ?, location = ?, commander_id = ? WHERE id = ?");
                $stmt->execute([$unit_name, $location, $commander_id, $unit_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE units SET unit_name = ?, commander_id = ? WHERE id = ?");
                $stmt->execute([$unit_name, $commander_id, $unit_id]);
            }
        } catch (PDOException $e) {
            // If error checking columns, just update unit_name
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
?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Manage Units</h1>
    </div>
    <div class="page-actions">
        <button id="addUnitBtn" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Unit
        </button>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success_message'] ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error_message'] ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<!-- Units List -->
<section class="content-section">
    <div class="section-header">
        <h2>Units List</h2>
        <div class="section-actions">
            <div class="search-mini">
                <input type="text" id="searchInput" placeholder="Search units...">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>
    <div class="section-body">
        <table class="data-table" id="unitsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Unit Name</th>
                    <th>Location</th>
                    <th>Commander</th>
                    <th>Personnel Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    // Check if location column exists
                    $hasLocation = false;
                    try {
                        $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'location'");
                        $hasLocation = ($check->rowCount() > 0);
                    } catch (PDOException $e) {
                        // Column check failed, assume no location column
                    }

                    // Get units data
                    if ($hasLocation && $hasCommanderId) {
                        $stmt = $pdo->query("SELECT u.id, u.unit_name, u.location, u.commander_id,
                                            (SELECT COUNT(*) FROM people WHERE unit_id = u.id) as personnel_count
                                            FROM units u
                                            ORDER BY u.id");
                    } else if ($hasLocation) {
                        $stmt = $pdo->query("SELECT u.id, u.unit_name, u.location,
                                            (SELECT COUNT(*) FROM people WHERE unit_id = u.id) as personnel_count
                                            FROM units u
                                            ORDER BY u.id");
                    } else if ($hasCommanderId) {
                        $stmt = $pdo->query("SELECT u.id, u.unit_name, u.commander_id,
                                            (SELECT COUNT(*) FROM people WHERE unit_id = u.id) as personnel_count
                                            FROM units u
                                            ORDER BY u.id");
                    } else {
                        $stmt = $pdo->query("SELECT u.id, u.unit_name,
                                            (SELECT COUNT(*) FROM people WHERE unit_id = u.id) as personnel_count
                                            FROM units u
                                            ORDER BY u.id");
                    }

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Determine commander name
                        $commander_name = 'Not Assigned';
                        if ($hasCommanderId && isset($row['commander_id']) && isset($commanders[$row['commander_id']])) {
                            $commander_name = htmlspecialchars($commanders[$row['commander_id']]);
                        }

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['unit_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['location'] ?? 'N/A') . '</td>';
                        echo '<td>' . $commander_name . '</td>';
                        echo '<td>' . htmlspecialchars($row['personnel_count']) . '</td>';
                        echo '<td>
                                <a href="view.php?id=' . $row['id'] . '" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i></a>
                                <a href="#" class="action-btn btn-edit edit-unit-btn" data-id="' . $row['id'] . '" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=' . $row['id'] . '" class="action-btn btn-delete" title="Delete" onclick="return confirm(\'Are you sure you want to delete this unit?\');"><i class="fas fa-trash"></i></a>
                              </td>';
                        echo '</tr>';
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="6">Error: ' . $e->getMessage() . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Add Unit Modal -->
<div class="modal" id="addUnitModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Unit</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="" method="post" id="add-unit-form">
                <div class="form-group">
                    <label for="unit_name" class="form-label">Unit Name</label>
                    <input type="text" id="unit_name" name="unit_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location" class="form-control">
                </div>

                <div class="form-group">
                    <label for="commander_id" class="form-label">Commander</label>
                    <select id="commander_id" name="commander_id" class="form-control">
                        <option value="">-- Select Commander --</option>
                        <?php foreach ($commanders as $id => $name): ?>
                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
            <button type="submit" form="add-unit-form" name="add_unit" class="btn btn-primary">Add Unit</button>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal" id="editUnitModal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Edit Unit</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="" method="post" id="edit-unit-form">
                <input type="hidden" id="edit_unit_id" name="unit_id">

                <div class="form-group">
                    <label for="edit_unit_name" class="form-label">Unit Name</label>
                    <input type="text" id="edit_unit_name" name="unit_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_location" class="form-label">Location</label>
                    <input type="text" id="edit_location" name="location" class="form-control">
                </div>

                <div class="form-group">
                    <label for="edit_commander_id" class="form-label">Commander</label>
                    <select id="edit_commander_id" name="commander_id" class="form-control">
                        <option value="">-- Select Commander --</option>
                        <?php foreach ($commanders as $id => $name): ?>
                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr>

                <h4>Assign Personnel</h4>
                <p class="text-muted">Select personnel to assign to this unit</p>

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
                                            data-unit-id="<?= $person['unit_id'] ?>">
                                    </td>
                                    <td><?= htmlspecialchars($person['name']) ?></td>
                                    <td class="current-unit">
                                        <?php
                                        if (!empty($person['unit_id']) && isset($units)) {
                                            foreach ($units as $unit) {
                                                if ($unit['id'] == $person['unit_id']) {
                                                    echo htmlspecialchars($unit['unit_name']);
                                                    break;
                                                }
                                            }
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
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
            <button type="submit" form="edit-unit-form" name="update_unit" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</div>

<style>
    .modal-lg {
        width: 800px;
        max-width: 90%;
    }

    .personnel-table-container {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
    }

    #personnelTable th:first-child,
    #personnelTable td:first-child {
        text-align: center;
    }
</style>

<script>
    // Unit data for edit modal
    const unitData = {};
    <?php foreach ($units as $unit): ?>
    unitData[<?= $unit['id'] ?>] = {
        id: <?= $unit['id'] ?>,
        name: "<?= addslashes($unit['unit_name']) ?>",
        location: "<?= isset($unit['location']) ? addslashes($unit['location']) : '' ?>",
        commander_id: <?= isset($unit['commander_id']) && !empty($unit['commander_id']) ? $unit['commander_id'] : 'null' ?>
    };
    <?php endforeach; ?>

    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('unitsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const rowText = rows[i].textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });

        // Add Unit Modal
        const addModal = document.getElementById('addUnitModal');
        const addUnitBtn = document.getElementById('addUnitBtn');
        const addCloseBtn = addModal.querySelector('.modal-close');
        const addCloseBtnFooter = addModal.querySelector('.modal-close-btn');

        function openAddModal() {
            addModal.classList.add('show');
        }

        function closeAddModal() {
            addModal.classList.remove('show');
        }

        addUnitBtn.addEventListener('click', openAddModal);
        addCloseBtn.addEventListener('click', closeAddModal);
        addCloseBtnFooter.addEventListener('click', closeAddModal);

        // Edit Unit Modal
        const editModal = document.getElementById('editUnitModal');
        const editBtns = document.querySelectorAll('.edit-unit-btn');
        const editCloseBtn = editModal.querySelector('.modal-close');
        const editCloseBtnFooter = editModal.querySelector('.modal-close-btn');
        const editForm = document.getElementById('edit-unit-form');

        function openEditModal(unitId) {
            // Fill form with unit data
            const unit = unitData[unitId];
            document.getElementById('edit_unit_id').value = unit.id;
            document.getElementById('edit_unit_name').value = unit.name;
            document.getElementById('edit_location').value = unit.location || '';

            const commanderSelect = document.getElementById('edit_commander_id');
            if (unit.commander_id) {
                commanderSelect.value = unit.commander_id;
            } else {
                commanderSelect.selectedIndex = 0;
            }

            // Check personnel checkboxes for this unit
            const checkboxes = document.querySelectorAll('.personnel-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = (checkbox.getAttribute('data-unit-id') == unit.id);
            });

            editModal.classList.add('show');
        }

        function closeEditModal() {
            editModal.classList.remove('show');
        }

        editBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const unitId = this.getAttribute('data-id');
                openEditModal(unitId);
            });
        });

        editCloseBtn.addEventListener('click', closeEditModal);
        editCloseBtnFooter.addEventListener('click', closeEditModal);

        // Select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        const personnelCheckboxes = document.querySelectorAll('.personnel-checkbox');

        selectAllCheckbox.addEventListener('change', function() {
            personnelCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === addModal) {
                closeAddModal();
            }
            if (e.target === editModal) {
                closeEditModal();
            }
        });
    });
</script>

<!-- Add export functionality -->
<script src="../js/export.js"></script>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
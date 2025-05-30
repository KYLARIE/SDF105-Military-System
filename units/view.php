<?php
require_once '../config/db.php';
require_once '../config/auth.php';
include_once '../includes/header.php';

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

// Check if commander_id column exists and get commander details
$commander = null;
try {
    $check = $pdo->query("SHOW COLUMNS FROM units LIKE 'commander_id'");
    $hasCommanderId = ($check->rowCount() > 0);
    
    if ($hasCommanderId && !empty($unit['commander_id'])) {
        $cmdStmt = $pdo->prepare("SELECT p.id, p.first_name, p.last_name, r.rank_name 
                                FROM people p 
                                LEFT JOIN ranks r ON p.rank_id = r.id
                                WHERE p.id = ?");
        $cmdStmt->execute([$unit['commander_id']]);
        $commander = $cmdStmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Failed to get commander
}

// Get personnel assigned to this unit
$personnel = [];
try {
    $personStmt = $pdo->prepare("SELECT p.id, p.first_name, p.last_name, r.rank_name 
                               FROM people p 
                               LEFT JOIN ranks r ON p.rank_id = r.id
                               WHERE p.unit_id = ?
                               ORDER BY r.rank_name DESC, p.last_name ASC");
    $personStmt->execute([$unit_id]);
    $personnel = $personStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Failed to get personnel
}
?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Unit Details</h1>
    </div>
    <div class="page-actions">
        <a href="edit.php?id=<?= $unit_id ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Unit
        </a>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Units
        </a>
    </div>
</div>

<!-- Unit Details -->
<section class="content-section">
    <div class="section-header">
        <h2><?= htmlspecialchars($unit['unit_name']) ?></h2>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <div class="detail-row">
                    <div class="detail-label">Unit Name:</div>
                    <div class="detail-value"><?= htmlspecialchars($unit['unit_name']) ?></div>
                </div>
                
                <?php if (isset($unit['location'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value"><?= htmlspecialchars($unit['location'] ?? 'Not Specified') ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($commander): ?>
                <div class="detail-row">
                    <div class="detail-label">Commander:</div>
                    <div class="detail-value">
                        <?php 
                        $rank = !empty($commander['rank_name']) ? $commander['rank_name'] . ' ' : '';
                        echo htmlspecialchars($rank . $commander['first_name'] . ' ' . $commander['last_name']);
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <div class="detail-label">Personnel Count:</div>
                    <div class="detail-value"><?= count($personnel) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Personnel List -->
<section class="content-section">
    <div class="section-header">
        <h2>Assigned Personnel</h2>
    </div>
    <div class="section-body">
        <?php if (count($personnel) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personnel as $person): ?>
                <tr>
                    <td><?= htmlspecialchars($person['id']) ?></td>
                    <td><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?></td>
                    <td>
                        <a href="../people/view.php?id=<?= $person['id'] ?>" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i></a>
                        <a href="../people/edit.php?id=<?= $person['id'] ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info">No personnel assigned to this unit.</div>
        <?php endif; ?>
    </div>
</section>

<style>
.detail-row {
    display: flex;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.detail-label {
    font-weight: bold;
    width: 150px;
    flex-shrink: 0;
}

.detail-value {
    flex-grow: 1;
}

.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.card-body {
    padding: 1.5rem;
}
</style>

<?php include_once '../includes/footer.php'; ?> 
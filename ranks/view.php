<?php
require_once '../config/db.php';
require_once '../config/auth.php';
include_once '../includes/header.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "No rank ID provided.";
    header("Location: index.php");
    exit();
}

$rank_id = (int)$_GET['id'];

// Get rank data
try {
    $stmt = $pdo->prepare("SELECT * FROM ranks WHERE id = ?");
    $stmt->execute([$rank_id]);
    $rank = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$rank) {
        $_SESSION['error_message'] = "Rank not found.";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error retrieving rank: " . $e->getMessage();
    header("Location: index.php");
    exit();
}

// Get personnel with this rank
$personnel = [];
try {
    $personStmt = $pdo->prepare("SELECT p.id, p.first_name, p.last_name, u.unit_name 
                               FROM people p 
                               LEFT JOIN units u ON p.unit_id = u.id
                               WHERE p.rank_id = ?
                               ORDER BY p.last_name ASC");
    $personStmt->execute([$rank_id]);
    $personnel = $personStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Failed to get personnel
}
?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Rank Details</h1>
    </div>
    <div class="page-actions">
        <a href="edit.php?id=<?= $rank_id ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Rank
        </a>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Ranks
        </a>
    </div>
</div>

<!-- Rank Details -->
<section class="content-section">
    <div class="section-header">
        <h2><?= htmlspecialchars($rank['rank_name']) ?></h2>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <div class="detail-row">
                    <div class="detail-label">Rank Name:</div>
                    <div class="detail-value"><?= htmlspecialchars($rank['rank_name']) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Abbreviation:</div>
                    <div class="detail-value"><?= htmlspecialchars($rank['abbreviation'] ?? 'Not Specified') ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Pay Grade:</div>
                    <div class="detail-value"><?= htmlspecialchars($rank['pay_grade'] ?? 'Not Specified') ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Category:</div>
                    <div class="detail-value"><?= htmlspecialchars($rank['category'] ?? 'Not Specified') ?></div>
                </div>
                
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
        <h2>Personnel with this Rank</h2>
    </div>
    <div class="section-body">
        <?php if (count($personnel) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Unit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personnel as $person): ?>
                <tr>
                    <td><?= htmlspecialchars($person['id']) ?></td>
                    <td><?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?></td>
                    <td><?= htmlspecialchars($person['unit_name'] ?? 'Not Assigned') ?></td>
                    <td>
                        <a href="../people/view.php?id=<?= $person['id'] ?>" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i></a>
                        <a href="../people/edit.php?id=<?= $person['id'] ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info">No personnel assigned to this rank.</div>
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
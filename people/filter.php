<?php ob_start(); ?>


<link rel="stylesheet" href="filter.css">
<link rel="stylesheet" href="index.css">
<?php
require_once '../config/db.php';
require_once '../config/auth.php';
require_once 'filter_functions.php';
include_once '../includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

// Check view preference
$view = $_GET['view'] ?? 'table';

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

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Military Personnel</h1>
        <nav class="breadcrumb">

        </nav>
    </div>
    <div class="page-actions">
        <button id="openAddPersonnelModal" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Personnel
        </button>
    </div>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">
        Record deleted successfully!
    </div>
<?php endif; ?>

<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">
        Personnel added successfully!
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success_message'] ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<!-- Personnel List -->
<section class="content-section">
    <div class="section-header">
        <div class="btn-group-container">
            <div class="btn-group">
                <!-- View toggle buttons -->
                <a href="?view=table" class="view-toggle-btn <?= $view === 'table' ? 'active' : '' ?>">Table View</a>
                <a href="?view=cards" class="view-toggle-btn <?= $view === 'cards' ? 'active' : '' ?>">Card View</a>

                <!-- Export dropdown -->
                <div class="export-dropdown">
                    <button class="export-btn">Export ▼</button>
                    <div class="export-dropdown-content">
                        <?php
                        // Convert filters to query string
                        $queryString = http_build_query($filters);
                        ?>
                        <a href="?export_format=csv&<?= $queryString ?>">CSV</a>
                        <a href="?export_format=excel&<?= $queryString ?>">Excel</a>
                        <a href="?export_format=html&<?= $queryString ?>">HTML</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-card">
        <h3><i class="fas fa-filter"></i> Filters</h3>
        <form method="GET" class="filter-form">
            <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">

            <div class="form-row">


                <div class="form-group">
                    <label>Rank</label>
                    <select name="rank_id">
                        <option value="">All Ranks</option>
                        <?php foreach ($ranks as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $filters['rank_id'] == $r['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['rank_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Unit</label>
                    <select name="unit_id">
                        <option value="">All Units</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $filters['unit_id'] == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['unit_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="military_status">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $filters['military_status'] == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['status_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Medical Status</label>
                    <select name="health_id">
                        <option value="">All Statuses</option>
                        <?php foreach ($healthStatuses as $h): ?>
                            <option value="<?= $h['id'] ?>" <?= $filters['health_id'] == $h['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($h['health_status_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Age Range</label>
                    <div class="range-inputs">
                        <input type="number" name="min_age" value="<?= htmlspecialchars($filters['min_age'] ?? '') ?>" placeholder="Min" min="18" max="100">
                        <span>to</span>
                        <input type="number" name="max_age" value="<?= htmlspecialchars($filters['max_age'] ?? '') ?>" placeholder="Max" min="18" max="100">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Superior</label>
                    <select name="superior_id">
                        <option value="">All Superiors</option>
                        <?php foreach ($superiors as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $filters['superior_id'] == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-container">
                        <input type="checkbox" name="has_email" value="1" <?= $filters['has_email'] ? 'checked' : '' ?>>
                        <span class="checkmark"></span>
                        Has Email
                    </label>

                </div>

                <div class="form-group actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>

    </div>
    <?php ob_end_flush(); ?>
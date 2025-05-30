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

// Handle update rank
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rank'])) {
    $rank_name = trim($_POST['rank_name']);
    $abbreviation = trim($_POST['abbreviation'] ?? '');
    $pay_grade = trim($_POST['pay_grade'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    try {
        $stmt = $pdo->prepare("UPDATE ranks SET rank_name = ?, abbreviation = ?, pay_grade = ?, category = ? WHERE id = ?");
        $stmt->execute([$rank_name, $abbreviation, $pay_grade, $category, $rank_id]);
        
        // Set success message
        $_SESSION['success_message'] = "Rank updated successfully!";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating rank: " . $e->getMessage();
    }
}

?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Edit Rank</h1>
    </div>
    <div class="page-actions">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Ranks
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

<!-- Edit Rank Form -->
<section class="content-section">
    <div class="section-header">
        <h2>Rank Details</h2>
    </div>
    <div class="section-body">
        <form action="" method="post" id="edit-rank-form">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="rank_name" class="form-label">Rank Name</label>
                    <input type="text" id="rank_name" name="rank_name" class="form-control" 
                           value="<?= htmlspecialchars($rank['rank_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="abbreviation" class="form-label">Abbreviation</label>
                    <input type="text" id="abbreviation" name="abbreviation" class="form-control" 
                           value="<?= htmlspecialchars($rank['abbreviation'] ?? '') ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label for="pay_grade" class="form-label">Pay Grade</label>
                    <input type="text" id="pay_grade" name="pay_grade" class="form-control" 
                           value="<?= htmlspecialchars($rank['pay_grade'] ?? '') ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label for="category" class="form-label">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="Enlisted" <?= ($rank['category'] ?? '') == 'Enlisted' ? 'selected' : '' ?>>Enlisted</option>
                        <option value="Officer" <?= ($rank['category'] ?? '') == 'Officer' ? 'selected' : '' ?>>Officer</option>
                        <option value="Warrant Officer" <?= ($rank['category'] ?? '') == 'Warrant Officer' ? 'selected' : '' ?>>Warrant Officer</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="update_rank" class="btn btn-primary">Save Changes</button>
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
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 15px;
    padding-left: 15px;
    box-sizing: border-box;
}

.form-actions {
    margin-top: 2rem;
}
</style>

<?php include_once '../includes/footer.php'; ?>
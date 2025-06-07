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

// Handle add new rank
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rank'])) {
    $rank_name = trim($_POST['rank_name'] ?? '');
    $abbreviation = trim($_POST['abbreviation'] ?? '');
    $pay_grade = trim($_POST['pay_grade'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    try {
        $stmt = $pdo->prepare("INSERT INTO ranks (rank_name, abbreviation, pay_grade, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$rank_name, $abbreviation, $pay_grade, $category]);
        
        // Set success message
        $_SESSION['success_message'] = "Rank added successfully!";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error adding rank: " . $e->getMessage();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ranks WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

// Get all ranks
$stmt = $pdo->query("SELECT * FROM ranks ORDER BY rank_name");
$ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page Title and Actions -->
<div class="page-header">
    <div>
        <h1>Manage Ranks</h1>
    </div>
    <div class="page-actions">
        <button id="addRankBtn" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Rank
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

<!-- Ranks List -->
<section class="content-section">
    <div class="section-header">
        <h2>Ranks List</h2>
        <div class="section-actions">
            <div class="search-mini">
                <input type="text" id="searchInput" placeholder="Search ranks...">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>
    <div class="section-body">
        <table class="data-table" id="ranksTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Rank Name</th>
                    <th>Abbreviation</th>
                    <th>Pay Grade</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM ranks ORDER BY id");
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['rank_name'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($row['abbreviation'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($row['pay_grade'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($row['category'] ?? 'N/A') . '</td>';
                        echo '<td>
                                <a href="view.php?id=' . $row['id'] . '" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i></a>
                                <a href="edit.php?id=' . $row['id'] . '" class="action-btn btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=' . $row['id'] . '" class="action-btn btn-delete" title="Delete" onclick="return confirm(\'Are you sure you want to delete this rank?\');"><i class="fas fa-trash"></i></a>
                              </td>';
                        echo '</tr>';
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="6">No ranks found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Add Rank Modal -->
<div class="modal" id="addRankModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Rank</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form action="" method="post" id="add-rank-form">
                <div class="form-group">
                    <label for="rank_name" class="form-label">Rank Name</label>
                    <input type="text" id="rank_name" name="rank_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="abbreviation" class="form-label">Abbreviation</label>
                    <input type="text" id="abbreviation" name="abbreviation" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="pay_grade" class="form-label">Pay Grade</label>
                    <input type="text" id="pay_grade" name="pay_grade" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="category" class="form-label">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="Enlisted">Enlisted</option>
                        <option value="Officer">Officer</option>
                        <option value="Warrant Officer">Warrant Officer</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
            <button type="submit" form="add-rank-form" name="add_rank" class="btn btn-primary">Add Rank</button>
        </div>
    </div>
</div>

<script>
    // Simple search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('ranksTable');
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
        
        // Modal functionality
        const modal = document.getElementById('addRankModal');
        const addRankBtn = document.getElementById('addRankBtn');
        const closeBtn = document.querySelector('.modal-close');
        const closeBtnFooter = document.querySelector('.modal-close-btn');
        
        function openModal() {
            modal.classList.add('show');
        }
        
        function closeModal() {
            modal.classList.remove('show');
        }
        
        addRankBtn.addEventListener('click', openModal);
        
        closeBtn.addEventListener('click', closeModal);
        closeBtnFooter.addEventListener('click', closeModal);
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>

<!-- Add export functionality -->
<script src="../js/export.js"></script>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
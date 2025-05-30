<?php
require_once '../config/db.php';
require_once '../config/auth.php';
include_once '../includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?deleted=1");
    exit();
}

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
        <h1>Manage Personnel</h1>
    </div>
    <div class="page-actions">
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Personnel
        </a>
    </div>
</div>

<!-- Personnel List -->
<section class="content-section">
    <div class="section-header">
        <h2>Personnel List</h2>
        <div class="section-actions">
            <div class="search-mini">
                <input type="text" id="searchInput" placeholder="Search personnel...">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>
    <div class="section-body">
        <table class="data-table" id="personnelTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Rank</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT p.id, p.first_name, p.last_name, r.name as rank_name, u.name as unit_name, p.status 
                                        FROM people p 
                                        LEFT JOIN ranks r ON p.rank_id = r.id 
                                        LEFT JOIN units u ON p.unit_id = u.id 
                                        ORDER BY p.id");
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $status_class = '';
                        switch(strtolower($row['status'] ?? 'active')) {
                            case 'active':
                                $status_class = 'status-active';
                                break;
                            case 'inactive':
                                $status_class = 'status-inactive';
                                break;
                            default:
                                $status_class = 'status-pending';
                        }
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['rank_name'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($row['unit_name'] ?? 'N/A') . '</td>';
                        echo '<td><span class="status ' . $status_class . '">' . htmlspecialchars($row['status'] ?? 'Active') . '</span></td>';
                        echo '<td>
                                <a href="view.php?id=' . $row['id'] . '" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i></a>
                                <a href="edit.php?id=' . $row['id'] . '" class="action-btn btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=' . $row['id'] . '" class="action-btn btn-delete" title="Delete" onclick="return confirm(\'Are you sure you want to delete this personnel record?\');"><i class="fas fa-trash"></i></a>
                              </td>';
                        echo '</tr>';
                    }
                } catch (PDOException $e) {
                    echo '<tr><td colspan="6">No personnel records found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    // Simple search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('personnelTable');
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
    });
</script>

<?php include_once '../includes/footer.php'; ?>

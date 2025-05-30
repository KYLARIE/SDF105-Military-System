<?php
require '../config/auth.php';
require '../config/db.php';
require 'filter_functions.php';

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

// Handle export if requested
if (isset($_GET['export_format'])) {
    // Generate and output the export file
    switch ($_GET['export_format']) {
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export_'.date('Y-m-d').'.csv"');
            
            $output = fopen('php://output', 'w');
            // CSV headers
            fputcsv($output, ['ID', 'Name', 'Age', 'Rank', 'Unit', 'Status', 'Health Status', 'Superior']);
            
            // CSV data rows
            foreach ($people as $person) {
                fputcsv($output, [
                    $person['id'],
                    $person['name'],
                    $person['age'],
                    $person['rank_name'] ?? 'N/A',
                    $person['unit_name'] ?? 'N/A',
                    $person['military_status_name'] ?? 'N/A',
                    $person['health_status_name'] ?? 'N/A',
                    $person['superior_name'] ?? 'N/A'
                ]);
            }
            fclose($output);
            exit;
            
        case 'excel':
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="export_'.date('Y-m-d').'.xls"');
            
            echo '<table border="1">';
            echo '<tr><th>ID</th><th>Name</th><th>Age</th><th>Rank</th><th>Unit</th><th>Status</th><th>Health Status</th><th>Superior</th></tr>';
            
            foreach ($people as $person) {
                echo '<tr>';
                echo '<td>'.$person['id'].'</td>';
                echo '<td>'.$person['name'].'</td>';
                echo '<td>'.$person['age'].'</td>';
                echo '<td>'.($person['rank_name'] ?? 'N/A').'</td>';
                echo '<td>'.($person['unit_name'] ?? 'N/A').'</td>';
                echo '<td>'.($person['military_status_name'] ?? 'N/A').'</td>';
                echo '<td>'.($person['health_status_name'] ?? 'N/A').'</td>';
                echo '<td>'.($person['superior_name'] ?? 'N/A').'</td>';
                echo '</tr>';
            }
            echo '</table>';
            exit;
            
        case 'html':
            header('Content-Type: text/html');
            header('Content-Disposition: attachment; filename="export_'.date('Y-m-d').'.html"');
            
            echo '<!DOCTYPE html>
            <html>
            <head>
                <title>Data Export</title>
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h1>Data Export - '.date('Y-m-d H:i:s').'</h1>
                <table>
                    <tr>
                        <th>ID</th><th>Name</th><th>Age</th><th>Rank</th><th>Unit</th>
                        <th>Status</th><th>Health Status</th><th>Superior</th>
                    </tr>';
            
            foreach ($people as $person) {
                echo '<tr>';
                echo '<td>'.htmlspecialchars($person['id']).'</td>';
                echo '<td>'.htmlspecialchars($person['name']).'</td>';
                echo '<td>'.htmlspecialchars($person['age']).'</td>';
                echo '<td>'.htmlspecialchars($person['rank_name'] ?? 'N/A').'</td>';
                echo '<td>'.htmlspecialchars($person['unit_name'] ?? 'N/A').'</td>';
                echo '<td>'.htmlspecialchars($person['military_status_name'] ?? 'N/A').'</td>';
                echo '<td>'.htmlspecialchars($person['health_status_name'] ?? 'N/A').'</td>';
                echo '<td>'.htmlspecialchars($person['superior_name'] ?? 'N/A').'</td>';
                echo '</tr>';
            }
            
            echo '</table></body></html>';
            exit;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid export format']);
            exit;
    }
}

// Get dropdown options
$ranks = $pdo->query("SELECT id, rank_name FROM ranks ORDER BY rank_name")->fetchAll(PDO::FETCH_ASSOC);
$units = $pdo->query("SELECT id, unit_name FROM units ORDER BY unit_name")->fetchAll(PDO::FETCH_ASSOC);
$statuses = $pdo->query("SELECT id, status_name FROM statuses ORDER BY status_name")->fetchAll(PDO::FETCH_ASSOC);
$healthStatuses = $pdo->query("SELECT id, health_status_name FROM health ORDER BY health_status_name")->fetchAll(PDO::FETCH_ASSOC);
$superiors = $pdo->query("SELECT id, name FROM people ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Military Personnel</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="personnel.css">
  <style>
/* Base container styling with equal padding */
.center-container {
    padding: 20px 5%; /* Equal percentage-based padding */
    width: 100%;
    box-sizing: border-box;
}

/* Full width content container */
.center-container > div {
    width: 100%;
    max-width: none;
}

/* Button Group Container */
.btn-group-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
    width: 100%;
    padding: 0 5%; /* Match container padding */
    box-sizing: border-box;
}

.btn-group {
    display: flex;
    gap: 10px;
    background: white;
    padding: 10px;
    border-radius: 8px;
    width: 100%;
    max-width: 1200px;
    justify-content: center;
}
            
/* View Toggle Buttons */
.view-toggle-btn {
    padding: 10px 20px;
    background: #f8f8f8;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #444;
    transition: all 0.2s ease;
    min-width: 110px;
    text-align: center;
}

.view-toggle-btn:hover {
    background: #f0f0f0;
    border-color: #d0d0d0;
}

.view-toggle-btn.active {
    background: #4CAF50;
    color: white;
    border-color: #3d8b40;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

/* Export Button & Dropdown */
.export-dropdown {
    position: relative;
}

.export-btn {
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 110px;
}

.export-btn:hover {
    background: #45a049;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.export-btn::after {
    content: "▼";
    font-size: 10px;
    margin-left: 5px;
    transition: transform 0.2s;
}

.export-dropdown:hover .export-btn::after {
    transform: rotate(180deg);
}

.export-dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 140px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-radius: 6px;
    z-index: 10;
    right: 0;
    border: 1px solid #e0e0e0;
    margin-top: 5px;
    overflow: hidden;
}

.export-dropdown-content a {
    color: #555;
    padding: 10px 15px;
    text-decoration: none;
    display: block;
    font-size: 14px;
    transition: all 0.2s;
    border-bottom: 1px solid #f0f0f0;
}

.export-dropdown-content a:last-child {
    border-bottom: none;
}

.export-dropdown-content a:hover {
    background-color: #f8f8f8;
    color: #4CAF50;
    padding-left: 18px;
}

.export-dropdown:hover .export-dropdown-content {
    display: block;
    animation: fadeIn 0.2s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Table Styling */
table {
    margin: 20px 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

table th {
    background: #4CAF50;
    color: white;
    padding: 12px 15px;
    text-align: left;
    font-weight: 500;
}

table td {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

table tr:last-child td {
    border-bottom: none;
}

table tr:hover td {
    background-color: #f9f9f9;
}

/* Cards Container - Optimized 3-column layout with proper spacing */
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    row-gap: 60px;
    width: 100%;
    margin: 20px 0;
    padding: 0 5%;
    box-sizing: border-box;
}

.person-card {
    width: 90%;
    min-height: 350px;
    padding-top: 20px;
    padding-bottom: 20px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
}

.person-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.card-header {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f8f8;
    border-bottom: 1px solid #e0e0e0;
    flex-shrink: 0;
}

.profile-pic {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 15px;
    background: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #666;
    flex-shrink: 0;
}

.card-name {
    font-weight: 500;
    font-size: 16px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-id {
    font-size: 13px;
    color: #777;
}

.card-details {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.card-detail-row {
    display: flex;
    margin-bottom: 10px;
    min-height: 24px;
}

.card-detail-label {
    font-weight: 500;
    color: #555;
    min-width: 100px;
    flex-shrink: 0;
}

.card-detail-value {
    flex-grow: 1;
    word-break: break-word;
}

.card-actions {
    padding: 10px 15px;
    display: flex;
    gap: 10px;
    border-top: 1px solid #f0f0f0;
    background: #f8f8f8;
    flex-shrink: 0;
}

/* Filter form styling */
.filter-form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 100%;
    box-sizing: border-box;
}

.filter-form label {
    display: inline-block;
    margin: 8px 15px 8px 0;
    vertical-align: top;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

@media (max-width: 1024px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        padding: 0 15px;
        gap: 15px;
    }
    
    .center-container {
        padding: 20px 15px;
    }
    
    .btn-group-container {
        padding: 0 15px;
    }
    
    .btn-group {
        flex-wrap: wrap;
    }
    
    .view-toggle-btn, .export-btn {
        min-width: calc(50% - 15px);
    }
    
    .filter-form label {
        display: block;
        margin: 10px 0;
    }
}

@media (max-width: 480px) {
    .cards-container {
        grid-template-columns: 1fr;
        padding: 0 10px;
        gap: 15px;
    }
    
    .center-container {
        padding: 10px;
    }
    
    .person-card {
        min-height: auto;
    }
    
    .card-detail-row {
        flex-direction: column;
    }
    
    .card-detail-label {
        min-width: auto;
        margin-bottom: 3px;
    }
}
</style>
</head>
<body>
    <div class="center-container">
        <div style="width: 100%; max-width: 1200px;">
            <h1>Military Personnel</h1>
            <a href="../dashboard/index.php" class="button-link">&larr; Dashboard</a>

            <?php if (isset($_GET['deleted'])): ?>
                <p class="message">Record deleted successfully!</p>
            <?php endif; ?>

            <a href="add.php" class="button-link">+ Add Personnel</a>

            <form method="GET" class="filter-form">
                <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                <h3>Filter Results</h3>
                <label>Search by Name:
                    <input type="text" name="name" value="<?= htmlspecialchars($filters['name'] ?? '') ?>">
                </label>
                
                <label>Rank:
                    <select name="rank_id">
                        <option value="">-- All --</option>
                        <?php foreach ($ranks as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $filters['rank_id'] == $r['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['rank_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Unit:
                    <select name="unit_id">
                        <option value="">-- All --</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $filters['unit_id'] == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['unit_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Status:
                    <select name="military_status">
                        <option value="">-- All --</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $filters['military_status'] == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['status_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Medical Status:
                    <select name="health_id">
                        <option value="">-- All --</option>
                        <?php foreach ($healthStatuses as $h): ?>
                            <option value="<?= $h['id'] ?>" <?= $filters['health_id'] == $h['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($h['health_status_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Age Range:
                    Min <input type="number" name="min_age" value="<?= htmlspecialchars($filters['min_age'] ?? '') ?>" style="width: 50px">
                    Max <input type="number" name="max_age" value="<?= htmlspecialchars($filters['max_age'] ?? '') ?>" style="width: 50px">
                </label>

                <label>Superior:
                    <select name="superior_id">
                        <option value="">-- All --</option>
                        <?php foreach ($superiors as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $filters['superior_id'] == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label><input type="checkbox" name="has_email" value="1" <?= $filters['has_email'] ? 'checked' : '' ?>> Has Email</label>
                <label><input type="checkbox" name="has_file" value="1" <?= $filters['has_file'] ? 'checked' : '' ?>> Has Document</label>

                <button type="submit" class="button-link">Apply Filters</button>
                <a href="index.php" class="button-link">Reset</a>
            </form>

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

            <?php if ($view === 'table'): ?>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Rank</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Medical Status</th>
                        <th>Superior</th>
                        <th>Documents</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($people as $person): ?>
                        <tr>
                            <td><?= htmlspecialchars($person['id']) ?></td>
                            <td>
                                <?php 
                                $profileImagePath = '../uploads/profile_images/' . ($person['profile_image'] ?? '');
                                if (!empty($person['profile_image']) && file_exists($profileImagePath)): ?>
                                    <img src="<?= $profileImagePath ?>" width="50" height="50" style="border-radius:50%">
                                <?php else: ?>
                                    <div style="width:50px;height:50px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center">
                                        <?= !empty($person['name']) ? substr(trim($person['name']), 0, 1) : '?' ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($person['name']) ?></td>
                            <td><?= htmlspecialchars($person['age']) ?></td>
                            <td><?= htmlspecialchars($person['contact'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($person['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($person['unit_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($person['military_status_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($person['health_status_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($person['superior_name'] ?? 'None') ?></td>
                            <td>
                                <?php if (!empty($person['file_upload'])): ?>
                                    <a href="../uploads/<?= htmlspecialchars($person['file_upload']) ?>" target="_blank">View Document</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $person['id'] ?>" class="table-btn edit-btn">Edit</a>
                                <a href="index.php?delete=<?= $person['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
             <?php else: ?>
                <div class="cards-container">
                    <?php foreach ($people as $person): ?>
                        <div class="person-card">
                            <div class="card-header">
                                <div class="profile-pic">
                                    <?php 
                                    $profileImagePath = '../uploads/profile_images/' . ($person['profile_image'] ?? '');
                                    if (!empty($person['profile_image']) && file_exists($profileImagePath)): ?>
                                        <img src="<?= $profileImagePath ?>" alt="Profile Image">
                                    <?php else: ?>
                                        <div class="initials">
                                            <?= !empty($person['name']) ? substr(trim($person['name']), 0, 1) : '?' ?>
                                        </div>
                                    <?php endif; ?>
                                </div>  
                                <div>
                                    <div class="card-name"><?= htmlspecialchars($person['name']) ?></div>
                                    <div class="card-id">ID: <?= htmlspecialchars($person['id']) ?></div>
                                </div>
                            </div>
                            
                            <div class="card-details">
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Age:</div>
                                    <div><?= htmlspecialchars($person['age']) ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Rank:</div>
                                    <div><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Unit:</div>
                                    <div><?= htmlspecialchars($person['unit_name'] ?? 'N/A') ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Status:</div>
                                    <div><?= htmlspecialchars($person['military_status_name'] ?? 'N/A') ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Medical:</div>
                                    <div><?= htmlspecialchars($person['health_status_name'] ?? 'N/A') ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Contact:</div>
                                    <div><?= htmlspecialchars($person['contact'] ?? 'N/A') ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Email:</div>
                                    <div><?= htmlspecialchars($person['email'] ?? 'N/A') ?></div>
                                </div>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Superior:</div>
                                    <div><?= htmlspecialchars($person['superior_name'] ?? 'None') ?></div>
                                </div>
                                <?php if (!empty($person['file_upload'])): ?>
                                <div class="card-detail-row">
                                    <div class="card-detail-label">Document:</div>
                                    <div><a href="../uploads/<?= htmlspecialchars($person['file_upload']) ?>" target="_blank">View</a></div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-actions">
                                <a href="edit.php?id=<?= $person['id'] ?>" class="table-btn edit-btn">Edit</a>
                                <a href="index.php?delete=<?= $person['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="personnel.js"></script>
</body>
</html>
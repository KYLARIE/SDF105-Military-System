<?php
// Handle export if requested
if (isset($_GET['export_format'])) {
    require_once '../config/db.php';
    require_once '../config/auth.php';
    require_once 'filter_functions.php';

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

    // Generate and output the export file
    switch ($_GET['export_format']) {
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.csv"');

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
            header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.xls"');

            echo '<table border="1">';
            echo '<tr><th>ID</th><th>Name</th><th>Age</th><th>Rank</th><th>Unit</th><th>Status</th><th>Health Status</th><th>Superior</th></tr>';

            foreach ($people as $person) {
                echo '<tr>';
                echo '<td>' . $person['id'] . '</td>';
                echo '<td>' . $person['name'] . '</td>';
                echo '<td>' . $person['age'] . '</td>';
                echo '<td>' . ($person['rank_name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($person['unit_name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($person['military_status_name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($person['health_status_name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($person['superior_name'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            exit;

        case 'html':
            header('Content-Type: text/html');
            header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.html"');

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
                <h1>Data Export - ' . date('Y-m-d H:i:s') . '</h1>
                <table>
                    <tr>
                        <th>ID</th><th>Name</th><th>Age</th><th>Rank</th><th>Unit</th>
                        <th>Status</th><th>Health Status</th><th>Superior</th>
                    </tr>';

            foreach ($people as $person) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($person['id']) . '</td>';
                echo '<td>' . htmlspecialchars($person['name']) . '</td>';
                echo '<td>' . htmlspecialchars($person['age']) . '</td>';
                echo '<td>' . htmlspecialchars($person['rank_name'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($person['unit_name'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($person['military_status_name'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($person['health_status_name'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($person['superior_name'] ?? 'N/A') . '</td>';
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

// Handle add personnel form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_personnel'])) {
    require_once '../config/db.php';
    require_once '../config/auth.php';
    
    $profileImageName = null;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profile_pics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $profileImageName = time() . '_' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $profileImageName);
    }

    $data = [
        'name' => trim($_POST['name']),
        'age' => (int)$_POST['age'],
        'contact' => trim($_POST['contact'] ?? null),
        'email' => trim($_POST['email'] ?? null),
        'rank_id' => !empty($_POST['rank_id']) ? (int)$_POST['rank_id'] : null,
        'unit_id' => !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : null,
        'military_status' => $_POST['military_status'],
        'health_id' => !empty($_POST['health_id']) ? (int)$_POST['health_id'] : null,
        'superior_id' => !empty($_POST['superior_id']) ? (int)$_POST['superior_id'] : null,
        'profile_image' => $profileImageName
    ];

    $stmt = $pdo->prepare("
        INSERT INTO people 
        (name, age, contact, email, rank_id, unit_id, military_status, health_id, superior_id, profile_image) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute(array_values($data));
    header("Location: index.php?added=1");
    exit();
}

// Include the filter and display sections
require_once 'filter.php';
require_once 'people_table.php';

// Get data for dropdowns in the modal
require_once '../config/db.php';
$ranks = $pdo->query("SELECT * FROM ranks ORDER BY rank_name")->fetchAll();
$units = $pdo->query("SELECT * FROM units ORDER BY unit_name")->fetchAll();
$personnel = $pdo->query("SELECT * FROM people ORDER BY name")->fetchAll();
$health_statuses = $pdo->query("SELECT * FROM health ORDER BY health_status_name")->fetchAll();
$statuses = ['Active Duty', 'Reserve', 'National Guard', 'Veteran', 'Retired', 'Dishonorably Discharged', 'AWOL'];
?>

<!-- Add Personnel Modal -->
<div id="addPersonnelModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Personnel</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addPersonnelForm" method="post" action="create.php" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required>
                
                <label for="contact">Contact</label>
                <input type="text" id="contact" name="contact">
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                
                <label for="rank_id">Rank</label>
                <select id="rank_id" name="rank_id">
                    <option value="">-- Select Rank --</option>
                    <?php foreach ($ranks as $rank): ?>
                        <option value="<?= $rank['id'] ?>"><?= htmlspecialchars($rank['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="unit_id">Unit</label>
                <select id="unit_id" name="unit_id">
                    <option value="">-- Select Unit --</option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="military_status">Military Status</label>
                <select id="military_status" name="military_status" required>
                    <option value="Active Duty">Active Duty</option>
                    <option value="Reserve">Reserve</option>
                    <option value="Retired">Retired</option>
                    <option value="Discharged">Discharged</option>
                </select>
                
                <label for="health_id">Health Status</label>
                <select id="health_id" name="health_id">
                    <option value="">-- Select Health Status --</option>
                    <?php foreach ($health_statuses as $health): ?>
                        <option value="<?= $health['id'] ?>"><?= htmlspecialchars($health['health_status_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="superior_id">Superior Officer</label>
                <select id="superior_id" name="superior_id">
                    <option value="">-- Select Superior --</option>
                    <?php foreach ($personnel as $person): ?>
                        <option value="<?= $person['id'] ?>"><?= htmlspecialchars($person['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="profileImageInput">Profile Picture (Click the square to upload)</label>
                <div id="profileImageBox">
                    <div class="initials-preview">?</div>
                </div>
                <input type="file" name="profile_image" id="profileImageInput" accept="image/png, image/jpeg" style="display:none;">
                
                <div class="modal-footer">
                    <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_personnel" class="add">Add Personnel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 80%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e0e0e0;
    background-color: #1e5631;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    position: relative;
}

.modal-header h3 {
    margin: 0;
    color: white;
    font-weight: 500;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: white;
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

.modal-body {
    padding: 20px;
}

.modal-body form {
    width: 100%;
}

.modal-body label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.modal-body input,
.modal-body select {
    width: 100%;
    padding: 8px 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.modal-footer button {
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    border: none;
}

.modal-footer button.cancel {
    background-color: #6c757d;
    color: white;
}

.modal-footer button.add {
    background-color: #1e5631;
    color: white;
}

/* Profile image upload box */
#profileImageBox {
    width: 150px;
    height: 150px;
    margin: 10px auto;
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

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}
</style>

<script>
// JavaScript for modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get the modal
    const modal = document.getElementById('addPersonnelModal');
    
    // Get the button that opens the modal
    const btn = document.getElementById('openAddPersonnelModal');
    
    // Get the close button
    const closeBtn = document.querySelector('.modal-close');
    
    // Function to open the modal
    function openModal() {
        modal.style.display = 'block';
    }
    
    // Function to close the modal
    function closeModal() {
        modal.style.display = 'none';
    }
    
    // When the user clicks the button, open the modal
    if (btn) {
        btn.addEventListener('click', openModal);
    }
    
    // When the user clicks on the close button, close the modal
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Make closeModal function available globally
    window.closeModal = closeModal;
    
    // Handle profile image upload
    const profileImageBox = document.getElementById('profileImageBox');
    const profileImageInput = document.getElementById('profileImageInput');
    
    if (profileImageBox && profileImageInput) {
        profileImageBox.addEventListener('click', function() {
            profileImageInput.click();
        });
        
        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImageBox.innerHTML = '';
                    profileImageBox.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>

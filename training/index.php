    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="training.css">

    <?php
    require '../config/auth.php';
    require '../config/db.php';

    // Handle delete
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM training WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?deleted=1");
        exit();
    }

    // Handle enlist action (new → under_training)
    if (isset($_GET['enlist'])) {
        $id = $_GET['enlist'];
        $stmt = $pdo->prepare("UPDATE training SET status = 'under_training' WHERE id = ? AND status = 'new'");
        $stmt->execute([$id]);
        header("Location: index.php?enlisted=1");
        exit();
    }

    // Handle deploy action (under_training → deployed and move to people table)
    if (isset($_GET['deploy'])) {
        $id = $_GET['deploy'];

        // Get the training record
        $stmt = $pdo->prepare("SELECT * FROM training WHERE id = ? AND status = 'under_training'");
        $stmt->execute([$id]);
        $training = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($training) {
            try {
                // Begin transaction
                $pdo->beginTransaction();

                // Insert into people table with all necessary fields
                $stmt = $pdo->prepare("INSERT INTO people 
                    (profile_image, name, age, military_status, health_id, unit_id, rank_id, document) 
                    VALUES (?, ?, ?, ?,?,?,?, ?)");
                $stmt->execute([
                    $training['profile_image'] ?? null,
                    $training['name'],
                    $training['age'],
                    'Active',
                    '2',
                    '5',
                    '1',
                    $training['document'],

                ]);


                // Update training status to deployed
                $stmt = $pdo->prepare("UPDATE training SET status = 'deployed' WHERE id = ?");
                $stmt->execute([$id]);

                // Commit transaction
                $pdo->commit();

                header("Location: index.php?deployed=1");
                exit();
            } catch (PDOException $e) {
                // Rollback transaction on error
                $pdo->rollBack();
                die("Error moving record to people table: " . $e->getMessage());
            }
        }
    }

    // Check view preference
    $view = $_GET['view'] ?? 'table';

    // Get new enlistments (status = 'new')
    $stmt = $pdo->prepare("SELECT * FROM training WHERE status = 'new' ORDER BY id DESC");
    $stmt->execute();
    $newEnlistments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get under training records (status = 'under_training')
    $stmt = $pdo->prepare("SELECT * FROM training WHERE status = 'under_training' ORDER BY id DESC");
    $stmt->execute();
    $underTraining = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine all records for export functionality
    $allRecords = array_merge($newEnlistments, $underTraining);

    // Handle export if requested
    if (isset($_GET['export_format'])) {
        switch ($_GET['export_format']) {
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="training_export_' . date('Y-m-d') . '.csv"');

                $output = fopen('php://output', 'w');
                fputcsv($output, ['ID', 'Name', 'Age', 'Status', 'Profile Image', 'Document']);
                foreach ($allRecords as $record) {
                    fputcsv($output, [
                        $record['id'],
                        $record['profile_image'] ?? 'N/A',
                        $record['name'],
                        $record['age'],
                        $record['status'],
                        $record['document'] ?? 'N/A'
                    ]);
                }

                fclose($output);
                exit;

            case 'excel':
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="training_export_' . date('Y-m-d') . '.xls"');

                echo '<table border="1">';
                echo '<tr><th>ID</th><th>Name</th><th>Age</th><th>Status</th><th>Profile Image</th><th>Document</th></tr>';

                foreach ($allRecords as $record) {
                    echo '<tr>';
                    echo '<td>' . $record['id'] . '</td>';
                    echo '<td>' . ($record['profile_image'] ?? 'N/A') . '</td>';
                    echo '<td>' . $record['name'] . '</td>';
                    echo '<td>' . $record['age'] . '</td>';
                    echo '<td>' . $record['status'] . '</td>';
                    echo '<td>' . ($record['document'] ?? 'N/A') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                exit;

            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid export format']);
                exit;
        }
    }
    ?>




    <?php include_once '../includes/header.php'; ?>
    <div class="center-container">
        <div style="width: 100%; max-width: 1200px;">
            <h1>Training Records</h1>

            <?php if (isset($_GET['deleted'])): ?>
                <p class="message">Record deleted successfully!</p>
            <?php endif; ?>

            <?php if (isset($_GET['enlisted'])): ?>
                <p class="message">Record moved to Under Training successfully!</p>
            <?php endif; ?>

            <?php if (isset($_GET['deployed'])): ?>
                <p class="message">Record deployed successfully!</p>
            <?php endif; ?>

            <div style="display: flex; gap: 10px; margin: 15px 0;">
                <a href="add.php" class="button-link">↧ Import from CSV</a>
            </div>

            <div class="btn-group-container">
                <div class="btn-group">
                    <a href="?view=table" class="view-toggle-btn <?= $view === 'table' ? 'active' : '' ?>">Table View</a>
                    <a href="?view=cards" class="view-toggle-btn <?= $view === 'cards' ? 'active' : '' ?>">Card View</a>

                    <div class="export-dropdown">
                        <button class="export-btn">Export ▼</button>
                        <div class="export-dropdown-content">
                            <a href="?export_format=csv">CSV</a>
                            <a href="?export_format=excel">Excel</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($view === 'table'): ?>
                <!-- New Enlistments Table -->
                <div class="status-section">
                    <h2 class="status-title">New Enlistments</h2>
                    <table class>
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach ($newEnlistments as $training): ?>
                            <tr>
                                <td><?= htmlspecialchars($training['id']) ?></td>
                                <td>
                                    <?php if (!empty($training['profile_image'])): ?>
                                        <img src="../uploads/profile_images/<?= htmlspecialchars($training['profile_image']) ?>" width="50" height="50" style="border-radius:50%" alt="Profile">
                                    <?php else: ?>
                                        <div style="width:50px;height:50px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center">
                                            <?= !empty($training['name']) ? substr(trim($training['name']), 0, 1) : '?' ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($training['name']) ?></td>
                                <td><?= htmlspecialchars($training['age']) ?></td>
                                <td><span class="status-badge badge-new">New</span></td>
                                <td>
                                    <?php if (!empty($training['document'])): ?>
                                        <?php
                                        $docLink = htmlspecialchars($training['document']);
                                        $isExternal = str_starts_with($training['document'], 'http');
                                        $fullPath = $isExternal ? $docLink : "../uploads/documents/$docLink";
                                        ?>
                                        <a href="<?= $fullPath ?>" target="_blank">View Document</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $training['id'] ?>" class="table-btn edit-btn">Edit</a>
                                    <a href="index.php?enlist=<?= $training['id'] ?>" class="table-btn enlist-btn">Enlist</a>
                                    <a href="index.php?delete=<?= $training['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <!-- Under Training Table -->
                <div class="status-section">
                    <h2 class="status-title">Under Training</h2>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach ($underTraining as $training): ?>
                            <tr>
                                <td><?= htmlspecialchars($training['id']) ?></td>
                                <td>
                                    <?php if (!empty($training['profile_image'])): ?>
                                        <img src="../uploads/profile_images/<?= htmlspecialchars($training['profile_image']) ?>" width="50" height="50" style="border-radius:50%" alt="Profile">
                                    <?php else: ?>
                                        <div style="width:50px;height:50px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center">
                                            <?= !empty($training['name']) ? substr(trim($training['name']), 0, 1) : '?' ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($training['name']) ?></td>
                                <td><?= htmlspecialchars($training['age']) ?></td>
                                <td><span class="status-badge badge-under-training">Under Training</span></td>
                                <td>
                                    <?php if (!empty($training['document'])): ?>
                                        <a href="../uploads/documents/<?= htmlspecialchars($training['document']) ?>" target="_blank">View Document</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $training['id'] ?>" class="table-btn edit-btn">Edit</a>
                                    <a href="index.php?deploy=<?= $training['id'] ?>" class="table-btn deploy-btn">Deploy</a>
                                    <a href="index.php?delete=<?= $training['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($underTraining)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No records under training</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php else: ?>
                <!-- Card View -->
                <div class="status-section">
                    <h2 class="status-title">New Enlistments</h2>
                    <div class="cards-container">
                        <?php foreach ($newEnlistments as $training): ?>
                            <div class="training-card">
                                <div class="card-header">
                                    <div class="profile-pic">
                                        <?php if (!empty($training['profile_image'])): ?>
                                            <img src="../uploads/profile_images/<?= htmlspecialchars($training['profile_image']) ?>" width="50" height="50" alt="Profile Image">
                                        <?php else: ?>
                                            <?= !empty($training['name']) ? substr(trim($training['name']), 0, 1) : '?' ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:500"><?= htmlspecialchars($training['name']) ?></div>
                                        <div style="font-size:13px;color:#777">ID: <?= htmlspecialchars($training['id']) ?></div>
                                        <div style="font-size:13px;color:#777">Age: <?= htmlspecialchars($training['age']) ?></div>
                                        <div style="font-size:13px;color:#777">
                                            Status: <span class="status-badge badge-new">New</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-details">
                                    <div class="card-detail-row">
                                        <strong>Profile Image:</strong>
                                        <?php if (!empty($training['profile_image'])): ?>
                                            <span>Uploaded</span>
                                        <?php else: ?>
                                            <span>Not provided</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-detail-row">
                                        <strong>Document:</strong>
                                        <?php if (!empty($training['document'])): ?>
                                            <a href="../uploads/documents/<?= htmlspecialchars($training['document']) ?>" target="_blank">View Document</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-actions">
                                    <a href="edit.php?id=<?= $training['id'] ?>" class="table-btn edit-btn">Edit</a>
                                    <a href="index.php?enlist=<?= $training['id'] ?>" class="table-btn enlist-btn">Enlist</a>
                                    <a href="index.php?delete=<?= $training['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($newEnlistments)): ?>
                            <p style="grid-column:1/-1;text-align:center;">No new enlistments found</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="status-section">
                    <h2 class="status-title">Under Training</h2>
                    <div class="cards-container">
                        <?php foreach ($underTraining as $training): ?>
                            <div class="training-card">
                                <div class="card-header">
                                    <div class="profile-pic">
                                        <?php if (!empty($training['profile_image'])): ?>
                                            <img src="../uploads/profile_images/<?= htmlspecialchars($training['profile_image']) ?>" width="50" height="50" alt="Profile Image">
                                        <?php else: ?>
                                            <?= !empty($training['name']) ? substr(trim($training['name']), 0, 1) : '?' ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:500"><?= htmlspecialchars($training['name']) ?></div>
                                        <div style="font-size:13px;color:#777">ID: <?= htmlspecialchars($training['id']) ?></div>
                                        <div style="font-size:13px;color:#777">Age: <?= htmlspecialchars($training['age']) ?></div>
                                        <div style="font-size:13px;color:#777">
                                            Status: <span class="status-badge badge-under-training">Under Training</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-details">
                                    <div class="card-detail-row">
                                        <strong>Profile Image:</strong>
                                        <?php if (!empty($training['profile_image'])): ?>
                                            <span>Uploaded</span>
                                        <?php else: ?>
                                            <span>Not provided</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-detail-row">
                                        <strong>Document:</strong>
                                        <?php if (!empty($training['document'])): ?>
                                            <a href="../uploads/documents/<?= htmlspecialchars($training['document']) ?>" target="_blank">View Document</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-actions">
                                    <a href="edit.php?id=<?= $training['id'] ?>" class="table-btn edit-btn">Edit</a>
                                    <a href="index.php?deploy=<?= $training['id'] ?>" class="table-btn deploy-btn">Deploy</a>
                                    <a href="index.php?delete=<?= $training['id'] ?>" onclick="return confirm('Delete this record?')" class="table-btn delete-btn">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($underTraining)): ?>
                            <p style="grid-column:1/-1;text-align:center;">No records under training</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="training.js"></script>
    <?php include_once '../includes/footer.php'; ?>
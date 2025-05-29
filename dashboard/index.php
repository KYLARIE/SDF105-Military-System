<?php
require '../config/db.php';
require '../config/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <?php include 'layout.php'; ?>
    <main class="dashboard-content">
        <section class="quick-stats">
            <div class="stats-grid">
                <?php
                $people_count = $pdo->query("SELECT COUNT(*) FROM people")->fetchColumn();
                $ranks_count = $pdo->query("SELECT COUNT(*) FROM ranks")->fetchColumn();
                $units_count = $pdo->query("SELECT COUNT(*) FROM units")->fetchColumn();
                ?>
                <div class="stat-card">
                    <h3>People</h3>
                    <p><?php echo $people_count; ?></p>
                    <a href="../people/index.php">View All</a>
                </div>
                <div class="stat-card">
                    <h3>Ranks</h3>
                    <p><?php echo $ranks_count; ?></p>
                    <a href="../ranks/index.php">View All</a>
                </div>
                <div class="stat-card">
                    <h3>Units</h3>
                    <p><?php echo $units_count; ?></p>
                    <a href="../units/index.php">View All</a>
                </div>
            </div>
        </section>
    </main>

</body>

</html>
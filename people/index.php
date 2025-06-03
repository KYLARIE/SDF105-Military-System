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

// Include the filter and display sections
require_once 'filter.php';
require_once 'people_table.php';

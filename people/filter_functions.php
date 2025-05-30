<?php
require '../config/db.php';

// Fetch dropdown/filter options
function getFilterOptions($pdo) {
    return [
        'ranks' => $pdo->query("SELECT id, rank_name FROM ranks ORDER BY rank_name")->fetchAll(),
        'units' => $pdo->query("SELECT id, unit_name FROM units ORDER BY unit_name")->fetchAll(),
        'statuses' => $pdo->query("SELECT DISTINCT military_status FROM people ORDER BY military_status")->fetchAll(),
        'health_statuses' => $pdo->query("SELECT id, health_status_name FROM health ORDER BY health_status_name")->fetchAll(),
        'superiors' => $pdo->query("SELECT id, name FROM people ORDER BY name")->fetchAll(),
    ];
}

// Fetch filtered people
function getFilteredPeople($pdo, $filters) {
    $sql = "
        SELECT p.*, 
               r.rank_name,
               u.unit_name,
               s.name as superior_name,
               h.health_status_name
        FROM people p
        LEFT JOIN ranks r ON p.rank_id = r.id
        LEFT JOIN units u ON p.unit_id = u.id
        LEFT JOIN people s ON p.superior_id = s.id
        LEFT JOIN health h ON p.health_id = h.id
        WHERE 1=1
    ";

    $params = [];

    // Apply filters
    if (!empty($filters['rank_id'])) {
        $sql .= " AND p.rank_id = ?";
        $params[] = $filters['rank_id'];
    }

    if (!empty($filters['unit_id'])) {
        $sql .= " AND p.unit_id = ?";
        $params[] = $filters['unit_id'];
    }

    if (!empty($filters['military_status'])) {
        $sql .= " AND p.military_status = ?";
        $params[] = $filters['military_status'];
    }

    if (!empty($filters['health_id'])) {
        $sql .= " AND p.health_id = ?";
        $params[] = $filters['health_id'];
    }

    if (!empty($filters['superior_id'])) {
        $sql .= " AND p.superior_id = ?";
        $params[] = $filters['superior_id'];
    }

    if (!empty($filters['has_email'])) {
        $sql .= " AND p.email IS NOT NULL AND p.email != ''";
    }

    if (!empty($filters['has_file'])) {
        $sql .= " AND p.file_upload IS NOT NULL AND p.file_upload != ''";
    }

    // 🔍 Search by name
    if (!empty($filters['name'])) {
        $sql .= " AND p.name LIKE ?";
        $params[] = '%' . $filters['name'] . '%';
    }

    // 👤 Filter by age range
    if (!empty($filters['min_age'])) {
        $sql .= " AND p.age >= ?";
        $params[] = $filters['min_age'];
    }

    if (!empty($filters['max_age'])) {
        $sql .= " AND p.age <= ?";
        $params[] = $filters['max_age'];
    }

    $sql .= " ORDER BY p.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function generateExport($pdo, $requestId) {
    // Verify request is approved
    $stmt = $pdo->prepare("SELECT filters FROM export_requests WHERE id = ? AND status = 'approved'");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();
    
    if (!$request) return false;
    
    $filters = json_decode($request['filters'], true);
    $people = getFilteredPeople($pdo, $filters);
    
    // Generate HTML for export
    $html = '<table border="1" style="width:100%;border-collapse:collapse;">';
    $html .= '<tr><th>ID</th><th>Name</th><th>Age</th><th>Rank</th><th>Unit</th><th>Status</th></tr>';
    
    foreach ($people as $person) {
        $html .= '<tr>';
        $html .= '<td>'.htmlspecialchars($person['id']).'</td>';
        $html .= '<td>'.htmlspecialchars($person['name']).'</td>';
        $html .= '<td>'.htmlspecialchars($person['age']).'</td>';
        $html .= '<td>'.htmlspecialchars($person['rank_name'] ?? 'N/A').'</td>';
        $html .= '<td>'.htmlspecialchars($person['unit_name'] ?? 'N/A').'</td>';
        $html .= '<td>'.htmlspecialchars($person['military_status_name'] ?? 'N/A').'</td>';
        $html .= '</tr>';
    }
    
    $html .= '</table>';
    
    return $html;
}
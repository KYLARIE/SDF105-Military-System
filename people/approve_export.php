<?php
require '../config/db.php';

$requestId = $_GET['id'];

// 1. Approve the request
$stmt = $pdo->prepare("
    UPDATE export_requests 
    SET status = 'approved' 
    WHERE id = ?
");
$stmt->execute([$requestId]);

// 2. Email download link to user
$stmt = $pdo->prepare("
    SELECT p.email 
    FROM export_requests er 
    JOIN people p ON er.requester_id = p.id 
    WHERE er.id = ?
");
$stmt->execute([$requestId]);
$userEmail = $stmt->fetchColumn();

$downloadLink = "https://yoursite.com/download_export.php?id=$requestId";

mail($userEmail, 
    "Your Export is Ready", 
    "Download your data: $downloadLink"
);

// 3. Optional: Notify admin
mail("earlrecometa@gmail.com", 
    "Export Approved (ID: $requestId)", 
    "You approved the request. User has been notified."
);

echo "Request approved. User notified.";
?>
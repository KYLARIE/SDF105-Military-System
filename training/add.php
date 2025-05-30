<?php
require '../config/auth.php';
require '../config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['google_sheet']) && $_FILES['google_sheet']['error'] === UPLOAD_ERR_OK) {
        // Check if the file is a CSV
        $fileInfo = pathinfo($_FILES['google_sheet']['name']);
        if (strtolower($fileInfo['extension']) === 'csv') {
            // Process the CSV file
            $filePath = $_FILES['google_sheet']['tmp_name'];
            $handle = fopen($filePath, 'r');
            
            // Skip header row if exists
            fgetcsv($handle);
            
            $importedCount = 0;
            $pdo->beginTransaction();
            
            try {
                while (($data = fgetcsv($handle)) !== false) {
                    // Expected format: Name, Age, Profile Image URL, Document URL
                    if (count($data) >= 2) { // At least Name and Age
                        $name = trim($data[0]);
                        $age = (int)trim($data[1]);
                        $profileImageUrl = isset($data[2]) ? trim($data[2]) : '';
                        $documentUrl = isset($data[3]) ? trim($data[3]) : '';
                        
                        // Download and save profile image if URL provided
                        $profileImage = '';
                        if (!empty($profileImageUrl)) {
                            $profileImage = downloadAndSaveFile($profileImageUrl, '../uploads/profile_images/');
                        }
                        
                        // Download and save document if URL provided
                        $document = '';
                        if (!empty($documentUrl)) {
                            $document = downloadAndSaveFile($documentUrl, '../uploads/documents/');
                        }
                        
                        // Insert into database
                        $stmt = $pdo->prepare("INSERT INTO training (name, age, profile_image, document) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $age, $profileImage, $document]);
                        $importedCount++;
                    }
                }
                
                $pdo->commit();
                $message = "Successfully imported $importedCount records.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error importing records: " . $e->getMessage();
            }
            
            fclose($handle);
        } else {
            $error = "Please upload a valid CSV file downloaded from Google Sheets.";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}

/**
 * Downloads a file from URL and saves it to the specified directory
 */
function downloadAndSaveFile($url, $targetDir) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }
    
    // Create target directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Get file info
    $pathInfo = pathinfo($url);
    $extension = isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
    $filename = uniqid() . (!empty($extension) ? '.' . $extension : '');
    $targetPath = $targetDir . $filename;
    
    // Download the file
    $fileContent = file_get_contents($url);
    if ($fileContent !== false) {
        file_put_contents($targetPath, $fileContent);
        return $filename;
    }
    
    return '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import Training Records</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .import-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .instructions {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .file-upload {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-upload input[type="file"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .success {
            background: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .error {
            background: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
    </style>
</head>
<body>
    <div class="center-container">
        <div class="import-container">
            <h1>Import Training Records</h1>
            <a href="index.php" class="button-link">&larr; Back to Training Records</a>
            
            <?php if (!empty($message)): ?>
                <div class="message success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="instructions">
                <h3>How to import records:</h3>
                <ol>
                    <li>Create a Google Sheets document with these columns (in order):</li>
                    <ul>
                        <li>Column 1: Name (required)</li>
                        <li>Column 2: Age (required)</li>
                        <li>Column 3: Profile Image URL (optional)</li>
                        <li>Column 4: Document URL (optional)</li>
                    </ul>
                    <li>In Google Sheets, go to File → Download → Comma-separated values (.csv)</li>
                    <li>Upload the downloaded CSV file below</li>
                </ol>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="google_sheet">CSV File (from Google Sheets):</label>
                    <div class="file-upload">
                        <input type="file" name="google_sheet" id="google_sheet" accept=".csv" required>
                    </div>
                </div>
                
                <button type="submit" class="button-link">Import Records</button>
            </form>
        </div>
    </div>
</body>
</html>
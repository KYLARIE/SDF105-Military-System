<?php
// Folder and file structure
$structure = [
    'config' => ['db.php', 'auth.php'],
    'admin' => ['login.php', 'logout.php'],
    'dashboard' => ['index.php'],
    'people' => ['index.php', 'add.php', 'edit.php', 'delete.php'],
    'ranks' => ['index.php', 'add.php', 'edit.php', 'delete.php'],
    'units' => ['index.php', 'add.php', 'edit.php', 'delete.php'],
    'statuses' => ['index.php', 'add.php', 'edit.php', 'delete.php'],
    'assets/css' => ['style.css'],
    'assets/js' => ['script.js'],
];

// Root directory (current directory)
$root = __DIR__;

// Function to create folders and files
foreach ($structure as $folder => $files) {
    $folderPath = $root . DIRECTORY_SEPARATOR . $folder;
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // create folders recursively
        echo "Created folder: $folderPath<br>";
    }
    foreach ($files as $file) {
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, ""); // create empty file
            echo "Created file: $filePath<br>";
        }
    }
}

// Create index.php in root if not exist
$indexPath = $root . DIRECTORY_SEPARATOR . 'index.php';
if (!file_exists($indexPath)) {
    file_put_contents($indexPath, "<?php\n// Entry point\n");
    echo "Created file: $indexPath<br>";
}

echo "<br>✅ All folders and files are ready!";

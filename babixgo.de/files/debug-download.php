<?php
/**
 * Debug Download - Diagnose download issues
 * Usage: /files/debug-download.php?id=5&type=apk
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('DOWNLOADS_PATH', BASE_PATH . 'file-storage/');

echo "<h1>Download Debug</h1>";
echo "<pre>";

// Step 1: Check parameters
echo "=== STEP 1: Parameters ===\n";
$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fileType = $_GET['type'] ?? null;
echo "ID: " . ($fileId ?: "MISSING") . "\n";
echo "Type: " . ($fileType ?: "MISSING") . "\n\n";

if (!$fileId || !$fileType) {
    die("ERROR: Missing parameters\n");
}

// Step 2: Load configuration
echo "=== STEP 2: Load Configuration ===\n";
try {
    require_once SHARED_PATH . 'config/database.php';
    echo "✓ Database config loaded\n";
    require_once SHARED_PATH . 'config/session.php';
    echo "✓ Session config loaded\n";
    require_once SHARED_PATH . 'config/autoload.php';
    echo "✓ Autoload config loaded\n\n";
} catch (Exception $e) {
    die("ERROR loading config: " . $e->getMessage() . "\n");
}

// Step 3: Database connection
echo "=== STEP 3: Database Connection ===\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connection successful\n";
    echo "Connection type: " . get_class($db) . "\n\n";
} catch (Exception $e) {
    die("ERROR: Database connection failed: " . $e->getMessage() . "\n");
}

// Step 4: Query database
echo "=== STEP 4: Query Database ===\n";
try {
    $stmt = $db->prepare("SELECT * FROM downloads WHERE id = ? AND filetype = ?");
    echo "✓ Statement prepared\n";

    $stmt->execute([$fileId, $fileType]);
    echo "✓ Query executed\n";

    $fileInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fileInfo) {
        echo "✓ Download found in database\n";
        echo "Data:\n";
        print_r($fileInfo);
    } else {
        echo "✗ Download NOT found in database\n";
        echo "Query: SELECT * FROM downloads WHERE id = $fileId AND filetype = '$fileType'\n";
    }
    echo "\n";
} catch (PDOException $e) {
    die("ERROR: Query failed: " . $e->getMessage() . "\n");
}

// Step 5: Check file path
if ($fileInfo) {
    echo "=== STEP 5: Check File Path ===\n";
    $filePath = DOWNLOADS_PATH . $fileInfo['filepath'];
    echo "Expected path: $filePath\n";
    echo "File exists: " . (file_exists($filePath) ? "YES ✓" : "NO ✗") . "\n";

    if (file_exists($filePath)) {
        echo "File size: " . filesize($filePath) . " bytes\n";
        echo "Readable: " . (is_readable($filePath) ? "YES ✓" : "NO ✗") . "\n";
    } else {
        echo "\nChecking parent directory:\n";
        $parentDir = dirname($filePath);
        echo "Parent dir: $parentDir\n";
        echo "Parent exists: " . (file_exists($parentDir) ? "YES" : "NO") . "\n";
        echo "Parent readable: " . (is_readable($parentDir) ? "YES" : "NO") . "\n";

        echo "\nListing files in parent directory:\n";
        if (file_exists($parentDir)) {
            $files = scandir($parentDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "  - $file\n";
                }
            }
        }
    }
    echo "\n";
}

echo "=== DEBUG COMPLETE ===\n";
echo "</pre>";

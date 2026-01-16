<?php
/**
 * Debug-Script für /files/ - Fehleranalyse
 * Aufruf: auth.babixgo.de/files/debug-test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug-Test für babixgo.de/files/</h1>\n";
echo "<pre>\n";

echo "=== SERVER INFORMATIONEN ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'nicht gesetzt') . "\n";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'nicht gesetzt') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'nicht gesetzt') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'nicht gesetzt') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'nicht gesetzt') . "\n";
echo "__FILE__: " . __FILE__ . "\n";
echo "__DIR__: " . __DIR__ . "\n\n";

echo "=== PFAD-DEFINITIONEN (aus init.php) ===\n";
$basePathCalc = dirname(__DIR__) . '/';
$sharedPathCalc = $basePathCalc . 'shared/';
$filesPathCalc = __DIR__ . '/';

echo "BASE_PATH (berechnet): " . $basePathCalc . "\n";
echo "SHARED_PATH (berechnet): " . $sharedPathCalc . "\n";
echo "FILES_PATH (berechnet): " . $filesPathCalc . "\n\n";

echo "=== DATEISYSTEM-PRÜFUNGEN ===\n";

// Check shared directory
if (is_dir($sharedPathCalc)) {
    echo "✓ SHARED_PATH existiert: " . $sharedPathCalc . "\n";
} else {
    echo "✗ FEHLER: SHARED_PATH existiert NICHT: " . $sharedPathCalc . "\n";
}

// Check shared/config/database.php
$dbConfigFile = $sharedPathCalc . 'config/database.php';
if (file_exists($dbConfigFile)) {
    echo "✓ database.php existiert: " . $dbConfigFile . "\n";
} else {
    echo "✗ FEHLER: database.php existiert NICHT: " . $dbConfigFile . "\n";
}

// Check shared/config/autoload.php
$autoloadFile = $sharedPathCalc . 'config/autoload.php';
if (file_exists($autoloadFile)) {
    echo "✓ autoload.php existiert: " . $autoloadFile . "\n";
} else {
    echo "✗ FEHLER: autoload.php existiert NICHT: " . $autoloadFile . "\n";
}

// Check shared/classes/Database.php
$databaseClassFile = $sharedPathCalc . 'classes/Database.php';
if (file_exists($databaseClassFile)) {
    echo "✓ Database.php existiert: " . $databaseClassFile . "\n";
} else {
    echo "✗ FEHLER: Database.php existiert NICHT: " . $databaseClassFile . "\n";
}

// Check shared partials
$headMetaFile = $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-meta.php';
if (file_exists($headMetaFile)) {
    echo "✓ head-meta.php existiert (via DOCUMENT_ROOT): " . $headMetaFile . "\n";
} else {
    echo "✗ FEHLER: head-meta.php existiert NICHT (via DOCUMENT_ROOT): " . $headMetaFile . "\n";
    // Try alternative path
    $altHeadMetaFile = $sharedPathCalc . 'partials/head-meta.php';
    if (file_exists($altHeadMetaFile)) {
        echo "  → Aber existiert via SHARED_PATH: " . $altHeadMetaFile . "\n";
    }
}

echo "\n=== .ENV FILE PRÜFUNG ===\n";
$envFile = $basePathCalc . '.env';
if (file_exists($envFile)) {
    echo "✓ .env existiert: " . $envFile . "\n";
    echo "  Inhalt (ohne sensible Daten):\n";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        if (strpos($key, 'PASSWORD') !== false || strpos($key, 'PASSWORT') !== false || strpos($key, 'KEY') !== false) {
            echo "  " . $key . "=***HIDDEN***\n";
        } else {
            echo "  " . trim($line) . "\n";
        }
    }
} else {
    echo "✗ FEHLER: .env existiert NICHT: " . $envFile . "\n";
}

echo "\n=== ENVIRONMENT VARIABLEN ===\n";
$envVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORT', 'DB_PASSWORD', 'DEBUG_MODE'];
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value !== false) {
        if (strpos($var, 'PASSWORT') !== false || strpos($var, 'PASSWORD') !== false) {
            echo $var . " = ***HIDDEN*** (gesetzt)\n";
        } else {
            echo $var . " = " . $value . "\n";
        }
    } else {
        echo $var . " = (nicht gesetzt)\n";
    }
}

echo "\n=== INIT.PHP LADEN (TEST) ===\n";
try {
    require_once __DIR__ . '/init.php';
    echo "✓ init.php erfolgreich geladen\n";
    echo "BASE_PATH (definiert): " . (defined('BASE_PATH') ? BASE_PATH : 'nicht definiert') . "\n";
    echo "SHARED_PATH (definiert): " . (defined('SHARED_PATH') ? SHARED_PATH : 'nicht definiert') . "\n";
    echo "FILES_PATH (definiert): " . (defined('FILES_PATH') ? FILES_PATH : 'nicht definiert') . "\n";
} catch (Exception $e) {
    echo "✗ FEHLER beim Laden von init.php:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== DATENBANK-VERBINDUNG (TEST) ===\n";
try {
    if (class_exists('Database')) {
        echo "✓ Database-Klasse verfügbar\n";
        $db = Database::getInstance();
        echo "✓ Database::getInstance() erfolgreich\n";
        $conn = $db->getConnection();
        echo "✓ Datenbankverbindung erfolgreich\n";
        echo "  Driver: " . $db->getDriver() . "\n";
    } else {
        echo "✗ Database-Klasse nicht verfügbar\n";
    }
} catch (Exception $e) {
    echo "✗ FEHLER bei Datenbankverbindung:\n";
    echo "  " . $e->getMessage() . "\n";
}

echo "\n=== FUNCTIONS.PHP LADEN (TEST) ===\n";
try {
    if (function_exists('getCategories')) {
        echo "✓ getCategories() Funktion verfügbar\n";

        echo "\n=== GETCATEGORIES() AUSFÜHREN (TEST) ===\n";
        $categories = getCategories();
        echo "✓ getCategories() erfolgreich ausgeführt\n";
        echo "  Anzahl Kategorien: " . count($categories) . "\n";
        if (count($categories) > 0) {
            echo "  Erste Kategorie: " . ($categories[0]['name'] ?? 'N/A') . "\n";
        }
    } else {
        echo "✗ getCategories() Funktion nicht verfügbar\n";
    }
} catch (Exception $e) {
    echo "✗ FEHLER bei getCategories():\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n  Stack Trace:\n";
    echo "  " . str_replace("\n", "\n  ", $e->getTraceAsString()) . "\n";
}

echo "\n=== SESSION (TEST) ===\n";
echo "Session Status: " . session_status() . " (1=disabled, 2=active)\n";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✓ Session aktiv\n";
    echo "  Session Name: " . session_name() . "\n";
    echo "  Session ID: " . session_id() . "\n";
} else {
    echo "○ Session nicht aktiv\n";
}

echo "\n</pre>\n";
echo "<p><a href='/files/'>← Zurück zu /files/</a></p>\n";

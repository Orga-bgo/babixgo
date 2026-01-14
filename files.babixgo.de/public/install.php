<?php
/**
 * BabixGO Files - Categories Installation Script
 * Run this to add categories functionality to existing database
 * 
 * Access via: https://files.babixgo.de/install.php
 * DELETE THIS FILE AFTER INSTALLATION!
 */

// Prevent running multiple times
$lockFile = __DIR__ . '/../.installed';

if (file_exists($lockFile)) {
    die('Installation bereits durchgeführt. Bitte lösche diese Datei aus Sicherheitsgründen.');
}

// Auto-detect config.php location (works in dev and production)
if (file_exists(__DIR__ . '/../includes/config.php')) {
    // Development: includes is outside public/
    require_once __DIR__ . '/../includes/config.php';
} elseif (file_exists(__DIR__ . '/includes/config.php')) {
    // Production: includes is inside public/
    require_once __DIR__ . '/includes/config.php';
} else {
    die('Error: Could not locate config.php');
}

$error = '';
$success = '';
$installed = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    try {
        // Connect to database
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($db->connect_error) {
            throw new Exception('Datenbankverbindung fehlgeschlagen: ' . $db->connect_error);
        }
        
        $db->set_charset('utf8mb4');
        
        // Create categories table
        $sql = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            icon VARCHAR(255),
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$db->query($sql)) {
            throw new Exception('Fehler beim Erstellen der categories-Tabelle: ' . $db->error);
        }
        
        // Add category_id to downloads table if it doesn't exist
        $checkColumn = $db->query("SHOW COLUMNS FROM downloads LIKE 'category_id'");
        if ($checkColumn === false) {
            throw new Exception('Fehler beim Prüfen der downloads-Tabelle: ' . $db->error);
        }
        
        if ($checkColumn->num_rows == 0) {
            // Add column
            $sql = "ALTER TABLE downloads ADD COLUMN category_id INT AFTER id";
            if (!$db->query($sql)) {
                throw new Exception('Fehler beim Hinzufügen der category_id zur downloads-Tabelle: ' . $db->error);
            }
            
            // Add index
            $sql = "ALTER TABLE downloads ADD INDEX idx_category_id (category_id)";
            if (!$db->query($sql)) {
                throw new Exception('Fehler beim Hinzufügen des Index für category_id: ' . $db->error);
            }
            
            // Add foreign key
            $sql = "ALTER TABLE downloads ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL";
            if (!$db->query($sql)) {
                throw new Exception('Fehler beim Hinzufügen der Foreign Key Constraint: ' . $db->error);
            }
        }
        $checkColumn->free();
        
        // Insert initial categories
        $sql = "INSERT IGNORE INTO categories (name, slug, description, sort_order) VALUES
                ('Scripts', 'scripts', 'Nützliche Scripts und Tools für deine Downloads', 1),
                ('Freundschaftsbalken - Android', 'freundschaftsbalken-android', 'Freundschaftsbalken-Tools speziell für Android-Geräte', 2),
                ('Freundschaftsbalken - Windows', 'freundschaftsbalken-windows', 'Freundschaftsbalken-Tools für Windows-Systeme', 3)";
        
        if (!$db->query($sql)) {
            throw new Exception('Fehler beim Einfügen der initialen Kategorien: ' . $db->error);
        }
        
        $db->close();
        
        // Create lock file
        file_put_contents($lockFile, date('Y-m-d H:i:s'));
        
        $installed = true;
        $success = 'Kategorien-Installation erfolgreich abgeschlossen! Bitte lösche diese Datei (install.php) aus Sicherheitsgründen.';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Kategorien-Installation - BabixGO Files</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="main-content auth-container">
        <div class="container">
            <div class="auth-card content-card" style="max-width: 500px;">
                <div class="auth-header">
                    <h1>📁 Kategorien-Installation</h1>
                    <p class="text-muted">BabixGO Files - Kategorien-System hinzufügen</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <p><a href="/" class="btn btn-primary" style="width: 100%;">Zur Startseite</a></p>
                <?php elseif (!$installed): ?>
                    <form method="post" action="">
                        <p class="text-muted mb-2">Dieses Script fügt das Kategorien-System zur bestehenden Datenbank hinzu.</p>
                        
                        <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                            <strong>Hinweis:</strong> Dieses Script setzt voraus, dass die Basistabellen (users, downloads, comments) bereits existieren.
                        </div>
                        
                        <h3>Folgende Änderungen werden vorgenommen:</h3>
                        <ul style="margin-bottom: 1.5rem;">
                            <li>Erstellen der categories-Tabelle</li>
                            <li>Hinzufügen von category_id zur downloads-Tabelle</li>
                            <li>Einfügen von 3 initialen Kategorien</li>
                        </ul>
                        
                        <div class="form-actions">
                            <button type="submit" name="install" value="1" class="btn btn-primary" style="width: 100%;">
                                🚀 Kategorien-Installation starten
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>

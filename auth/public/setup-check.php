<?php
/**
 * Setup Verification Script
 * Check if the system is properly configured
 * 
 * Usage: Run this script after installation to verify setup
 * Delete this file after successful verification for security
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

// Check if configuration exists
$checks = [
    'Database Config' => file_exists(SHARED_PATH . 'config/database.php'),
    'Session Config' => file_exists(SHARED_PATH . 'config/session.php'),
    'Autoloader' => file_exists(SHARED_PATH . 'config/autoload.php'),
    'Database Class' => file_exists(SHARED_PATH . 'classes/Database.php'),
    'User Class' => file_exists(SHARED_PATH . 'classes/User.php'),
    'Download Class' => file_exists(SHARED_PATH . 'classes/Download.php'),
    'Comment Class' => file_exists(SHARED_PATH . 'classes/Comment.php'),
    'SQL Schema' => file_exists(SHARED_PATH . 'create-tables.sql'),
];

// Check directories
$dirs = [
    'Downloads APK' => BASE_PATH . 'downloads/apk',
    'Downloads EXE' => BASE_PATH . 'downloads/exe',
    'Downloads Scripts' => BASE_PATH . 'downloads/scripts',
];

foreach ($dirs as $name => $dir) {
    $checks[$name . ' Directory'] = is_dir($dir);
    if (is_dir($dir)) {
        $checks[$name . ' Writable'] = is_writable($dir);
    }
}

// Check PHP version
$checks['PHP Version >= 7.4'] = version_compare(PHP_VERSION, '7.4.0', '>=');

// Check extensions
$checks['PDO Extension'] = extension_loaded('pdo');
$checks['PDO MySQL'] = extension_loaded('pdo_mysql');
$checks['Fileinfo Extension'] = extension_loaded('fileinfo');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Verification - babixgo.de</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        
        .checks {
            list-style: none;
        }
        
        .check-item {
            padding: 0.75rem;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .check-item:last-child {
            border-bottom: none;
        }
        
        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status.pass {
            background: #27ae60;
            color: white;
        }
        
        .status.fail {
            background: #e74c3c;
            color: white;
        }
        
        .summary {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .summary h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Setup Verification</h1>
        
        <div class="warning">
            <strong>Security Notice:</strong> Please delete this file (setup-check.php) after verification for security purposes.
        </div>
        
        <ul class="checks">
            <?php
            $passed = 0;
            $total = count($checks);
            
            foreach ($checks as $name => $status):
                if ($status) $passed++;
            ?>
                <li class="check-item">
                    <span><?= htmlspecialchars($name) ?></span>
                    <span class="status <?= $status ? 'pass' : 'fail' ?>">
                        <?= $status ? '✓ PASS' : '✗ FAIL' ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <div class="summary">
            <h2>Summary</h2>
            <p><strong><?= $passed ?> of <?= $total ?> checks passed</strong></p>
            
            <?php if ($passed === $total): ?>
                <p style="color: #27ae60; margin-top: 1rem;">
                    ✓ All checks passed! Your system is properly configured.
                </p>
                <p style="margin-top: 1rem;">Next steps:</p>
                <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
                    <li>Configure database credentials in <code>shared/config/database.php</code></li>
                    <li>Import database schema: <code>mysql -u user -p database < shared/create-tables.sql</code></li>
                    <li>Delete this verification file for security</li>
                    <li>Visit <a href="/register.php">/register.php</a> to test registration</li>
                </ol>
            <?php else: ?>
                <p style="color: #e74c3c; margin-top: 1rem;">
                    ✗ Some checks failed. Please fix the issues above before proceeding.
                </p>
            <?php endif; ?>
            
            <a href="/login.php" class="btn">Go to Login Page</a>
        </div>
    </div>
</body>
</html>

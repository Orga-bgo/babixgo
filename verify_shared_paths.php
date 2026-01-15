<?php
echo "=== Comprehensive Shared Path Test ===\n\n";

// Find all PHP files that define BASE_PATH
$files = shell_exec('grep -r "define.*BASE_PATH.*dirname" babixgo.de --include="*.php" -l');
$file_list = array_filter(explode("\n", trim($files)));

$all_good = true;
foreach ($file_list as $file) {
    $full_path = '/home/runner/work/babixgo/babixgo/' . $file;
    $content = file_get_contents($full_path);
    
    if (preg_match('/dirname\(__DIR__,\s*(\d+)\)/', $content, $matches)) {
        $level = (int)$matches[1];
        $file_dir = dirname($full_path);
        $computed_base = dirname($file_dir, $level);
        $expected_base = '/home/runner/work/babixgo/babixgo';
        $shared = $computed_base . '/shared/';
        
        if ($computed_base !== $expected_base) {
            echo "❌ $file: dirname(..., $level) → $computed_base (expected $expected_base)\n";
            $all_good = false;
        } elseif (!is_dir($shared)) {
            echo "❌ $file: Shared not found at $shared\n";
            $all_good = false;
        }
    }
}

if ($all_good) {
    echo "✅ All files correctly configured to access shared directory!\n";
    echo "\nTotal files checked: " . count($file_list) . "\n";
} else {
    echo "\n❌ Some files have incorrect paths\n";
}

// Also test the $_SERVER['DOCUMENT_ROOT'] pattern
echo "\n=== Testing DOCUMENT_ROOT Pattern ===\n";
$_SERVER['DOCUMENT_ROOT'] = '/home/runner/work/babixgo/babixgo/babixgo.de';
$shared_path = dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/';
if (is_dir($shared_path)) {
    echo "✅ dirname(\$_SERVER['DOCUMENT_ROOT']) . '/shared/' → $shared_path\n";
    
    // Check for key files
    $key_files = [
        'config/database.php',
        'config/session.php',
        'classes/User.php',
        'partials/header.php',
    ];
    
    foreach ($key_files as $key_file) {
        $path = $shared_path . $key_file;
        if (file_exists($path)) {
            echo "  ✅ $key_file exists\n";
        } else {
            echo "  ❌ $key_file NOT FOUND\n";
        }
    }
} else {
    echo "❌ Shared directory not accessible via dirname(\$_SERVER['DOCUMENT_ROOT'])\n";
}

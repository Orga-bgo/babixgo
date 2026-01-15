<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

if ($uri === '/') {
    $file = __DIR__ . '/index.php';
}

if (is_file($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($file);
        return true;
    }
    
    if ($ext === 'php') {
        include $file;
        return true;
    }
    
    return false;
}

if (is_dir($file)) {
    $indexFile = rtrim($file, '/') . '/index.php';
    if (file_exists($indexFile)) {
        include $indexFile;
        return true;
    }
    $indexHtml = rtrim($file, '/') . '/index.html';
    if (file_exists($indexHtml)) {
        readfile($indexHtml);
        return true;
    }
}

http_response_code(404);
$notFoundFile = __DIR__ . '/404.php';
if (file_exists($notFoundFile)) {
    include $notFoundFile;
} else {
    echo '<h1>404 Not Found</h1>';
}
return true;

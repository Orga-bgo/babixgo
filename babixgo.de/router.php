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
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'html'  => 'text/html',
        'htm'   => 'text/html',
        'xml'   => 'application/xml',
        'txt'   => 'text/plain',
        'mp4'   => 'video/mp4',
        'webm'  => 'video/webm',
        'pdf'   => 'application/pdf',
    ];
    
    if ($ext === 'php') {
        include $file;
        return true;
    }
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($file);
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
        header('Content-Type: text/html');
        readfile($indexHtml);
        return true;
    }
}

$uriWithoutSlash = rtrim($uri, '/');
$phpFile = __DIR__ . $uriWithoutSlash . '.php';
if (is_file($phpFile)) {
    include $phpFile;
    return true;
}

$parts = explode('/', trim($uri, '/'));

// Handle /files/kategorie/{slug} routes
if (count($parts) >= 3 && $parts[0] === 'files' && $parts[1] === 'kategorie') {
    $categoryFile = __DIR__ . '/files/category.php';
    if (is_file($categoryFile)) {
        include $categoryFile;
        return true;
    }
}

// Handle /files/download/{id} routes
if (count($parts) >= 3 && $parts[0] === 'files' && $parts[1] === 'download') {
    $downloadFile = __DIR__ . '/files/download.php';
    if (is_file($downloadFile)) {
        include $downloadFile;
        return true;
    }
}

if (count($parts) >= 2) {
    $dir = __DIR__ . '/' . $parts[0];
    $subFile = $parts[1];
    $phpPath = $dir . '/' . $subFile . '.php';
    if (is_file($phpPath)) {
        include $phpPath;
        return true;
    }
}

http_response_code(404);
error_log("404 Not Found: " . $uri);
$notFoundFile = __DIR__ . '/404.php';
if (file_exists($notFoundFile)) {
    include $notFoundFile;
} else {
    echo '<h1>404 Not Found</h1>';
}
return true;

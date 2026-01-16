<?php
echo '__DIR__: ' . __DIR__ . PHP_EOL;
echo 'dirname(__DIR__): ' . dirname(__DIR__) . PHP_EOL;
echo 'dirname(__DIR__) . "/": ' . dirname(__DIR__) . '/' . PHP_EOL;
echo 'dirname(__DIR__) . "/shared/": ' . dirname(__DIR__) . '/shared/' . PHP_EOL;

$base = dirname(__DIR__) . '/';
$shared = $base . 'shared/';
echo 'BASE_PATH: ' . $base . PHP_EOL;
echo 'SHARED_PATH: ' . $shared . PHP_EOL;
echo 'database.php path: ' . $shared . 'config/database.php' . PHP_EOL;
echo 'File exists: ' . (file_exists($shared . 'config/database.php') ? 'YES' : 'NO') . PHP_EOL;

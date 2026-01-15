<?php
if (!defined('BASE_PATH')) {
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        define('BASE_PATH', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/');
    } else {
        define('BASE_PATH', realpath(__DIR__ . '/../../') . '/');
    }
}

if (!defined('SHARED_PATH')) {
    define('SHARED_PATH', BASE_PATH . 'shared/');
}

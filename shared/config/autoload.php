<?php
/**
 * Autoloader for shared classes
 */

spl_autoload_register(function ($className) {
    $classFile = SHARED_PATH . 'classes/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

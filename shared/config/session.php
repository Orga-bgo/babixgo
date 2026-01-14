<?php

/**
 * Session configuration
 */
ini_set('session.cookie_domain', '.babixgo.de');
return [
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
];

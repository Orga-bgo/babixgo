<?php
/**
 * Logout page
 */
require_once(__DIR__ . '/../shared/classes/Session.php');

$session = new Session();
$session->destroy();

header('Location: https://babixgo.de');
exit;

<?php
/**
 * Logout Script
 * babixGO - admin/logout.php
 */

session_start();
session_destroy();
header('Location: contacts.php');
exit;
?>
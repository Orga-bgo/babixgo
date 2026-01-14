<?php
/**
 * Admin Navigation Bar
 * Navigation component for admin panel pages
 * 
 * Variables:
 * - $currentAdminPage: Current admin page identifier for active state
 */

$currentAdminPage = $currentAdminPage ?? '';
?>

<nav class="main-nav">
    <div class="nav-container">
        <a href="/admin/" class="logo">babixgo.de Admin</a>
        <ul class="nav-menu">
            <li><a href="/admin/" class="<?= $currentAdminPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="/admin/users.php" class="<?= $currentAdminPage === 'users' ? 'active' : '' ?>">Users</a></li>
            <li><a href="/admin/downloads.php" class="<?= $currentAdminPage === 'downloads' ? 'active' : '' ?>">Downloads</a></li>
            <li><a href="/admin/comments.php" class="<?= $currentAdminPage === 'comments' ? 'active' : '' ?>">Comments</a></li>
            <li><a href="/">My Profile</a></li>
            <li><a href="/logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

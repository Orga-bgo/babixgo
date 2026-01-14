<?php
/**
 * Navigation Bar
 * Shared navigation component for authenticated users
 * 
 * Variables:
 * - $currentPage: Current page identifier for active state
 * - $showAdminLink: Whether to show admin panel link (default: auto-detect)
 */

// Auto-detect if user is logged in
$isLoggedIn = User::isLoggedIn();
$isAdmin = User::isAdmin();

// Set defaults
$currentPage = $currentPage ?? '';
$showAdminLink = $showAdminLink ?? $isAdmin;
?>

<nav class="main-nav">
    <div class="nav-container">
        <a href="/" class="logo">babixgo.de</a>
        <?php if ($isLoggedIn): ?>
        <ul class="nav-menu">
            <li><a href="/" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">Profile</a></li>
            <?php if ($showAdminLink): ?>
            <li><a href="/admin/" class="<?= $currentPage === 'admin' ? 'active' : '' ?>">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="/logout.php">Logout</a></li>
        </ul>
        <?php else: ?>
        <ul class="nav-menu">
            <li><a href="/login.php" class="<?= $currentPage === 'login' ? 'active' : '' ?>">Login</a></li>
            <li><a href="/register.php" class="<?= $currentPage === 'register' ? 'active' : '' ?>">Register</a></li>
        </ul>
        <?php endif; ?>
    </div>
</nav>

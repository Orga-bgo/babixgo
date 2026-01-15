<?php
/**
 * Shared Header Partial
 * Used across all pages on babixgo.de
 * Detects current section and shows appropriate navigation with user menu
 */

// Check if user is logged in (session must already be started)
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
$username = $_SESSION['username'] ?? '';

// Detect current section from URL
$currentPath = $_SERVER['REQUEST_URI'];
$currentSection = 'main'; // default

if (strpos($currentPath, '/admin/') !== false) {
    $currentSection = 'admin';
} elseif (strpos($currentPath, '/user/') !== false) {
    $currentSection = 'user';
} elseif (strpos($currentPath, '/files/') !== false) {
    $currentSection = 'files';
} elseif (strpos($currentPath, '/auth/') !== false) {
    $currentSection = 'auth';
}
?>

<!-- ========== HEADER ========== -->
<header class="site-header">
  <div class="header-brand">
    <a href="/" class="header-logo">
      <span class="logo-babix">babix</span><span class="logo-go">GO</span>
    </a>
    <div class="header-tagline">Monopoly Go Würfel, Accounts und mehr!</div>
  </div>
  
  <button class="menu-toggle" aria-label="Menü öffnen" aria-controls="mobileMenu" aria-expanded="false" id="menuToggle" type="button">
    <span></span>
    <span></span>
    <span></span>
  </button>
</header>

<!-- ========== MOBILE MENU ========== -->
<nav class="mobile-menu" id="mobileMenu" aria-hidden="true">
  <div class="mobile-menu-inner">
    <a href="/" <?= $currentSection === 'main' ? 'class="active"' : '' ?>>Home</a>
    
    <div class="menu-dropdown">
      <button class="menu-dropdown-toggle" aria-expanded="false" type="button">
        Angebote
        <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="menu-dropdown-content">
        <a href="/wuerfel/">Würfel</a>
        <a href="/partnerevents/">Partnerevents</a>
        <a href="/accounts/">Accounts</a>
        <a href="/tycoon-racers/">Tycoon Racers</a>
        <a href="/anleitungen/freundschaftsbalken-fuellen/">Freundschaftsbalken</a>
        <a href="/sticker/">Sticker</a>
      </div>
    </div>
    
    <a href="/anleitungen/">Anleitungen</a>
    <a href="/files/" <?= $currentSection === 'files' ? 'class="active"' : '' ?>>Downloads</a>
    <a href="/kontakt/">Kontakt</a>
    
    <?php if ($isLoggedIn): ?>
      <!-- User Menu -->
      <div class="menu-divider"></div>
      <div class="menu-user">
        <div class="menu-user-header">
          <span class="user-icon">👤</span>
          <span class="user-name"><?= htmlspecialchars($username) ?></span>
        </div>
        <a href="/user/" <?= $currentSection === 'user' ? 'class="active"' : '' ?>>Mein Profil</a>
        <a href="/user/my-comments">Meine Kommentare</a>
        <a href="/user/my-downloads">Meine Downloads</a>
        <a href="/user/settings">Einstellungen</a>
        <?php if ($isAdmin): ?>
          <div class="menu-divider"></div>
          <a href="/admin/" <?= $currentSection === 'admin' ? 'class="active"' : '' ?>>⚙️ Admin-Panel</a>
        <?php endif; ?>
        <div class="menu-divider"></div>
        <a href="/auth/logout">Logout</a>
      </div>
    <?php else: ?>
      <!-- Guest Links -->
      <div class="menu-divider"></div>
      <a href="/auth/login" class="menu-btn-login">Login</a>
      <a href="/auth/register" class="menu-btn-register">Registrieren</a>
    <?php endif; ?>
    
    <div class="menu-social">
      <a href="https://wa.me/4915223842897" target="_blank" rel="noopener" class="whatsapp">
        <img src="/assets/icons/whatsapp_schriftzug.svg" class="menu-social-icon" alt="Kontakt via WhatsApp" width="150" height="35">
      </a>
      <a href="https://www.facebook.com/share/1DC2snqois/" target="_blank" rel="noopener" class="facebook">
        <img src="/assets/icons/facebook_schriftzug.svg" class="menu-social-icon" alt="Kontakt via Facebook" width="125" height="24">
      </a>
    </div>
  </div>
</nav>

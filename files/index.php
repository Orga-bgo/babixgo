<?php
/**
 * Files subdomain index page
 */
$pageTitle = 'Dateien - babixgo';
require_once(__DIR__ . '/../shared/partials/header.php');
require_once(__DIR__ . '/../shared/classes/Database.php');
require_once(__DIR__ . '/../shared/classes/Download.php');
require_once(__DIR__ . '/../shared/classes/Session.php');

$session = new Session();
?>

<h1>Verfügbare Dateien</h1>
<p>Durchsuchen Sie unsere Dateisammlung.</p>

<div class="file-categories">
    <h2>Kategorien</h2>
    <ul>
        <li><a href="/browse.php?type=apk">Android Apps (APK)</a></li>
        <li><a href="/browse.php?type=exe">Windows Programme (EXE)</a></li>
        <li><a href="/browse.php?type=bash">Bash Scripts</a></li>
        <li><a href="/browse.php?type=python">Python Scripts</a></li>
        <li><a href="/browse.php?type=powershell">PowerShell Scripts</a></li>
    </ul>
</div>

<?php require_once(__DIR__ . '/../shared/partials/footer.php'); ?>

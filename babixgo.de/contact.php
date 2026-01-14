<?php
/**
 * Contact page
 */
$pageTitle = 'Kontakt - babixgo';
require_once(__DIR__ . '/../shared/partials/header.php');
?>

<h1>Kontakt</h1>
<p>Nehmen Sie Kontakt mit uns auf.</p>

<form method="post" action="">
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="message">Nachricht:</label>
        <textarea id="message" name="message" required></textarea>
    </div>
    <button type="submit">Senden</button>
</form>

<?php require_once(__DIR__ . '/../shared/partials/footer.php'); ?>

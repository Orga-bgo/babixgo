<?php
/**
 * Registration page
 */
$pageTitle = 'Registrieren - babixgo';
require_once(__DIR__ . '/../shared/partials/header.php');
require_once(__DIR__ . '/../shared/classes/Database.php');
require_once(__DIR__ . '/../shared/classes/User.php');
require_once(__DIR__ . '/../shared/classes/Session.php');

$session = new Session();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $user->username = $_POST['username'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if ($user->password !== $password_confirm) {
        $error = 'Passwörter stimmen nicht überein.';
    } else {
        if ($user->create()) {
            $success = 'Registrierung erfolgreich! Sie können sich jetzt anmelden.';
        } else {
            $error = 'Registrierung fehlgeschlagen. Bitte versuchen Sie es erneut.';
        }
    }
}
?>

<h1>Registrieren</h1>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="post" action="">
    <div>
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="password_confirm">Passwort bestätigen:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    <button type="submit">Registrieren</button>
</form>

<p>Bereits ein Konto? <a href="/login.php">Anmelden</a></p>

<?php require_once(__DIR__ . '/../shared/partials/footer.php'); ?>

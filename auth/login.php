<?php
/**
 * Login page
 */
$pageTitle = 'Login - babixgo';
require_once(__DIR__ . '/../shared/partials/header.php');
require_once(__DIR__ . '/../shared/classes/Database.php');
require_once(__DIR__ . '/../shared/classes/User.php');
require_once(__DIR__ . '/../shared/classes/Session.php');

$session = new Session();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $user->email = $_POST['email'] ?? '';
    $user->password = $_POST['password'] ?? '';
    
    if ($user->login()) {
        $session->set('user_id', $user->id);
        $session->set('username', $user->username);
        header('Location: https://babixgo.de');
        exit;
    } else {
        $error = 'Ungültige Anmeldedaten.';
    }
}
?>

<h1>Anmelden</h1>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="post" action="">
    <div>
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Anmelden</button>
</form>

<p>Noch kein Konto? <a href="/register.php">Registrieren</a></p>

<?php require_once(__DIR__ . '/../shared/partials/footer.php'); ?>

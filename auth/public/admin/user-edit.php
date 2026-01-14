<?php
/**
 * User Edit Page
 */

require_once __DIR__ . '/../includes/admin-check.php';

$userId = intval($_GET['id'] ?? 0);

if (!$userId) {
    header('Location: /admin/users.php');
    exit;
}

$db = Database::getInstance();
$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);

if (!$user) {
    header('Location: /admin/users.php');
    exit;
}

// Get user stats
$commentCount = $db->fetchOne("SELECT COUNT(*) as count FROM comments WHERE user_id = ?", [$userId])['count'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $role = $_POST['role'] ?? 'user';
    $isVerified = isset($_POST['is_verified']) ? 1 : 0;
    
    if (!in_array($role, ['user', 'admin'])) {
        $error = 'Invalid role';
    } else {
        $sql = "UPDATE users SET role = ?, is_verified = ? WHERE id = ?";
        $db->query($sql, [$role, $isVerified, $userId]);
        
        $success = 'User updated successfully';
        // Refresh user data
        $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - babixgo.de</title>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/admin.css">
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/admin.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/admin/" class="logo">babixgo.de Admin</a>
            <ul class="nav-menu">
                <li><a href="/admin/">Dashboard</a></li>
                <li><a href="/admin/users.php" class="active">Users</a></li>
                <li><a href="/admin/downloads.php">Downloads</a></li>
                <li><a href="/admin/comments.php">Comments</a></li>
                <li><a href="/">My Profile</a></li>
                <li><a href="/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h1>Edit User: <?= htmlspecialchars($user['username'], ENT_QUOTES) ?></h1>
        
        <?php if (isset($success)): ?>
            <div class="message message-success"><?= htmlspecialchars($success, ENT_QUOTES) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
        <?php endif; ?>
        
        <div class="profile-grid">
            <div class="profile-card">
                <h2>User Information</h2>
                
                <div class="info-row">
                    <label>User ID:</label>
                    <span><?= $user['id'] ?></span>
                </div>
                
                <div class="info-row">
                    <label>Username:</label>
                    <span><?= htmlspecialchars($user['username'], ENT_QUOTES) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Email:</label>
                    <span><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Friendship Link:</label>
                    <span><?= htmlspecialchars($user['friendship_link'], ENT_QUOTES) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Description:</label>
                    <p><?= $user['description'] ? htmlspecialchars($user['description'], ENT_QUOTES) : '<em>None</em>' ?></p>
                </div>
                
                <div class="info-row">
                    <label>Registered:</label>
                    <span><?= date('F j, Y g:i A', strtotime($user['created_at'])) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Last Updated:</label>
                    <span><?= date('F j, Y g:i A', strtotime($user['updated_at'])) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Total Comments:</label>
                    <span><?= number_format($commentCount) ?></span>
                </div>
            </div>
            
            <div class="profile-card">
                <h2>Edit User Settings</h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                    
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" class="form-control">
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="is_verified" value="1" <?= $user['is_verified'] ? 'checked' : '' ?>>
                            Email Verified
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/admin/users.php" class="btn btn-secondary">Back to Users</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

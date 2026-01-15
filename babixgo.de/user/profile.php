<?php
/**
 * User Dashboard/Profile Page
 */

require_once __DIR__ . '/includes/auth-check.php';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

// Get user comment count
$commentCount = $user->getUserCommentCount($_SESSION['user_id']);

// Get last 5 comments
$comments = $user->getUserComments($_SESSION['user_id'], 5);

// Generate friendship link URL
$friendshipUrl = 'https://babixgo.de/friend/' . $userData['friendship_link'];

// Page configuration
$pageTitle = 'My Profile - babixgo.de';
$currentPage = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/main.css">
</head>
<body>
    <?php require_once SHARED_PATH . 'partials/header.php'; ?>
    
    <div class="container">
        <div class="profile-header">
            <h1>Welcome, <?= htmlspecialchars($userData['username'], ENT_QUOTES) ?>!</h1>
            <p class="profile-subtitle">Member since <?= date('F j, Y', strtotime($userData['created_at'])) ?></p>
        </div>
        
        <div class="profile-grid">
            <div class="profile-card">
                <h2>Account Information</h2>
                <div class="info-row">
                    <label>Username:</label>
                    <span><?= htmlspecialchars($userData['username'], ENT_QUOTES) ?></span>
                </div>
                <div class="info-row">
                    <label>Email:</label>
                    <span><?= htmlspecialchars($userData['email'], ENT_QUOTES) ?></span>
                </div>
                <div class="info-row">
                    <label>Role:</label>
                    <span class="badge badge-<?= $userData['role'] ?>"><?= ucfirst($userData['role']) ?></span>
                </div>
                <div class="info-row">
                    <label>Status:</label>
                    <span class="badge badge-<?= $userData['is_verified'] ? 'success' : 'warning' ?>">
                        <?= $userData['is_verified'] ? 'Verified' : 'Unverified' ?>
                    </span>
                </div>
                <div class="info-row">
                    <label>Description:</label>
                    <p><?= $userData['description'] ? htmlspecialchars($userData['description'], ENT_QUOTES) : '<em>No description set</em>' ?></p>
                </div>
                <a href="/user/edit-profile" class="btn btn-primary">Edit Profile</a>
            </div>
            
            <div class="profile-card">
                <h2>Friendship Link</h2>
                <p>Share this unique link with your friends:</p>
                <div class="friendship-link-box">
                    <input type="text" id="friendship-link" value="<?= htmlspecialchars($friendshipUrl, ENT_QUOTES) ?>" readonly>
                    <button onclick="copyFriendshipLink()" class="btn btn-secondary">Copy</button>
                </div>
                <p class="hint">Your unique code: <strong><?= htmlspecialchars($userData['friendship_link'], ENT_QUOTES) ?></strong></p>
            </div>
        </div>
        
        <div class="profile-card">
            <h2>Comment Activity</h2>
            <p>Total comments: <strong><?= $commentCount ?></strong></p>
            
            <?php if (!empty($comments)): ?>
                <div class="comments-list">
                    <h3>Recent Comments</h3>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-meta">
                                <span class="comment-domain"><?= htmlspecialchars($comment['domain'], ENT_QUOTES) ?></span>
                                <span class="comment-date"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></span>
                                <span class="badge badge-<?= $comment['status'] ?>"><?= ucfirst($comment['status']) ?></span>
                            </div>
                            <div class="comment-content">
                                <?php
                                $commentText = htmlspecialchars($comment['comment'], ENT_QUOTES);
                                echo strlen($commentText) > 100 ? substr($commentText, 0, 100) . '...' : $commentText;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-state">You haven't posted any comments yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php require_once SHARED_PATH . 'partials/footer.php'; ?>
    <script src="/shared/assets/js/main.js"></script>
    <script>
        function copyFriendshipLink() {
            const input = document.getElementById('friendship-link');
            input.select();
            document.execCommand('copy');
            
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }
    </script>
</body>
</html>

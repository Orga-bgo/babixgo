<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/includes/admin-check.php';

$db = Database::getInstance();

// Get statistics
$userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users")['count'];
$downloadCount = $db->fetchOne("SELECT COUNT(*) as count FROM downloads")['count'];

$comment = new Comment();
$commentCounts = $comment->getCountsByStatus();
$totalComments = array_sum($commentCounts);

// Recent activity
$recentUsers = $db->fetchAll("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 10");

// Calculate date 7 days ago (database-agnostic approach)
$sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
$recentDownloads = $db->fetchAll(
    "SELECT d.*, COUNT(dl.id) as recent_downloads 
     FROM downloads d 
     LEFT JOIN download_logs dl ON d.id = dl.file_id 
     WHERE dl.downloaded_at > ? 
     GROUP BY d.id, d.filename, d.filepath, d.filetype, d.filesize, d.version, d.description, d.download_count, d.active, d.created_at, d.updated_at 
     ORDER BY recent_downloads DESC 
     LIMIT 10",
    [$sevenDaysAgo]
);

$recentComments = $db->fetchAll("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - babixgo.de</title>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/admin.css">
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/admin.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/" class="logo">babixgo.de Admin</a>
            <ul class="nav-menu">
                <li><a href="/admin/" class="active">Dashboard</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/downloads.php">Downloads</a></li>
                <li><a href="/admin/comments.php">Comments</a></li>
                <li><a href="/user/">My Profile</a></li>
                <li><a href="/auth/logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-value"><?= number_format($userCount) ?></div>
                <a href="/admin/users.php" class="stat-link">Manage Users →</a>
            </div>
            
            <div class="stat-card">
                <h3>Total Downloads</h3>
                <div class="stat-value"><?= number_format($downloadCount) ?></div>
                <a href="/admin/downloads.php" class="stat-link">Manage Downloads →</a>
            </div>
            
            <div class="stat-card">
                <h3>Total Comments</h3>
                <div class="stat-value"><?= number_format($totalComments) ?></div>
                <div class="stat-breakdown">
                    <span class="badge badge-pending"><?= $commentCounts['pending'] ?> Pending</span>
                    <span class="badge badge-approved"><?= $commentCounts['approved'] ?> Approved</span>
                    <span class="badge badge-spam"><?= $commentCounts['spam'] ?> Spam</span>
                </div>
                <a href="/admin/comments.php" class="stat-link">Moderate Comments →</a>
            </div>
        </div>
        
        <div class="activity-grid">
            <div class="activity-card">
                <h2>Recent User Registrations</h2>
                <?php if (!empty($recentUsers)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['username'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-state">No recent registrations</p>
                <?php endif; ?>
            </div>
            
            <div class="activity-card">
                <h2>Popular Downloads (Last 7 Days)</h2>
                <?php if (!empty($recentDownloads)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Type</th>
                                <th>Downloads</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDownloads as $download): ?>
                                <tr>
                                    <td><?= htmlspecialchars($download['filename'], ENT_QUOTES) ?></td>
                                    <td><span class="badge badge-<?= $download['filetype'] ?>"><?= $download['filetype'] ?></span></td>
                                    <td><?= number_format($download['recent_downloads']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-state">No downloads in the last 7 days</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="activity-card">
            <h2>Recent Comments</h2>
            <?php if (!empty($recentComments)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Domain</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentComments as $comment): ?>
                            <tr>
                                <td><?= $comment['id'] ?></td>
                                <td><?= htmlspecialchars($comment['username'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($comment['domain'], ENT_QUOTES) ?></td>
                                <td class="comment-preview">
                                    <?php
                                    $commentText = htmlspecialchars($comment['comment'], ENT_QUOTES);
                                    echo strlen($commentText) > 50 ? substr($commentText, 0, 50) . '...' : $commentText;
                                    ?>
                                </td>
                                <td><span class="badge badge-<?= $comment['status'] ?>"><?= ucfirst($comment['status']) ?></span></td>
                                <td><?= date('M j, Y', strtotime($comment['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">No recent comments</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

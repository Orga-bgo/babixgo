<?php
/**
 * Comment Moderation Page
 */

require_once __DIR__ . '/../includes/admin-check.php';

$db = Database::getInstance();
$comment = new Comment();

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Filter
$status = $_GET['status'] ?? 'all';
$statusFilter = in_array($status, ['pending', 'approved', 'spam']) ? $status : null;

// Get total count
$totalComments = $db->fetchOne(
    "SELECT COUNT(*) as count FROM comments" . ($statusFilter ? " WHERE status = ?" : ""),
    $statusFilter ? [$statusFilter] : []
)['count'];
$totalPages = ceil($totalComments / $perPage);

// Get comments
$comments = $comment->getAll($statusFilter, null, $perPage, $offset);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Moderation - babixgo.de</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/admin/" class="logo">babixgo.de Admin</a>
            <ul class="nav-menu">
                <li><a href="/admin/">Dashboard</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/downloads.php">Downloads</a></li>
                <li><a href="/admin/comments.php" class="active">Comments</a></li>
                <li><a href="/">My Profile</a></li>
                <li><a href="/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h1>Comment Moderation</h1>
        
        <div id="message-container"></div>
        
        <div class="toolbar">
            <div class="filter-buttons">
                <a href="?status=all" class="btn btn-secondary <?= $status === 'all' ? 'active' : '' ?>">All</a>
                <a href="?status=pending" class="btn btn-secondary <?= $status === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="?status=approved" class="btn btn-secondary <?= $status === 'approved' ? 'active' : '' ?>">Approved</a>
                <a href="?status=spam" class="btn btn-secondary <?= $status === 'spam' ? 'active' : '' ?>">Spam</a>
            </div>
            
            <div class="bulk-actions">
                <button onclick="bulkApprove()" class="btn btn-secondary">Approve Selected</button>
                <button onclick="bulkDelete()" class="btn btn-danger">Delete Selected</button>
            </div>
        </div>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
                        <th>ID</th>
                        <th>User</th>
                        <th>Domain</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $c): ?>
                            <tr>
                                <td><input type="checkbox" class="comment-checkbox" value="<?= $c['id'] ?>"></td>
                                <td><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['username'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($c['domain'], ENT_QUOTES) ?></td>
                                <td class="comment-preview">
                                    <?php
                                    $commentText = htmlspecialchars($c['comment'], ENT_QUOTES);
                                    echo strlen($commentText) > 100 ? substr($commentText, 0, 100) . '...' : $commentText;
                                    ?>
                                </td>
                                <td><span class="badge badge-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span></td>
                                <td><?= date('M j, Y g:i A', strtotime($c['created_at'])) ?></td>
                                <td class="actions">
                                    <?php if ($c['status'] !== 'approved'): ?>
                                        <button onclick="updateStatus(<?= $c['id'] ?>, 'approved')" class="btn-small btn-success">Approve</button>
                                    <?php endif; ?>
                                    <?php if ($c['status'] !== 'spam'): ?>
                                        <button onclick="updateStatus(<?= $c['id'] ?>, 'spam')" class="btn-small btn-warning">Spam</button>
                                    <?php endif; ?>
                                    <button onclick="deleteComment(<?= $c['id'] ?>)" class="btn-small btn-danger">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">No comments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&status=<?= $status ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&status=<?= $status ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="/assets/js/admin.js"></script>
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.comment-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }
        
        async function updateStatus(commentId, status) {
            const formData = new FormData();
            formData.append('csrf_token', '<?= getCsrfToken() ?>');
            formData.append('action', 'update_comment_status');
            formData.append('comment_id', commentId);
            formData.append('status', status);
            
            try {
                const response = await fetch('/includes/form-handlers/admin-handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Comment status updated', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(result.error, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
        
        async function deleteComment(commentId) {
            if (!confirm('Are you sure you want to delete this comment?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= getCsrfToken() ?>');
            formData.append('action', 'delete_comment');
            formData.append('comment_id', commentId);
            
            try {
                const response = await fetch('/includes/form-handlers/admin-handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Comment deleted', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(result.error, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
        
        async function bulkApprove() {
            const selected = getSelectedComments();
            if (selected.length === 0) {
                alert('Please select comments to approve');
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= getCsrfToken() ?>');
            formData.append('action', 'bulk_approve_comments');
            formData.append('comment_ids', JSON.stringify(selected));
            
            try {
                const response = await fetch('/includes/form-handlers/admin-handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(result.error, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
        
        async function bulkDelete() {
            const selected = getSelectedComments();
            if (selected.length === 0) {
                alert('Please select comments to delete');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${selected.length} comment(s)?`)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= getCsrfToken() ?>');
            formData.append('action', 'bulk_delete_comments');
            formData.append('comment_ids', JSON.stringify(selected));
            
            try {
                const response = await fetch('/includes/form-handlers/admin-handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(result.error, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
        
        function getSelectedComments() {
            const checkboxes = document.querySelectorAll('.comment-checkbox:checked');
            return Array.from(checkboxes).map(cb => parseInt(cb.value));
        }
    </script>
</body>
</html>

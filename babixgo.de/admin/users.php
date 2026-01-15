<?php
/**
 * User Management Page
 */

require_once __DIR__ . '/includes/admin-check.php';

$db = Database::getInstance();

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Search
$search = trim($_GET['search'] ?? '');
$searchWhere = '';
$searchParams = [];

if ($search) {
    $searchWhere = " WHERE username LIKE ? OR email LIKE ?";
    $searchLike = '%' . $search . '%';
    $searchParams = [$searchLike, $searchLike];
}

// Get total count
$totalUsers = $db->fetchOne("SELECT COUNT(*) as count FROM users" . $searchWhere, $searchParams)['count'];
$totalPages = ceil($totalUsers / $perPage);

// Get users
$sql = "SELECT id, username, email, role, is_verified, created_at FROM users" . $searchWhere . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$users = $db->fetchAll($sql, array_merge($searchParams, [$perPage, $offset]));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - babixgo.de</title>
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
                <li><a href="/user/">My Profile</a></li>
                <li><a href="/auth/logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h1>User Management</h1>
        
        <div id="message-container"></div>
        
        <div class="toolbar">
            <form method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by username or email..." 
                    value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                >
                <button type="submit" class="btn btn-secondary">Search</button>
                <?php if ($search): ?>
                    <a href="/admin/users.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
            
            <div class="bulk-actions">
                <button onclick="bulkVerifyUsers()" class="btn btn-secondary">Verify Selected</button>
                <button onclick="bulkDeleteUsers()" class="btn btn-danger">Delete Selected</button>
            </div>
        </div>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Verified</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><input type="checkbox" class="user-checkbox" value="<?= $user['id'] ?>"></td>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                                <td><span class="badge badge-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span></td>
                                <td>
                                    <span class="badge badge-<?= $user['is_verified'] ? 'success' : 'warning' ?>">
                                        <?= $user['is_verified'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="/admin/user-edit.php?id=<?= $user['id'] ?>" class="btn-small btn-primary">Edit</a>
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <button onclick="deleteUser(<?= $user['id'] ?>)" class="btn-small btn-danger">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="/shared/assets/js/main.js"></script>
    <script src="/shared/assets/js/admin.js"></script>
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }
        
        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                return;
            }
            
            await performAction('delete_user', { user_id: userId });
        }
        
        async function bulkVerifyUsers() {
            const selected = getSelectedUsers();
            if (selected.length === 0) {
                alert('Please select users to verify');
                return;
            }
            
            await performAction('bulk_verify_users', { user_ids: JSON.stringify(selected) });
        }
        
        async function bulkDeleteUsers() {
            const selected = getSelectedUsers();
            if (selected.length === 0) {
                alert('Please select users to delete');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${selected.length} user(s)? This action cannot be undone.`)) {
                return;
            }
            
            await performAction('bulk_delete_users', { user_ids: JSON.stringify(selected) });
        }
        
        function getSelectedUsers() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            return Array.from(checkboxes).map(cb => parseInt(cb.value));
        }
        
        async function performAction(action, data) {
            const formData = new FormData();
            formData.append('csrf_token', '<?= getCsrfToken() ?>');
            formData.append('action', action);
            
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }
            
            try {
                const response = await fetch('/admin/includes/form-handlers/admin-handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(result.error, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
    </script>
</body>
</html>

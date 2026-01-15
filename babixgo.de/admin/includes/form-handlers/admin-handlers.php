<?php
/**
 * Admin Handlers
 * Processes admin panel form submissions
 */

define('BASE_PATH', dirname(__DIR__, 3) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Check if user is admin
if (!User::isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Verify CSRF token
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';
$db = Database::getInstance();

try {
    switch ($action) {
        case 'update_user':
            $userId = intval($_POST['user_id'] ?? 0);
            $role = $_POST['role'] ?? 'user';
            $isVerified = isset($_POST['is_verified']) ? 1 : 0;
            
            if (!in_array($role, ['user', 'admin'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid role']);
                exit;
            }
            
            $sql = "UPDATE users SET role = ?, is_verified = ? WHERE id = ?";
            $db->query($sql, [$role, $isVerified, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;
            
        case 'delete_user':
            $userId = intval($_POST['user_id'] ?? 0);
            
            // Don't allow deleting yourself
            if ($userId === $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
                exit;
            }
            
            $sql = "DELETE FROM users WHERE id = ?";
            $db->query($sql, [$userId]);
            
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;
            
        case 'bulk_verify_users':
            $userIds = json_decode($_POST['user_ids'] ?? '[]', true);
            
            if (empty($userIds)) {
                echo json_encode(['success' => false, 'error' => 'No users selected']);
                exit;
            }
            
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $sql = "UPDATE users SET is_verified = 1 WHERE id IN ($placeholders)";
            $db->query($sql, $userIds);
            
            echo json_encode(['success' => true, 'message' => 'Users verified successfully']);
            break;
            
        case 'bulk_delete_users':
            $userIds = json_decode($_POST['user_ids'] ?? '[]', true);
            
            if (empty($userIds)) {
                echo json_encode(['success' => false, 'error' => 'No users selected']);
                exit;
            }
            
            // Don't allow deleting yourself
            if (in_array($_SESSION['user_id'], $userIds)) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
                exit;
            }
            
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $sql = "DELETE FROM users WHERE id IN ($placeholders)";
            $db->query($sql, $userIds);
            
            echo json_encode(['success' => true, 'message' => 'Users deleted successfully']);
            break;
            
        case 'update_comment_status':
            $commentId = intval($_POST['comment_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            
            $comment = new Comment();
            $result = $comment->updateStatus($commentId, $status);
            
            echo json_encode($result);
            break;
            
        case 'delete_comment':
            $commentId = intval($_POST['comment_id'] ?? 0);
            
            $comment = new Comment();
            $result = $comment->delete($commentId);
            
            echo json_encode($result);
            break;
            
        case 'bulk_approve_comments':
            $commentIds = json_decode($_POST['comment_ids'] ?? '[]', true);
            
            if (empty($commentIds)) {
                echo json_encode(['success' => false, 'error' => 'No comments selected']);
                exit;
            }
            
            $placeholders = implode(',', array_fill(0, count($commentIds), '?'));
            $sql = "UPDATE comments SET status = 'approved' WHERE id IN ($placeholders)";
            $db->query($sql, $commentIds);
            
            echo json_encode(['success' => true, 'message' => 'Comments approved successfully']);
            break;
            
        case 'bulk_delete_comments':
            $commentIds = json_decode($_POST['comment_ids'] ?? '[]', true);
            
            if (empty($commentIds)) {
                echo json_encode(['success' => false, 'error' => 'No comments selected']);
                exit;
            }
            
            $placeholders = implode(',', array_fill(0, count($commentIds), '?'));
            $sql = "DELETE FROM comments WHERE id IN ($placeholders)";
            $db->query($sql, $commentIds);
            
            echo json_encode(['success' => true, 'message' => 'Comments deleted successfully']);
            break;
            
        case 'update_download':
            $downloadId = intval($_POST['download_id'] ?? 0);
            $download = new Download();
            
            $data = [
                'filename' => trim($_POST['filename'] ?? ''),
                'filetype' => $_POST['filetype'] ?? '',
                'filesize' => intval($_POST['filesize'] ?? 0),
                'version' => trim($_POST['version'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'active' => isset($_POST['active']) ? 1 : 0
            ];
            
            $result = $download->update($downloadId, $data);
            echo json_encode($result);
            break;
            
        case 'delete_download':
            $downloadId = intval($_POST['download_id'] ?? 0);
            $download = new Download();
            
            // Get file path before deleting
            $downloadData = $download->getById($downloadId);
            if ($downloadData) {
                $filePath = BASE_PATH . 'babixgo.de/file-storage/' . $downloadData['filetype'] . '/' . basename($downloadData['filepath']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            $result = $download->delete($downloadId);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Admin handler error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Operation failed. Please try again.']);
}

<?php
/**
 * Profile Handler
 * Processes profile update and password change submissions
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

// Check if user is logged in
if (!User::isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Verify CSRF token
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';
$user = new User();

try {
    if ($action === 'update_profile') {
        // Update profile
        $username = trim($_POST['username'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be between 3 and 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }
        
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }
        
        $result = $user->updateProfile($_SESSION['user_id'], [
            'username' => $username,
            'description' => $description
        ]);
        
        if ($result['success']) {
            $_SESSION['username'] = $username;
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
        
    } elseif ($action === 'change_password') {
        // Change password
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required';
        }
        
        if (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        } elseif (!preg_match('/[A-Z]/', $newPassword)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $newPassword)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $newPassword)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }
        
        $result = $user->changePassword($_SESSION['user_id'], $currentPassword, $newPassword);
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Password changed successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Profile handler error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Operation failed. Please try again.']);
}

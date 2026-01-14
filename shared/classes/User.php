<?php
/**
 * User Class
 * Handles user authentication and management
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Register a new user
     */
    public function register($username, $email, $password) {
        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        
        // Generate unique friendship link
        $friendshipLink = $this->generateFriendshipLink();
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password_hash, verification_token, friendship_link) 
                VALUES (?, ?, ?, ?, ?)";
        
        try {
            $this->db->query($sql, [$username, $email, $passwordHash, $verificationToken, $friendshipLink]);
            return [
                'success' => true,
                'user_id' => $this->db->lastInsertId(),
                'verification_token' => $verificationToken
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Username or email already exists'];
        }
    }
    
    /**
     * Verify email with token
     */
    public function verifyEmail($token) {
        $sql = "UPDATE users SET is_verified = 1, verification_token = NULL 
                WHERE verification_token = ? AND is_verified = 0";
        
        $stmt = $this->db->query($sql, [$token]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Login user
     */
    public function login($identifier, $password) {
        // Identifier can be email or username
        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $user = $this->db->fetchOne($sql, [$identifier, $identifier]);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid credentials'];
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid credentials'];
        }
        
        if (!$user['is_verified']) {
            return ['success' => false, 'error' => 'Please verify your email first'];
        }
        
        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Regenerate session ID after login
        session_regenerate_id(true);
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $sql = "SELECT id, username, email, description, friendship_link, role, is_verified, created_at 
                FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get user by username
     */
    public function getUserByUsername($username) {
        $sql = "SELECT id, username, email, description, friendship_link, role, is_verified, created_at 
                FROM users WHERE username = ?";
        return $this->db->fetchOne($sql, [$username]);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $sql = "UPDATE users SET username = ?, description = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        try {
            $this->db->query($sql, [$data['username'], $data['description'], $userId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Username already exists'];
        }
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        // Verify current password
        $sql = "SELECT password_hash FROM users WHERE id = ?";
        $user = $this->db->fetchOne($sql, [$userId]);
        
        if (!password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }
        
        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$newHash, $userId]);
        
        return ['success' => true];
    }
    
    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $user = $this->db->fetchOne($sql, [$email]);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Email not found'];
        }
        
        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
        $this->db->query($sql, [$resetToken, $expires, $user['id']]);
        
        return ['success' => true, 'reset_token' => $resetToken, 'user_id' => $user['id']];
    }
    
    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword) {
        $sql = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()";
        $user = $this->db->fetchOne($sql, [$token]);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid or expired reset token'];
        }
        
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL 
                WHERE id = ?";
        $this->db->query($sql, [$newHash, $user['id']]);
        
        return ['success' => true];
    }
    
    /**
     * Get user comments with pagination
     */
    public function getUserComments($userId, $limit = 5, $offset = 0) {
        $sql = "SELECT c.*, d.domain as domain_name 
                FROM comments c 
                LEFT JOIN (SELECT DISTINCT domain FROM comments) d ON c.domain = d.domain 
                WHERE c.user_id = ? 
                ORDER BY c.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
    }
    
    /**
     * Get user comment count
     */
    public function getUserCommentCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM comments WHERE user_id = ?";
        $result = $this->db->fetchOne($sql, [$userId]);
        return $result['count'];
    }
    
    /**
     * Generate unique friendship link
     */
    private function generateFriendshipLink() {
        do {
            $link = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $sql = "SELECT id FROM users WHERE friendship_link = ?";
            $exists = $this->db->fetchOne($sql, [$link]);
        } while ($exists);
        
        return $link;
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        $_SESSION = [];
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
}

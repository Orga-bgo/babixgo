<?php

/**
 * User management class
 */
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $created_at;

    public function __construct($db = null) {
        if ($db === null) {
            $database = Database::getInstance();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $db;
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, created_at) 
                  VALUES (:username, :email, :password, NOW())";

        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        return $stmt->execute();
    }

    public function login($identifier = null, $password = null) {
        if ($identifier !== null) {
            $this->email = $identifier;
        }
        if ($password !== null) {
            $this->password = $password;
        }
        
        $query = "SELECT id, username, email, password_hash, role, is_verified 
                  FROM " . $this->table_name . " 
                  WHERE email = :email OR username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":username", $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['success' => false, 'error' => 'User not found'];
        }

        if (!password_verify($this->password, $row['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid password'];
        }

        $this->id = $row['id'];
        $this->username = $row['username'];
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
        
        return [
            'success' => true,
            'user' => [
                'id' => $row['id'],
                'username' => $row['username'],
                'email' => $row['email'],
                'role' => $row['role']
            ]
        ];
    }

    /**
     * Check if a user is currently logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if the current user is an admin
     * 
     * @return bool True if user is admin, false otherwise
     */
    public static function isAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Get user data by ID
     * 
     * @param int $userId The user ID to retrieve
     * @return array|false User data array or false if not found
     */
    public function getUserById(int $userId): array|false {
        $query = "SELECT id, username, email, role, is_verified, description, friendship_link, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user comment count
     * 
     * @param int $userId The user ID
     * @return int The number of comments
     */
    public function getUserCommentCount(int $userId): int {
        $query = "SELECT COUNT(*) as count 
                  FROM comments 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Get user comments
     * 
     * @param int $userId The user ID
     * @param int $limit Maximum number of comments to retrieve (1-100)
     * @return array Array of comment data
     */
    public function getUserComments(int $userId, int $limit = 10): array {
        // Validate and sanitize limit
        $limit = max(1, min(100, $limit));
        
        $query = "SELECT id, domain, content_id, comment, status, created_at 
                  FROM comments 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

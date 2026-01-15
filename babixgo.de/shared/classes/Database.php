<?php

/**
 * Database connection class (Singleton)
 * Supports MySQL (Strato) and PostgreSQL (Supabase)
 */
class Database {
    private static $instance = null;
    private $conn;
    private $driver;

    private function __construct() {
        $supabaseHost = getenv('SUPABASE_DB_HOST');
        
        if ($supabaseHost) {
            $config = require(__DIR__ . '/../config/database-supabase.php');
            $this->driver = 'pgsql';
            $dsn = "pgsql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'];
        } else {
            $config = require(__DIR__ . '/../config/database.php');
            $this->driver = 'mysql';
            $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['database'] . ";charset=utf8mb4";
        }
        
        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Execute a query with parameters
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage() . " | SQL: " . $sql);
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch a single row
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}

    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

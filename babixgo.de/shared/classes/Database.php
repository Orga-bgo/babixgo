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
        // Load primary configuration (supports both MySQL and PostgreSQL)
        $config = require(__DIR__ . '/../config/database.php');

        // Support legacy SUPABASE_* variables as fallback
        $supabaseHost = getenv('SUPABASE_DB_HOST');
        if ($supabaseHost && $config['host'] === 'localhost') {
            // If SUPABASE_* variables are set and DB_HOST is not, use Supabase config
            $supabaseConfig = require(__DIR__ . '/../config/database-supabase.php');
            $config = array_merge($config, $supabaseConfig);
        }

        // Set driver
        $this->driver = $config['driver'] ?? 'mysql';

        // Build DSN based on driver
        if ($this->driver === 'pgsql') {
            $dsn = "pgsql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'];
        } else {
            $dsn = "mysql:host=" . $config['host'];
            if (!empty($config['port']) && $config['port'] != '3306') {
                $dsn .= ";port=" . $config['port'];
            }
            $dsn .= ";dbname=" . $config['database'] . ";charset=" . $config['charset'];
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

<?php
/**
 * Database connection and helper functions
 * Uses shared Database singleton for both MySQL and PostgreSQL support
 * Backward compatible with old mysqli-style calls
 */

// Track last statement for getAffectedRows()
$_lastStatement = null;

/**
 * Get database connection (uses shared singleton)
 * @return PDO
 */
function getDB(): PDO {
    return Database::getInstance()->getConnection();
}

/**
 * Execute a prepared statement with parameters
 * Backward compatible: accepts old mysqli signature (sql, types, params) or new PDO signature (sql, params)
 * @param string $sql SQL query with placeholders
 * @param mixed $typesOrParams Type string (ignored) or params array
 * @param array|null $params Parameters to bind
 * @return PDOStatement|false
 */
function executeQuery(string $sql, $typesOrParams = [], ?array $params = null) {
    global $_lastStatement;
    
    // Handle backward compatibility with mysqli-style calls
    if (is_string($typesOrParams)) {
        $params = $params ?? [];
    } else {
        $params = $typesOrParams;
    }
    
    $db = getDB();
    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            throw new Exception('Query preparation failed');
        }
        return false;
    }
    
    $stmt->execute($params);
    $_lastStatement = $stmt;
    return $stmt;
}

/**
 * Get single row from query result
 * Backward compatible: accepts old mysqli signature (sql, types, params) or new PDO signature (sql, params)
 */
function fetchOne(string $sql, $typesOrParams = [], ?array $params = null): ?array {
    if (is_string($typesOrParams)) {
        $params = $params ?? [];
    } else {
        $params = $typesOrParams;
    }
    
    $stmt = executeQuery($sql, $params);
    
    if ($stmt instanceof PDOStatement) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    
    return null;
}

/**
 * Get all rows from query result
 * Backward compatible: accepts old mysqli signature (sql, types, params) or new PDO signature (sql, params)
 */
function fetchAll(string $sql, $typesOrParams = [], ?array $params = null): array {
    if (is_string($typesOrParams)) {
        $params = $params ?? [];
    } else {
        $params = $typesOrParams;
    }
    
    $stmt = executeQuery($sql, $params);
    
    if ($stmt instanceof PDOStatement) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return [];
}

/**
 * Insert a row and return the insert ID
 * Backward compatible: accepts old mysqli signature (sql, types, params) or new PDO signature (sql, params)
 */
function insertRow(string $sql, $typesOrParams = [], ?array $params = null) {
    if (is_string($typesOrParams)) {
        $params = $params ?? [];
    } else {
        $params = $typesOrParams;
    }
    
    $db = getDB();
    $stmt = executeQuery($sql, $params);
    
    if ($stmt) {
        return (int) $db->lastInsertId();
    }
    
    return false;
}

/**
 * Get the number of affected rows from last query
 * @return int
 */
function getAffectedRows(): int {
    global $_lastStatement;
    return $_lastStatement ? $_lastStatement->rowCount() : 0;
}

/**
 * Close database connection (no-op for PDO singleton)
 */
function closeDB(): void {
    // PDO handles connection cleanup automatically
}

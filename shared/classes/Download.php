<?php
/**
 * Download Class
 * Handles download management and logging
 */

class Download {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Add new download
     */
    public function add($data) {
        $sql = "INSERT INTO downloads (filename, filepath, filetype, filesize, version, description) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $this->db->query($sql, [
                $data['filename'],
                $data['filepath'],
                $data['filetype'],
                $data['filesize'],
                $data['version'],
                $data['description']
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to add download'];
        }
    }
    
    /**
     * Get download by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM downloads WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get all downloads with filters
     */
    public function getAll($filetype = null, $activeOnly = true, $limit = 100, $offset = 0) {
        $sql = "SELECT * FROM downloads WHERE 1=1";
        $params = [];
        
        if ($filetype) {
            $sql .= " AND filetype = ?";
            $params[] = $filetype;
        }
        
        if ($activeOnly) {
            $sql .= " AND active = 1";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update download
     */
    public function update($id, $data) {
        $sql = "UPDATE downloads SET 
                filename = ?, 
                filetype = ?, 
                filesize = ?, 
                version = ?, 
                description = ?,
                active = ?,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        try {
            $this->db->query($sql, [
                $data['filename'],
                $data['filetype'],
                $data['filesize'],
                $data['version'],
                $data['description'],
                $data['active'] ?? 1,
                $id
            ]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to update download'];
        }
    }
    
    /**
     * Delete download
     */
    public function delete($id) {
        $sql = "DELETE FROM downloads WHERE id = ?";
        $this->db->query($sql, [$id]);
        return ['success' => true];
    }
    
    /**
     * Increment download count
     */
    public function incrementCount($id) {
        $sql = "UPDATE downloads SET download_count = download_count + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }
    
    /**
     * Log download
     */
    public function logDownload($fileId, $userId = null) {
        $sql = "INSERT INTO download_logs (file_id, user_id, ip_address, user_agent) 
                VALUES (?, ?, ?, ?)";
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $this->db->query($sql, [$fileId, $userId, $ipAddress, $userAgent]);
        $this->incrementCount($fileId);
    }
    
    /**
     * Get download logs for a file
     */
    public function getLogs($fileId, $limit = 100, $offset = 0) {
        $sql = "SELECT dl.*, u.username 
                FROM download_logs dl 
                LEFT JOIN users u ON dl.user_id = u.id 
                WHERE dl.file_id = ? 
                ORDER BY dl.downloaded_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$fileId, $limit, $offset]);
    }
    
    /**
     * Get total downloads count
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM downloads";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }
}

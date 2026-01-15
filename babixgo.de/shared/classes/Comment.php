<?php
/**
 * Comment Class
 * Handles comment management and moderation
 */

class Comment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Add new comment
     */
    public function add($userId, $domain, $contentId, $comment) {
        $sql = "INSERT INTO comments (user_id, domain, content_id, comment) 
                VALUES (?, ?, ?, ?)";
        
        try {
            $this->db->query($sql, [$userId, $domain, $contentId, $comment]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to add comment'];
        }
    }
    
    /**
     * Get comment by ID
     */
    public function getById($id) {
        $sql = "SELECT c.*, u.username FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get all comments with filters
     */
    public function getAll($status = null, $domain = null, $limit = 50, $offset = 0) {
        $sql = "SELECT c.*, u.username FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND c.status = ?";
            $params[] = $status;
        }
        
        if ($domain) {
            $sql .= " AND c.domain = ?";
            $params[] = $domain;
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update comment status
     */
    public function updateStatus($id, $status) {
        $validStatuses = ['approved', 'pending', 'spam'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'error' => 'Invalid status'];
        }
        
        $sql = "UPDATE comments SET status = ? WHERE id = ?";
        $this->db->query($sql, [$status, $id]);
        return ['success' => true];
    }
    
    /**
     * Delete comment
     */
    public function delete($id) {
        $sql = "DELETE FROM comments WHERE id = ?";
        $this->db->query($sql, [$id]);
        return ['success' => true];
    }
    
    /**
     * Get comments by user
     */
    public function getByUser($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM comments WHERE user_id = ? 
                ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
    }
    
    /**
     * Get comment counts by status
     */
    public function getCountsByStatus() {
        $sql = "SELECT status, COUNT(*) as count FROM comments GROUP BY status";
        $results = $this->db->fetchAll($sql);
        
        $counts = ['pending' => 0, 'approved' => 0, 'spam' => 0];
        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Get total comment count
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM comments";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }
}

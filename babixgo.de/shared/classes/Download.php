<?php

/**
 * Download management class
 */
class Download {
    private $conn;
    private $table_name = "downloads";
    private $download_path;

    public $id;
    public $filename;
    public $filepath;
    public $filetype;
    public $filesize;
    public $downloads_count;

    public function __construct($db = null) {
        $this->conn = $db ?? Database::getInstance()->getConnection();
        $this->download_path = __DIR__ . '/../../downloads/';
    }

    public function getAll($filterType = null, $activeOnly = false, $limit = null, $offset = null) {
        $query = "SELECT * FROM " . $this->table_name;
        $params = [];
        $conditions = [];
        
        if ($filterType) {
            $conditions[] = "filetype = ?";
            $params[] = $filterType;
        }
        
        if ($activeOnly) {
            $conditions[] = "active = TRUE";
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY id DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $query .= " OFFSET " . intval($offset);
            }
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementDownloadCount($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET download_count = download_count + 1 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function add($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (filename, filepath, filetype, filesize, version, description, active)
                  VALUES (:filename, :filepath, :filetype, :filesize, :version, :description, TRUE)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':filename', $data['filename']);
            $stmt->bindParam(':filepath', $data['filepath']);
            $stmt->bindParam(':filetype', $data['filetype']);
            $stmt->bindParam(':filesize', $data['filesize']);
            $stmt->bindParam(':version', $data['version']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->execute();
            
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        $download = $this->getById($id);
        if (!$download) {
            return ['success' => false, 'error' => 'Download not found'];
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $filepath = $this->download_path . $download['filepath'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            return ['success' => true];
        }
        
        return ['success' => false, 'error' => 'Failed to delete'];
    }

    public function serveFile($filepath) {
        $fullpath = $this->download_path . $filepath;
        
        if (!file_exists($fullpath)) {
            return false;
        }

        $filename = basename($fullpath);
        $filename = str_replace('"', '', $filename); // Remove quotes to prevent header injection
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($fullpath));
        readfile($fullpath);
        return true;
    }
}

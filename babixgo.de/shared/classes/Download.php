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
        // Updated to use file-storage path to match upload location
        $this->download_path = dirname(__DIR__, 2) . '/file-storage/';
    }

    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY sort_order ASC, name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                  (name, filename, filepath, filetype, filesize, version, description, category_id, active)
                  VALUES (:name, :filename, :filepath, :filetype, :filesize, :version, :description, :category_id, TRUE)";

        try {
            $stmt = $this->conn->prepare($query);
            // Use filename as display name if no name provided
            $name = $data['name'] ?? $data['filename'];
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':filename', $data['filename']);
            $stmt->bindParam(':filepath', $data['filepath']);
            $stmt->bindParam(':filetype', $data['filetype']);
            $stmt->bindParam(':filesize', $data['filesize']);
            $stmt->bindParam(':version', $data['version']);
            $stmt->bindParam(':description', $data['description']);
            $categoryId = $data['category_id'] ?? null;
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();

            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Download add error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name,
                      filename = :filename,
                      filetype = :filetype,
                      filesize = :filesize,
                      version = :version,
                      description = :description,
                      category_id = :category_id,
                      active = :active
                  WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            // Use filename as display name if no name provided
            $name = $data['name'] ?? $data['filename'];
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':filename', $data['filename']);
            $stmt->bindParam(':filetype', $data['filetype']);
            $stmt->bindParam(':filesize', $data['filesize']);
            $stmt->bindParam(':version', $data['version']);
            $stmt->bindParam(':description', $data['description']);
            $categoryId = $data['category_id'] ?? null;
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':active', $data['active']);
            $stmt->execute();

            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Download update error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        $download = $this->getById($id);
        if (!$download) {
            return ['success' => false, 'error' => 'Download not found'];
        }

        // Delete file first
        $filepath = $this->download_path . $download['filepath'];
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                error_log("Failed to delete file: " . $filepath);
            }
        } else {
            error_log("File not found for deletion: " . $filepath);
        }

        // Delete database record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Failed to delete'];
    }

    public function getLogs($downloadId, $limit = 20) {
        $query = "SELECT dl.*, u.username
                  FROM download_logs dl
                  LEFT JOIN users u ON dl.user_id = u.id
                  WHERE dl.file_id = :download_id
                  ORDER BY dl.downloaded_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':download_id', $downloadId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function serveFile($filepath) {
        // filepath is stored as "filetype/filename.ext" in database
        $fullpath = $this->download_path . $filepath;

        if (!file_exists($fullpath)) {
            error_log("Download file not found: " . $fullpath);
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

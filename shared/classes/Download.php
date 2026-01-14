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

    public function __construct($db) {
        $this->conn = $db;
        $this->download_path = __DIR__ . '/../../downloads/';
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
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
                  SET downloads_count = downloads_count + 1 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
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

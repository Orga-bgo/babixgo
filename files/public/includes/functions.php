<?php
/**
 * General helper functions
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Escape HTML to prevent XSS
 * @param string|null $string
 * @return string
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format file size to human readable format
 * @param int $bytes
 * @return string
 */
function formatFileSize(int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Format date to German format
 * @param string $date
 * @return string
 */
function formatDate(string $date): string {
    return date('d.m.Y H:i', strtotime($date));
}

/**
 * Format date to relative time (e.g., "vor 2 Stunden")
 * @param string $date
 * @return string
 */
function formatRelativeTime(string $date): string {
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'gerade eben';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'vor ' . $minutes . ' Minute' . ($minutes > 1 ? 'n' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'vor ' . $hours . ' Stunde' . ($hours > 1 ? 'n' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return 'vor ' . $days . ' Tag' . ($days > 1 ? 'en' : '');
    } else {
        return formatDate($date);
    }
}

/**
 * Get all downloads with optional filtering
 * @param int|null $limit
 * @param int $offset
 * @param string $orderBy
 * @return array
 */
function getDownloads(?int $limit = null, int $offset = 0, string $orderBy = 'created_at DESC'): array {
    // Whitelist order by to prevent SQL injection
    $allowedOrderBy = [
        'created_at DESC', 'created_at ASC',
        'download_count DESC', 'download_count ASC',
        'name ASC', 'name DESC'
    ];
    
    if (!in_array($orderBy, $allowedOrderBy)) {
        $orderBy = 'created_at DESC';
    }
    
    $sql = "SELECT d.*, u.username as creator_name,
            (SELECT COUNT(*) FROM comments c WHERE c.download_id = d.id) as comment_count
            FROM downloads d 
            LEFT JOIN users u ON d.created_by = u.id
            ORDER BY {$orderBy}";
    
    if ($limit !== null) {
        $sql .= " LIMIT ? OFFSET ?";
        return fetchAll($sql, 'ii', [$limit, $offset]);
    }
    
    return fetchAll($sql);
}

/**
 * Get single download by ID
 * @param int $id
 * @return array|null
 */
function getDownloadById(int $id): ?array {
    return fetchOne(
        "SELECT d.*, u.username as creator_name,
         (SELECT COUNT(*) FROM comments c WHERE c.download_id = d.id) as comment_count
         FROM downloads d 
         LEFT JOIN users u ON d.created_by = u.id
         WHERE d.id = ?",
        'i',
        [$id]
    );
}

/**
 * Increment download counter
 * @param int $id
 * @return bool
 */
function incrementDownloadCount(int $id): bool {
    executeQuery(
        "UPDATE downloads SET download_count = download_count + 1 WHERE id = ?",
        'i',
        [$id]
    );
    return getAffectedRows() > 0;
}

/**
 * Get comments for a download
 * @param int $downloadId
 * @return array
 */
function getComments(int $downloadId): array {
    return fetchAll(
        "SELECT c.*, u.username 
         FROM comments c 
         JOIN users u ON c.user_id = u.id 
         WHERE c.download_id = ? 
         ORDER BY c.created_at DESC",
        'i',
        [$downloadId]
    );
}

/**
 * Add a comment to a download
 * @param int $downloadId
 * @param int $userId
 * @param string $commentText
 * @return int|false Comment ID or false on failure
 */
function addComment(int $downloadId, int $userId, string $commentText) {
    $commentText = trim($commentText);
    
    if (empty($commentText)) {
        return false;
    }
    
    $commentId = insertRow(
        "INSERT INTO comments (download_id, user_id, comment_text) VALUES (?, ?, ?)",
        'iis',
        [$downloadId, $userId, $commentText]
    );
    
    if ($commentId) {
        // Update user's comment count
        executeQuery(
            "UPDATE users SET comment_count = comment_count + 1 WHERE id = ?",
            'i',
            [$userId]
        );
    }
    
    return $commentId;
}

/**
 * Delete a comment
 * @param int $commentId
 * @param int|null $userId Optional: only delete if user owns the comment
 * @return bool
 */
function deleteComment(int $commentId, ?int $userId = null): bool {
    // Get comment to check ownership and update counts
    $comment = fetchOne("SELECT user_id FROM comments WHERE id = ?", 'i', [$commentId]);
    
    if (!$comment) {
        return false;
    }
    
    // If userId is provided, check ownership
    if ($userId !== null && $comment['user_id'] !== $userId) {
        return false;
    }
    
    executeQuery("DELETE FROM comments WHERE id = ?", 'i', [$commentId]);
    
    if (getAffectedRows() > 0) {
        // Decrease user's comment count
        executeQuery(
            "UPDATE users SET comment_count = GREATEST(comment_count - 1, 0) WHERE id = ?",
            'i',
            [$comment['user_id']]
        );
        return true;
    }
    
    return false;
}

/**
 * Create a new download entry
 * @param array $data
 * @return int|false Download ID or false on failure
 */
function createDownload(array $data) {
    return insertRow(
        "INSERT INTO downloads (name, description, file_size, file_type, download_link, alternative_link, created_by) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        'ssssssi',
        [
            $data['name'],
            $data['description'] ?? '',
            $data['file_size'] ?? '',
            $data['file_type'] ?? '',
            $data['download_link'] ?? '',
            $data['alternative_link'] ?? '',
            $data['created_by'] ?? null
        ]
    );
}

/**
 * Update a download entry
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateDownload(int $id, array $data): bool {
    executeQuery(
        "UPDATE downloads SET name = ?, description = ?, file_size = ?, file_type = ?, 
         download_link = ?, alternative_link = ? WHERE id = ?",
        'ssssssi',
        [
            $data['name'],
            $data['description'] ?? '',
            $data['file_size'] ?? '',
            $data['file_type'] ?? '',
            $data['download_link'] ?? '',
            $data['alternative_link'] ?? '',
            $id
        ]
    );
    return getAffectedRows() > 0;
}

/**
 * Delete a download entry
 * @param int $id
 * @return bool
 */
function deleteDownload(int $id): bool {
    executeQuery("DELETE FROM downloads WHERE id = ?", 'i', [$id]);
    return getAffectedRows() > 0;
}

/**
 * Get all users (admin function)
 * @return array
 */
function getAllUsers(): array {
    return fetchAll(
        "SELECT id, username, email, description, role, comment_count, email_verified, created_at 
         FROM users ORDER BY created_at DESC"
    );
}

/**
 * Get user by ID
 * @param int $id
 * @return array|null
 */
function getUserById(int $id): ?array {
    return fetchOne(
        "SELECT id, username, email, description, role, comment_count, email_verified, created_at 
         FROM users WHERE id = ?",
        'i',
        [$id]
    );
}

/**
 * Update user role (admin function)
 * @param int $userId
 * @param string $role
 * @return bool
 */
function updateUserRole(int $userId, string $role): bool {
    if (!in_array($role, ['member', 'admin'])) {
        return false;
    }
    
    executeQuery(
        "UPDATE users SET role = ? WHERE id = ?",
        'si',
        [$role, $userId]
    );
    return getAffectedRows() > 0;
}

/**
 * Delete user (admin function)
 * @param int $userId
 * @return bool
 */
function deleteUser(int $userId): bool {
    executeQuery("DELETE FROM users WHERE id = ?", 'i', [$userId]);
    return getAffectedRows() > 0;
}

/**
 * Update user profile
 * @param int $userId
 * @param array $data
 * @return bool
 */
function updateUserProfile(int $userId, array $data): bool {
    $updates = [];
    $types = '';
    $params = [];
    
    if (isset($data['description'])) {
        $updates[] = 'description = ?';
        $types .= 's';
        $params[] = $data['description'];
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $types .= 'i';
    $params[] = $userId;
    
    executeQuery(
        "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?",
        $types,
        $params
    );
    
    return getAffectedRows() > 0;
}

/**
 * Get total counts for dashboard
 * @return array
 */
function getDashboardStats(): array {
    $downloads = fetchOne("SELECT COUNT(*) as count, SUM(download_count) as total_downloads FROM downloads");
    $users = fetchOne("SELECT COUNT(*) as count FROM users");
    $comments = fetchOne("SELECT COUNT(*) as count FROM comments");
    
    return [
        'downloads' => $downloads['count'] ?? 0,
        'total_downloads' => $downloads['total_downloads'] ?? 0,
        'users' => $users['count'] ?? 0,
        'comments' => $comments['count'] ?? 0
    ];
}

/**
 * Get all comments (for moderation)
 * @return array
 */
function getAllComments(): array {
    return fetchAll(
        "SELECT c.*, u.username, d.name as download_name 
         FROM comments c 
         JOIN users u ON c.user_id = u.id 
         JOIN downloads d ON c.download_id = d.id 
         ORDER BY c.created_at DESC"
    );
}

/**
 * Generate a safe filename
 * @param string $filename
 * @return string
 */
function sanitizeFilename(string $filename): string {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    return trim($filename, '_');
}

/**
 * Get file icon based on file type
 * @param string $fileType
 * @return string
 */
function getFileIcon(string $fileType): string {
    $icons = [
        'apk' => 'android',
        'zip' => 'folder_zip',
        'pdf' => 'picture_as_pdf',
        'exe' => 'window',
        'dmg' => 'computer',
        'tar' => 'folder_zip',
        'gz' => 'folder_zip',
        '7z' => 'folder_zip',
        'rar' => 'folder_zip'
    ];
    
    return $icons[strtolower($fileType)] ?? 'description';
}

/**
 * Get all categories with download count
 * @return array
 */
function getCategories(): array {
    return fetchAll(
        "SELECT c.*, 
        COUNT(d.id) as download_count
        FROM categories c
        LEFT JOIN downloads d ON d.category_id = c.id
        GROUP BY c.id
        ORDER BY c.sort_order ASC"
    );
}

/**
 * Get single category by slug
 * @param string $slug
 * @return array|null
 */
function getCategoryBySlug(string $slug): ?array {
    return fetchOne(
        "SELECT * FROM categories WHERE slug = ?",
        's',
        [$slug]
    );
}

/**
 * Get downloads by category
 * @param int $categoryId
 * @return array
 */
function getDownloadsByCategory(int $categoryId): array {
    return fetchAll(
        "SELECT d.*, u.username as creator_name,
        (SELECT COUNT(*) FROM comments c WHERE c.download_id = d.id) as comment_count
        FROM downloads d
        LEFT JOIN users u ON d.created_by = u.id
        WHERE d.category_id = ?
        ORDER BY d.created_at DESC",
        'i',
        [$categoryId]
    );
}

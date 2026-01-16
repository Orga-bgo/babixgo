# Database Migration Guide

## For Existing Databases

If you already have a database set up, you need to add the missing tables and columns. Use the appropriate migration script below based on your database type.

## MySQL/MariaDB Migration

```sql
-- 1. Add missing columns to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS comment_count INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT 0;

-- 2. Create categories table (if not exists)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(500) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add missing columns to downloads table
ALTER TABLE downloads
ADD COLUMN IF NOT EXISTS name VARCHAR(255) NULL AFTER id,
ADD COLUMN IF NOT EXISTS file_size VARCHAR(100) NULL AFTER filesize,
ADD COLUMN IF NOT EXISTS file_type VARCHAR(100) NULL AFTER file_size,
ADD COLUMN IF NOT EXISTS download_link VARCHAR(500) NULL AFTER description,
ADD COLUMN IF NOT EXISTS alternative_link VARCHAR(500) NULL AFTER download_link,
ADD COLUMN IF NOT EXISTS category_id INT NULL AFTER download_count,
ADD COLUMN IF NOT EXISTS created_by INT NULL AFTER category_id;

-- 4. Add foreign keys to downloads table
ALTER TABLE downloads
ADD CONSTRAINT fk_downloads_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_downloads_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- 5. Add indexes to downloads table
ALTER TABLE downloads
ADD INDEX IF NOT EXISTS idx_category_id (category_id),
ADD INDEX IF NOT EXISTS idx_created_by (created_by);

-- 6. Add missing columns to comments table
ALTER TABLE comments
MODIFY COLUMN domain VARCHAR(50) NULL,
MODIFY COLUMN content_id INT NULL,
ADD COLUMN IF NOT EXISTS download_id INT NULL AFTER content_id,
ADD COLUMN IF NOT EXISTS comment_text TEXT NULL AFTER comment;

-- 7. Add foreign key and index to comments table
ALTER TABLE comments
ADD CONSTRAINT fk_comments_download FOREIGN KEY (download_id) REFERENCES downloads(id) ON DELETE CASCADE,
ADD INDEX IF NOT EXISTS idx_download_id (download_id);

-- 8. Insert sample categories
INSERT INTO categories (name, slug, description, icon, sort_order) VALUES
('Android Apps', 'android-apps', 'Android APK Downloads für BabixGO', '📱', 1),
('Windows Tools', 'windows-tools', 'Windows EXE Programme und Tools', '💻', 2),
('Scripts', 'scripts', 'Nützliche Scripts und Automatisierungen', '📜', 3)
ON DUPLICATE KEY UPDATE name=name;
```

## PostgreSQL Migration

```sql
-- 1. Add missing columns to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS comment_count INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT FALSE;

-- 2. Create categories table (if not exists)
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(500) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories(slug);
CREATE INDEX IF NOT EXISTS idx_categories_sort_order ON categories(sort_order);

-- 3. Add missing columns to downloads table
ALTER TABLE downloads
ADD COLUMN IF NOT EXISTS name VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS file_size VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS file_type VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS download_link VARCHAR(500) NULL,
ADD COLUMN IF NOT EXISTS alternative_link VARCHAR(500) NULL,
ADD COLUMN IF NOT EXISTS category_id INT NULL,
ADD COLUMN IF NOT EXISTS created_by INT NULL;

-- 4. Add foreign keys to downloads table
ALTER TABLE downloads
ADD CONSTRAINT fk_downloads_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_downloads_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- 5. Add indexes to downloads table
CREATE INDEX IF NOT EXISTS idx_downloads_category_id ON downloads(category_id);
CREATE INDEX IF NOT EXISTS idx_downloads_created_by ON downloads(created_by);

-- 6. Modify comments table to make domain and content_id nullable
ALTER TABLE comments
ALTER COLUMN domain DROP NOT NULL,
ALTER COLUMN content_id DROP NOT NULL;

-- 7. Add missing columns to comments table
ALTER TABLE comments
ADD COLUMN IF NOT EXISTS download_id INT NULL,
ADD COLUMN IF NOT EXISTS comment_text TEXT NULL;

-- 8. Add foreign key and index to comments table
ALTER TABLE comments
ADD CONSTRAINT fk_comments_download FOREIGN KEY (download_id) REFERENCES downloads(id) ON DELETE CASCADE;

CREATE INDEX IF NOT EXISTS idx_comments_download_id ON comments(download_id);

-- 9. Insert sample categories
INSERT INTO categories (name, slug, description, icon, sort_order) VALUES
('Android Apps', 'android-apps', 'Android APK Downloads für BabixGO', '📱', 1),
('Windows Tools', 'windows-tools', 'Windows EXE Programme und Tools', '💻', 2),
('Scripts', 'scripts', 'Nützliche Scripts und Automatisierungen', '📜', 3)
ON CONFLICT (slug) DO NOTHING;
```

## Verification

After running the migration, verify that all tables and columns exist:

### MySQL/MariaDB
```sql
-- Check users table
DESCRIBE users;

-- Check categories table
DESCRIBE categories;

-- Check downloads table
DESCRIBE downloads;

-- Check comments table
DESCRIBE comments;

-- Verify sample categories were inserted
SELECT * FROM categories;
```

### PostgreSQL
```sql
-- Check users table
\d users

-- Check categories table
\d categories

-- Check downloads table
\d downloads

-- Check comments table
\d comments

-- Verify sample categories were inserted
SELECT * FROM categories;
```

## Notes

- The migration scripts use `IF NOT EXISTS` and `ADD COLUMN IF NOT EXISTS` to safely run on databases that may already have some of these changes.
- Existing data will not be affected by these changes.
- For MySQL, the `ADD COLUMN IF NOT EXISTS` syntax requires MySQL 8.0.12+. For older versions, you may need to check if columns exist before adding them.
- All foreign keys are set to `ON DELETE SET NULL` or `ON DELETE CASCADE` appropriately to maintain referential integrity.

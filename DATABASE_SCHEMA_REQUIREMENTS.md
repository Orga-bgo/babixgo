# Database Schema Requirements for Downloads and Categories

## Problem Analysis

Based on code analysis of `/babixgo.de/files/` and `/babixgo.de/admin/`, the following database schema is REQUIRED but currently MISSING or INCOMPLETE.

## 1. CATEGORIES Table (COMPLETELY MISSING)

The `categories` table is referenced in the code but does NOT exist in the current schema files.

### Required Fields (from code analysis):

```sql
CREATE TABLE categories (
    id INT/SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(500),  -- Path to category icon
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Code References:
- `/babixgo.de/files/includes/functions.php:427` - `SELECT c.*, COUNT(d.id) as download_count FROM categories c`
- `/babixgo.de/files/includes/functions.php:441` - `SELECT * FROM categories WHERE slug = ?`
- `/babixgo.de/files/index.php:12` - `$categories = getCategories();`
- `/babixgo.de/files/category.php:27` - `$category = getCategoryBySlug($slug);`

### Fields Used in Code:
- `id` - Primary key, referenced in downloads table
- `name` - Display name (shown in UI)
- `slug` - URL-friendly identifier (e.g., "/kategorie/scripts")
- `description` - Category description text
- `icon` - Icon path or emoji
- `sort_order` - For ordering categories in display
- `download_count` - Calculated field (COUNT from downloads)

## 2. DOWNLOADS Table - Missing Columns

The current schema has these fields:
```
✓ id
✓ filename
✓ filepath
✓ filetype
✓ filesize
✓ version
✓ description
✓ download_count
✓ active
✓ created_at
✓ updated_at
```

But the code REQUIRES these additional fields:

### MISSING Fields:

```sql
-- Add to downloads table:
name VARCHAR(255) NOT NULL,              -- Display name (different from filename)
file_size VARCHAR(100),                  -- Human-readable size (e.g., "2.5 MB")
file_type VARCHAR(100),                  -- Display type (e.g., "Android APK")
download_link VARCHAR(500),              -- Primary download URL
alternative_link VARCHAR(500),           -- Alternative download URL
category_id INT,                         -- Foreign key to categories table
created_by INT,                          -- Foreign key to users table
```

### Code References:
- `/babixgo.de/files/includes/functions.php:221` - INSERT with: name, description, file_size, file_type, download_link, alternative_link, created_by
- `/babixgo.de/files/includes/functions.php:89` - `LEFT JOIN users u ON d.created_by = u.id`
- `/babixgo.de/files/includes/functions.php:428` - `LEFT JOIN downloads d ON d.category_id = c.id`
- `/babixgo.de/files/includes/functions.php:458` - `WHERE d.category_id = ?`
- `/babixgo.de/files/category.php:111-120` - Uses: name, file_type, file_size, description, alternative_link

## 3. COMMENTS Table - Missing/Changed Columns

Current schema uses:
```
✓ id
✓ user_id
✓ domain
✓ content_id
✓ comment
✓ status
✓ created_at
```

But files section code REQUIRES:
```sql
-- Add to comments table:
download_id INT,                         -- Direct reference to downloads (instead of domain/content_id)
comment_text TEXT,                       -- Field name used in code (instead of 'comment')
```

### Code References:
- `/babixgo.de/files/includes/functions.php:87` - `WHERE c.download_id = d.id`
- `/babixgo.de/files/includes/functions.php:142` - `WHERE c.download_id = ?`
- `/babixgo.de/files/includes/functions.php:163` - INSERT into `comment_text` field
- `/babixgo.de/files/includes/functions.php:380` - `d.name as download_name`

## 4. USERS Table - Missing Columns

The files section code expects:
```sql
-- Add to users table:
comment_count INT DEFAULT 0,             -- Track user's comment count
email_verified BOOLEAN DEFAULT FALSE,    -- Email verification status
```

### Code References:
- `/babixgo.de/files/includes/functions.php:170` - `UPDATE users SET comment_count = comment_count + 1`
- `/babixgo.de/files/includes/functions.php:276` - SELECT includes `comment_count, email_verified`

## Summary of Required Changes

### For MySQL (create-tables.sql):

1. **CREATE categories table** (completely new)
2. **ALTER downloads table** - ADD:
   - `name` VARCHAR(255) NOT NULL
   - `file_size` VARCHAR(100)
   - `file_type` VARCHAR(100)
   - `download_link` VARCHAR(500)
   - `alternative_link` VARCHAR(500)
   - `category_id` INT (with foreign key to categories)
   - `created_by` INT (with foreign key to users)

3. **ALTER comments table** - ADD:
   - `download_id` INT (with foreign key to downloads)
   - `comment_text` TEXT

4. **ALTER users table** - ADD:
   - `comment_count` INT DEFAULT 0
   - `email_verified` BOOLEAN DEFAULT 0

### For PostgreSQL (create-tables-postgres.sql):

Same changes as MySQL, with PostgreSQL-compatible syntax.

## Notes

- The current downloads table structure in the schema is used by `/admin/downloads.php` and `/shared/classes/Download.php`
- The files section (`/babixgo.de/files/`) uses a DIFFERENT set of fields
- There's a schema mismatch between the two sections that needs to be reconciled
- Both field sets should be supported to avoid breaking existing functionality

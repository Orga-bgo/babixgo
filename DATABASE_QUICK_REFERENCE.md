# Database Quick Reference

## Table Overview

```
┌─────────────┐
│   users     │
├─────────────┤
│ id          │◄────┐
│ username    │     │
│ email       │     │ created_by
│ ...         │     │
│ comment_count│    │
│ email_verified│   │
└─────────────┘     │
                    │
                    │
┌─────────────┐     │    ┌──────────────┐
│ categories  │     │    │  downloads   │
├─────────────┤     │    ├──────────────┤
│ id          │◄────┼────│ id           │
│ name        │     │    │ name         │ ← Display name
│ slug        │     │    │ filename     │ ← Actual filename
│ description │     │    │ filepath     │
│ icon        │     │    │ filetype     │ ← Enum: apk/scripts/exe
│ sort_order  │     │    │ filesize     │ ← Bytes
└─────────────┘     │    │ file_size    │ ← Human readable
         ▲          │    │ file_type    │ ← Display type
         │          │    │ version      │
    category_id     │    │ description  │
         │          │    │ download_link│
         │          └────│ alternative_link
         │               │ download_count
         │               │ category_id  │
         │               │ created_by   │───┘
         │               │ active       │
         │               │ created_at   │
         │               │ updated_at   │
         │               └──────────────┘
         │                      ▲
         │                      │ download_id
         │                      │
         │               ┌──────────────┐
         │               │  comments    │
         │               ├──────────────┤
         │               │ id           │
         │               │ user_id      │───────┐
         │               │ domain       │       │
         │               │ content_id   │       │
         │               │ download_id  │       │
         │               │ comment      │ ◄─┐   │
         │               │ comment_text │   │   │
         │               │ status       │   │   │
         │               │ created_at   │   │   │
         │               └──────────────┘   │   │
         │                                  │   │
         └──────────────────────────────────┘   │
                                                │
                Both fields for                 │
                backwards compatibility ────────┘
```

## Field Usage by Section

### Admin Section (`/admin/downloads.php`)
```
downloads:
  ✓ filename, filepath, filetype, filesize
  ✓ version, description
  ✓ active, download_count
  ✓ created_at, updated_at

comments:
  ✓ comment (uses this field)
  ✓ status
```

### Files Section (`/babixgo.de/files/`)
```
downloads:
  ✓ name, description
  ✓ file_size, file_type
  ✓ download_link, alternative_link
  ✓ category_id, created_by
  ✓ download_count

categories:
  ✓ name, slug, description
  ✓ icon, sort_order

comments:
  ✓ download_id
  ✓ comment_text (uses this field)
```

## Sample INSERT Statements

### Categories
```sql
INSERT INTO categories (name, slug, description, icon, sort_order) VALUES
('Android Apps', 'android-apps', 'Android APK Downloads', '📱', 1),
('Windows Tools', 'windows-tools', 'Windows Programs', '💻', 2),
('Scripts', 'scripts', 'Useful Scripts', '📜', 3);
```

### Downloads (Full Example)
```sql
INSERT INTO downloads (
  name, filename, filepath, filetype, filesize,
  file_size, file_type, version, description,
  download_link, alternative_link,
  category_id, created_by, active
) VALUES (
  'BabixGO App',                    -- name (display)
  'babixgo-v1.0.0.apk',            -- filename
  'apk/babixgo-v1.0.0_123456.apk', -- filepath
  'apk',                            -- filetype
  15728640,                         -- filesize (bytes)
  '15 MB',                          -- file_size (readable)
  'Android APK',                    -- file_type
  '1.0.0',                          -- version
  'Official BabixGO Android App',   -- description
  '/download.php?id=1',             -- download_link
  'https://backup.example.com/app', -- alternative_link
  1,                                -- category_id (Android Apps)
  1,                                -- created_by (admin user)
  1                                 -- active
);
```

### Comments (Both Compatibility Methods)
```sql
-- Method 1: Populate both fields
INSERT INTO comments (user_id, download_id, comment, comment_text, status)
VALUES (1, 1, 'Great app!', 'Great app!', 'approved');

-- Method 2: Use trigger to sync (advanced)
-- Create trigger to auto-sync comment <-> comment_text
```

## Foreign Key Constraints

```
downloads.category_id  → categories.id  (ON DELETE SET NULL)
downloads.created_by   → users.id       (ON DELETE SET NULL)
comments.user_id       → users.id       (ON DELETE CASCADE)
comments.download_id   → downloads.id   (ON DELETE CASCADE)
```

## Indexes

### Categories
- `idx_slug` on `slug`
- `idx_sort_order` on `sort_order`

### Downloads
- `idx_filetype` on `filetype`
- `idx_category_id` on `category_id`
- `idx_created_by` on `created_by`
- `idx_active` on `active`

### Comments
- `idx_user_id` on `user_id`
- `idx_domain_content` on `(domain, content_id)`
- `idx_download_id` on `download_id`
- `idx_status` on `status`

## Common Queries

### Get all downloads in a category
```sql
SELECT d.*, c.name as category_name
FROM downloads d
LEFT JOIN categories c ON d.category_id = c.id
WHERE c.slug = 'android-apps'
AND d.active = 1
ORDER BY d.created_at DESC;
```

### Get download with comment count
```sql
SELECT d.*, 
  COUNT(cm.id) as comment_count,
  u.username as creator_name
FROM downloads d
LEFT JOIN comments cm ON cm.download_id = d.id
LEFT JOIN users u ON d.created_by = u.id
WHERE d.id = 1
GROUP BY d.id;
```

### Get all categories with download counts
```sql
SELECT c.*, COUNT(d.id) as download_count
FROM categories c
LEFT JOIN downloads d ON d.category_id = c.id AND d.active = 1
GROUP BY c.id
ORDER BY c.sort_order ASC;
```

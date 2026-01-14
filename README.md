# babixgo.de Authentication & Admin System

Complete authentication and administration system for babixgo.de multi-domain setup.

## Features

### Authentication System
- ✅ User registration with email verification
- ✅ Secure login with "remember me" functionality
- ✅ Password reset via email
- ✅ Email verification system
- ✅ Profile management
- ✅ Friendship link sharing system

### Admin Panel
- ✅ User management (view, edit, delete, verify)
- ✅ Download management with file uploads
- ✅ Comment moderation system
- ✅ Statistics dashboard
- ✅ Activity logs
- ✅ Bulk actions

### Security Features
- ✅ Password hashing with bcrypt
- ✅ CSRF protection on all forms
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (output escaping)
- ✅ Session security (httponly, secure, samesite)
- ✅ Input validation (server-side and client-side)
- ✅ Secure file upload handling

## Technology Stack

- **Backend**: Pure PHP 7.4+ (no frameworks)
- **Database**: MySQL/MariaDB with PDO
- **Frontend**: HTML5, CSS3, JavaScript (no frameworks)
- **Deployment**: FTP-deployable to Strato hosting

## Directory Structure

```
babixgo/                             # Monorepo root
├── shared/                          # Shared resources across ALL domains
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css            # Global styles (v1.0.15)
│   │   │   ├── style.css           # Additional styles
│   │   │   └── admin.css           # Admin panel styles
│   │   ├── js/
│   │   │   └── main.js             # Global JavaScript
│   │   ├── icons/                  # SVG icons
│   │   ├── images/                 # Shared images
│   │   └── logo/                   # Logo assets
│   │
│   ├── classes/
│   │   ├── Database.php            # Database wrapper
│   │   ├── User.php                # User management
│   │   ├── Session.php             # Session handling
│   │   ├── Download.php            # Download management
│   │   └── Comment.php             # Comment management
│   │
│   ├── config/
│   │   ├── database.php            # Database configuration
│   │   ├── session.php             # Session management
│   │   └── autoload.php            # Class autoloader
│   │
│   ├── partials/                   # Shared PHP partials
│   │   ├── header.php              # Site header
│   │   ├── footer.php              # Site footer
│   │   ├── nav.php                 # Navigation
│   │   ├── head-meta.php           # Meta tags
│   │   ├── head-links.php          # CSS/Font links
│   │   ├── critical-css.php        # Critical CSS
│   │   ├── version.php             # Version constant
│   │   └── [other partials]
│   │
│   └── create-tables.sql           # Database schema
│
├── downloads/                       # Secure file storage (NOT web-accessible)
│   ├── .htaccess                   # Deny direct access
│   ├── apk/                        # Android APK files
│   ├── exe/                        # Windows executables
│   └── scripts/                    # Script files
│       ├── bash/
│       ├── python/
│       └── powershell/
│
├── babixgo.de/                      # Main website (babixgo.de)
│   ├── index.php                   # Homepage
│   ├── about.php                   # About page
│   ├── 404.php                     # Error page
│   ├── .htaccess                   # Web server config
│   │
│   ├── assets/                     # Domain-specific assets
│   │   ├── css/
│   │   │   └── style.css           # Main site styles
│   │   ├── js/
│   │   ├── icons/
│   │   ├── img/
│   │   └── logo/
│   │
│   ├── public/                     # PWA assets
│   │   ├── manifest.json
│   │   ├── sw.js
│   │   └── offline.html
│   │
│   └── [content directories]/      # sticker/, wuerfel/, etc.
│
├── auth/                            # Authentication system (auth.babixgo.de)
│   ├── .htaccess                   # Root config
│   │
│   └── public/                     # Document root for auth.babixgo.de
│       ├── index.php               # User dashboard/profile
│       ├── login.php               # Login page
│       ├── register.php            # Registration page
│       ├── logout.php              # Logout handler
│       ├── verify-email.php        # Email verification
│       ├── forgot-password.php     # Password reset request
│       ├── reset-password.php      # Password reset form
│       ├── edit-profile.php        # Edit profile page
│       ├── .htaccess               # Security configuration
│       ├── manifest.json           # PWA manifest
│       ├── sw.js                   # Service worker
│       ├── offline.html            # Offline fallback
│       │
│       ├── admin/                  # Admin panel
│       │   ├── index.php           # Admin dashboard
│       │   ├── users.php           # User management
│       │   ├── user-edit.php       # Edit user
│       │   ├── downloads.php       # Download management
│       │   ├── download-edit.php   # Edit download
│       │   ├── comments.php        # Comment moderation
│       │   └── .htaccess           # Admin protection
│       │
│       ├── assets/
│       │   ├── css/
│       │   │   ├── auth.css        # Authentication styling
│       │   │   └── admin.css       # Admin panel styling
│       │   └── js/
│       │       ├── form-validation.js
│       │       └── admin.js
│       │
│       └── includes/
│           ├── auth-check.php      # Authentication guard
│           ├── admin-check.php     # Admin authorization
│           ├── mail-helper.php     # Email functions
│           └── form-handlers/
│
└── files.babixgo.de/                # Download portal (files.babixgo.de)
    ├── .htaccess                   # Root config
    │
    └── public/                     # Document root for files.babixgo.de
        ├── index.php               # Download listing
        ├── download.php            # Download handler
        ├── category.php            # Category view
        ├── .htaccess               # Security configuration
        ├── manifest.json           # PWA manifest
        ├── sw.js                   # Service worker
        ├── offline.html            # Offline fallback
        │
        ├── admin/                  # Admin panel
        │   ├── dashboard.php
        │   ├── manage-downloads.php
        │   └── manage-users.php
        │
        ├── assets/                 # Domain-specific assets
        │   ├── css/
        │   │   └── style.css       # Files portal styles
        │   └── js/
        │
        └── includes/               # Domain-specific includes
            ├── config.php
            ├── db.php
            ├── auth.php
            └── functions.php
```

### Domain to Directory Mapping

| Domain | Document Root | Purpose |
|--------|--------------|---------|
| **babixgo.de** | `/babixgo.de/` | Main website |
| **auth.babixgo.de** | `/auth/public/` | Authentication & Admin |
| **files.babixgo.de** | `/files.babixgo.de/public/` | Download portal |

All domains access shared resources via: `dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/'`

## Installation

### 1. Database Setup

1. Create a MySQL/MariaDB database:
```sql
CREATE DATABASE babixgo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'babixgo_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON babixgo_db.* TO 'babixgo_user'@'localhost';
FLUSH PRIVILEGES;
```

2. Import the database schema:
```bash
mysql -u babixgo_user -p babixgo_db < shared/create-tables.sql
```

### 2. Configuration

1. Configure database credentials using environment variables (recommended):

   **Method 1: Using .env file (recommended for local development)**
   ```bash
   cp .env.example .env
   # Edit .env and set your database credentials
   ```
   
   Example `.env` file:
   ```bash
   DB_HOST=localhost
   DB_NAME=babixgo_db
   DB_USER=babixgo_user
   DB_PASSWORT=your_secure_password
   ```
   
   **Method 2: Set environment variables directly (recommended for production)**
   ```bash
   export DB_HOST=localhost
   export DB_NAME=babixgo_db
   export DB_USER=babixgo_user
   export DB_PASSWORT=your_secure_password
   ```
   
   **Method 3: Update defaults in `shared/config/database.php`** (legacy method)
   ```php
   return [
       'host' => 'localhost',
       'database' => 'babixgo_db',
       'username' => 'babixgo_user',
       'password' => 'your_secure_password',
       'charset' => 'utf8mb4'
   ];
   ```
   
   > **Note:** Environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORT/DB_PASSWORD) take precedence over hardcoded values.

2. Update session domain in `shared/config/session.php`:
```php
ini_set('session.cookie_domain', '.babixgo.de');
```

3. Configure error logging path in `auth/public/.htaccess`:
```apache
php_value error_log /path/to/error_log.txt
```

### 3. File Upload

1. Upload all files via FTP to your Strato hosting
2. Ensure proper directory structure is maintained
3. Set permissions:
```bash
chmod 755 auth/public/
chmod 755 downloads/
chmod 755 downloads/apk/
chmod 755 downloads/exe/
chmod 755 downloads/scripts/
```

### 4. Email Configuration

The system uses PHP's `mail()` function. For production, configure SMTP in `auth/public/includes/mail-helper.php` if needed.

Update email sender in mail-helper.php:
```php
'From' => 'noreply@babixgo.de',
'Reply-To' => 'support@babixgo.de',
```

### 5. Create Admin Account

The database schema includes a default admin account:
- **Username**: admin
- **Email**: admin@babixgo.de
- **Password**: Admin@123 (CHANGE THIS IMMEDIATELY!)

To change the admin password after first login:
1. Login at `https://auth.babixgo.de/login.php`
2. Go to Edit Profile
3. Change password

Or create a new admin via SQL:
```sql
-- Generate password hash (use PHP)
-- php -r "echo password_hash('YourSecurePassword', PASSWORD_DEFAULT);"

INSERT INTO users (username, email, password_hash, role, is_verified, friendship_link) 
VALUES (
    'youradmin',
    'youremail@example.com',
    '$2y$10$...your_generated_hash...',
    'admin',
    1,
    'ADMIN002'
);
```

### 6. Security Configuration

**Important for Production:**

1. **Enable HTTPS redirect** in `auth/public/.htaccess`:
   - Uncomment the HTTPS redirect lines
   
2. **Update session security** in `shared/config/session.php`:
   - Session cookies will use secure flag when HTTPS is detected

3. **Configure Content Security Policy** in `.htaccess` based on your needs

4. **Set up IP whitelist for admin** (optional) in `auth/public/admin/.htaccess`

5. **Disable debug mode** in `shared/config/database.php`:
```php
define('DB_DEBUG', false);
```

6. **Create custom error pages**:
   - Create 404.html, 403.html, 500.html in auth/public/

## Usage

### User Registration Flow

1. User visits `https://auth.babixgo.de/register.php`
2. Fills registration form (username, email, password)
3. System creates account with `is_verified = 0`
4. Verification email sent to user
5. User clicks link in email → `verify-email.php?token=XXX`
6. Account verified, user can login

### Login Flow

1. User visits `https://auth.babixgo.de/login.php`
2. Enters username/email and password
3. Optional: Check "Remember me" for 30-day cookie
4. Redirected to dashboard at `https://auth.babixgo.de/`

### Admin Access

1. Login with admin account
2. Access admin panel at `https://auth.babixgo.de/admin/`
3. Manage users, downloads, and comments

### File Upload (Admin)

1. Navigate to Downloads management
2. Select file type (APK, EXE, Scripts)
3. Upload file (max 500MB)
4. Enter version and description
5. File stored in `downloads/{type}/` directory

## Database Schema

### users
- User accounts with authentication
- Roles: 'user', 'admin'
- Email verification system
- Password reset tokens
- Unique friendship links

### comments
- User comments across domains
- Status: 'pending', 'approved', 'spam'
- Domain and content_id for cross-site comments

### downloads
- File metadata and tracking
- Types: 'apk', 'scripts', 'exe'
- Download count tracking
- Active/inactive status

### download_logs
- Download activity logging
- User tracking (optional)
- IP and user agent logging

## Security Best Practices

### Implemented
- ✅ Passwords hashed with PASSWORD_DEFAULT (bcrypt)
- ✅ CSRF tokens on all forms
- ✅ Prepared statements (no SQL injection)
- ✅ Output escaping (XSS prevention)
- ✅ Session regeneration on login
- ✅ Session timeout (30 minutes)
- ✅ File upload validation (type, size, MIME)
- ✅ Secure session cookies

### Recommended
- 🔒 Enable HTTPS in production
- 🔒 Regular database backups
- 🔒 Monitor error logs
- 🔒 Update PHP regularly
- 🔒 Use strong database passwords
- 🔒 Implement rate limiting for login attempts
- 🔒 Enable 2FA for admin accounts (future enhancement)

## API Endpoints (AJAX)

All form handlers return JSON responses:

**Registration**: `POST /includes/form-handlers/register-handler.php`
```json
{
  "success": true,
  "message": "Registration successful!"
}
```

**Login**: `POST /includes/form-handlers/login-handler.php`
```json
{
  "success": true,
  "redirect": "/index.php"
}
```

**Profile Update**: `POST /includes/form-handlers/profile-handler.php`
```json
{
  "success": true,
  "message": "Profile updated!"
}
```

**Admin Actions**: `POST /includes/form-handlers/admin-handlers.php`
```json
{
  "success": true,
  "message": "Action completed"
}
```

## Integration with Other Domains

### babixgo.de (main site)
```php
// Check if user is logged in
require_once '../shared/config/database.php';
require_once '../shared/config/session.php';
require_once '../shared/config/autoload.php';

if (User::isLoggedIn()) {
    echo "Welcome, " . htmlspecialchars($_SESSION['username']);
}
```

### files.babixgo.de
```php
// Track downloads with user
require_once '../shared/config/database.php';
require_once '../shared/config/session.php';
require_once '../shared/config/autoload.php';

$download = new Download();
$userId = User::isLoggedIn() ? $_SESSION['user_id'] : null;
$download->logDownload($fileId, $userId);
```

## Troubleshooting

### Email not sending
- Check PHP mail() configuration
- Verify SMTP settings if using SMTP
- Check server logs for mail errors

### Session issues across domains
- Verify cookie domain is set to `.babixgo.de`
- Ensure all domains are using HTTPS (or all HTTP)
- Check session.cookie_samesite setting

### File upload fails
- Check PHP upload_max_filesize and post_max_size
- Verify directory permissions (755)
- Check available disk space

### Database connection errors
- Verify credentials in database.php
- Check MySQL service is running
- Verify database user has proper privileges

## Development vs Production

### Development
```php
// database.php
define('DB_DEBUG', true);

// .htaccess - HTTPS redirect commented out
```

### Production
```php
// database.php
define('DB_DEBUG', false);

// .htaccess - HTTPS redirect enabled
// RewriteCond %{HTTPS} off
// RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

## Maintenance

### Regular Tasks
- Monitor error logs
- Review download logs
- Moderate pending comments
- Check for suspicious login attempts
- Backup database regularly
- Update user roles as needed

### Database Cleanup
```sql
-- Remove old unverified accounts (older than 30 days)
DELETE FROM users 
WHERE is_verified = 0 
AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Remove expired reset tokens
UPDATE users 
SET reset_token = NULL, reset_token_expires = NULL 
WHERE reset_token_expires < NOW();
```

## Support & Documentation

For issues or questions:
- Check error logs: `error_log.txt`
- Review database for data integrity
- Verify file permissions
- Check PHP version compatibility (7.4+)

## License

Proprietary - babixgo.de

## Credits

Built with pure PHP, no frameworks or build tools required.
Designed for easy FTP deployment to Strato hosting.
# babixGO Platform - Single Domain Architecture

**Version**: 2.0.0  
**Architecture**: Unified Single-Domain  
**Status**: ✅ Production Ready

Complete authentication, user management, file downloads, and administration system for babixgo.de in a unified single-domain architecture.

---

## 🎯 Features

### 🔐 Authentication System
- ✅ User registration with email verification
- ✅ Secure login with "remember me" functionality
- ✅ Password reset via email
- ✅ Email verification system
- ✅ Profile management
- ✅ Friendship link sharing system

### 👤 User Area (NEW)
- ✅ Personal dashboard
- ✅ Profile editing
- ✅ Comment management
- ✅ Download history
- ✅ Account settings

### 📥 Download Portal
- ✅ Browse downloads by category (APK, EXE, Scripts)
- ✅ Secure file download system
- ✅ Download tracking and analytics
- ✅ File versioning
- ✅ Download count tracking

### ⚙️ Admin Panel
- ✅ User management (view, edit, delete, verify)
- ✅ Download management with file uploads
- ✅ Comment moderation system
- ✅ Statistics dashboard
- ✅ Activity logs
- ✅ Bulk actions

### 🔒 Security Features
- ✅ Password hashing with bcrypt
- ✅ CSRF protection on all forms
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (output escaping)
- ✅ Session security (httponly, secure, samesite)
- ✅ Input validation (server-side and client-side)
- ✅ Secure file upload handling
- ✅ Protected download directory
- ✅ Role-based access control

### 📱 Progressive Web App (PWA)
- ✅ Installable on mobile and desktop
- ✅ Offline support
- ✅ Service worker caching
- ✅ App shortcuts (Downloads, Profile, Login)
- ✅ Responsive design

---

## 🛠️ Technology Stack

- **Backend**: Pure PHP 8.2+ (no frameworks)
- **Database**: MySQL/MariaDB with PDO
- **Frontend**: HTML5, CSS3, JavaScript (no frameworks)
- **Deployment**: FTP-deployable to Strato hosting
- **PWA**: Service Worker, Web App Manifest
- **Design**: Material Design 3 Dark Medium Contrast

---

## 📁 Directory Structure

```
babixgo/                             # Monorepo root
├── shared/                          # Shared resources across entire platform
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
│   ├── classes/                     # PHP classes
│   │   ├── Database.php            # Database wrapper
│   │   ├── User.php                # User management
│   │   ├── Session.php             # Session handling
│   │   ├── Download.php            # Download management
│   │   └── Comment.php             # Comment management
│   │
│   ├── config/                      # Configuration files
│   │   ├── database.php            # Database config
│   │   ├── session.php             # Session management
│   │   └── autoload.php            # Class autoloader
│   │
│   ├── partials/                    # Shared PHP partials
│   │   ├── header.php              # Site header with user menu
│   │   ├── footer.php              # Site footer
│   │   ├── nav.php                 # Navigation
│   │   ├── head-meta.php           # Meta tags
│   │   ├── head-links.php          # CSS/Font links
│   │   ├── critical-css.php        # Critical CSS
│   │   ├── version.php             # Version constant
│   │   └── [other partials]
│   │
│   └── create-tables.sql            # Database schema
│
├── downloads/                       # Secure file storage (NOT web-accessible)
│   ├── .htaccess                   # Deny direct access (CRITICAL)
│   ├── apk/                        # Android APK files
│   ├── exe/                        # Windows executables
│   └── scripts/                    # Script files
│       ├── bash/
│       ├── python/
│       └── powershell/
│
└── babixgo.de/                      # *** UNIFIED SINGLE DOMAIN ***
    ├── .htaccess                   # Unified routing configuration
    ├── index.php                   # Homepage
    ├── 404.php                     # Not Found error page
    ├── 403.php                     # Access Denied error page
    ├── 500.php                     # Server Error page
    │
    ├── public/                      # PWA assets
    │   ├── manifest.json           # PWA manifest with shortcuts
    │   ├── sw.js                   # Service worker (unified)
    │   └── offline.html            # Offline fallback
    │
    ├── assets/                      # Domain-specific assets
    │   ├── css/
    │   │   ├── style.css           # Main site styles
    │   │   └── user.css            # User area styles
    │   ├── js/
    │   ├── icons/
    │   ├── img/
    │   └── logo/
    │
    ├── auth/                        # Authentication (babixgo.de/auth/*)
    │   ├── login.php               # Login page
    │   ├── register.php            # Registration
    │   ├── logout.php              # Logout handler
    │   ├── verify-email.php        # Email verification
    │   ├── forgot-password.php     # Password reset request
    │   ├── reset-password.php      # Password reset form
    │   └── includes/
    │       ├── auth-check.php      # Authentication guard
    │       ├── admin-check.php     # Admin authorization
    │       ├── mail-helper.php     # Email functions
    │       └── form-handlers/
    │
    ├── user/                        # User Area (babixgo.de/user/*)
    │   ├── index.php               # User dashboard
    │   ├── profile.php             # Public profile view
    │   ├── edit-profile.php        # Edit profile
    │   ├── settings.php            # Account settings
    │   ├── my-comments.php         # User's comments
    │   ├── my-downloads.php        # Download history
    │   └── includes/
    │       └── auth-check.php      # User authentication check
    │
    ├── files/                       # Download Portal (babixgo.de/files/*)
    │   ├── index.php               # Download overview
    │   ├── browse.php              # Browse downloads
    │   ├── category.php            # Category view
    │   ├── download.php            # Download handler
    │   └── includes/
    │       └── [helper files]
    │
    ├── admin/                       # Admin Panel (babixgo.de/admin/*)
    │   ├── .htaccess               # Additional admin protection
    │   ├── index.php               # Admin dashboard
    │   ├── users.php               # User management
    │   ├── user-edit.php           # Edit user
    │   ├── downloads.php           # Download management
    │   ├── download-edit.php       # Edit download
    │   ├── comments.php            # Comment moderation
    │   └── includes/
    │       ├── admin-check.php     # Admin role check
    │       └── handlers/
    │
    └── [existing content]/          # Existing site content
        ├── accounts/
        ├── anleitungen/
        ├── wuerfel/
        ├── sticker/
        └── ...
```

---

## 🌐 URL Structure

All features are unified under **babixgo.de**:

### Main Site
```
https://babixgo.de/                  # Homepage
https://babixgo.de/wuerfel/          # Dice service
https://babixgo.de/accounts/         # Accounts
https://babixgo.de/kontakt/          # Contact
```

### Authentication
```
https://babixgo.de/auth/login        # Login
https://babixgo.de/auth/register     # Registration
https://babixgo.de/auth/logout       # Logout
```

### User Area
```
https://babixgo.de/user/             # Dashboard
https://babixgo.de/user/edit-profile # Edit profile
https://babixgo.de/user/settings     # Settings
https://babixgo.de/user/my-comments  # My comments
https://babixgo.de/user/my-downloads # Download history
```

### Downloads
```
https://babixgo.de/files/            # Download portal
https://babixgo.de/files/browse      # Browse files
https://babixgo.de/files/category/apk # APK downloads
https://babixgo.de/files/download/123/apk # Download file
```

### Admin Panel (Admins Only)
```
https://babixgo.de/admin/            # Admin dashboard
https://babixgo.de/admin/users       # User management
https://babixgo.de/admin/downloads   # Download management
https://babixgo.de/admin/comments    # Comment moderation
```

---

## 🗄️ Database Schema

### users
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    description TEXT,
    friendship_link VARCHAR(8) UNIQUE,
    is_verified BOOLEAN DEFAULT 0,
    verification_token VARCHAR(64),
    reset_token VARCHAR(64),
    reset_token_expires DATETIME,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### downloads
```sql
CREATE TABLE downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    filetype ENUM('apk', 'scripts', 'exe') NOT NULL,
    filesize BIGINT,
    version VARCHAR(50),
    description TEXT,
    download_count INT DEFAULT 0,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### download_logs
```sql
CREATE TABLE download_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES downloads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### comments
```sql
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    domain VARCHAR(50) NOT NULL,
    content_id INT,
    comment TEXT NOT NULL,
    status ENUM('approved', 'pending', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 🚀 Deployment

### Prerequisites
- PHP 8.2+ with PDO MySQL extension
- MySQL/MariaDB database
- Web server with .htaccess support (Apache/LiteSpeed)
- FTP/SFTP access to server

### Installation Steps

1. **Upload Files**
   ```bash
   # Upload via FTP to Strato server
   /shared/           → /var/www/shared/
   /downloads/        → /var/www/downloads/
   /babixgo.de/       → /var/www/babixgo.de/
   ```

2. **Set File Permissions**
   ```bash
   chmod 755 /var/www/babixgo.de/
   chmod 750 /var/www/downloads/
   chmod 644 /var/www/downloads/.htaccess  # CRITICAL
   ```

3. **Configure Database**
   - Create database in Strato panel
   - Import `/shared/create-tables.sql`
   - Update `/shared/config/database.php` with credentials

4. **Configure .htaccess**
   - Backup existing: `cp .htaccess .htaccess.backup`
   - Deploy new: `cp .htaccess.new .htaccess`

5. **Test All Sections**
   - Homepage: https://babixgo.de/
   - Login: https://babixgo.de/auth/login
   - Files: https://babixgo.de/files/
   - Admin: https://babixgo.de/admin/

6. **Create First Admin User**
   ```php
   // Via phpMyAdmin or SQL console
   INSERT INTO users (username, email, password_hash, role, is_verified) 
   VALUES (
       'admin',
       'admin@babixgo.de',
       '$2y$10$...',  -- Generate with password_hash('yourpassword', PASSWORD_DEFAULT)
       'admin',
       1
   );
   ```

---

## 📖 Documentation

- **[MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)** - Complete migration documentation from multi-domain to single-domain
- **[DESIGN_SYSTEM.md](DESIGN_SYSTEM.md)** - Design tokens, components, and styling guidelines
- **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Detailed deployment instructions
- **[.github/copilot-instructions.md](.github/copilot-instructions.md)** - Development guidelines

---

## 🔐 Security

- Password hashing: bcrypt with PASSWORD_DEFAULT
- Session: HttpOnly, Secure, SameSite=Lax
- CSRF tokens on all forms
- SQL: PDO prepared statements only
- XSS: All output escaped with htmlspecialchars()
- File uploads: Type validation, size limits, served via PHP
- Downloads: Protected directory, no direct access
- Admin: Role-based access control

---

## 🧪 Testing

### Authentication Flow
1. Register: `/auth/register`
2. Verify email: Check email for link
3. Login: `/auth/login`
4. Access dashboard: `/user/`

### Download Flow
1. Browse: `/files/`
2. Select category: `/files/category/apk`
3. Download: Click download button
4. File served via `/files/download.php`

### Admin Flow
1. Login as admin
2. Access: `/admin/`
3. Manage users, downloads, comments

---

## 📝 Version History

### v2.0.0 (2026-01-15)
- ✅ Migrated to unified single-domain architecture
- ✅ Created user area (/user/)
- ✅ Enhanced header with user menu
- ✅ Updated PWA with shortcuts
- ✅ Unified .htaccess routing
- ✅ Complete documentation

### v1.0.15 (2026-01-14)
- ✅ Cleaned up partials structure
- ✅ Consolidated shared resources
- ✅ Fixed auth structure

---

## 🆘 Support

- **Issues**: Create an issue in the GitHub repository
- **Documentation**: See guides in repository root
- **Contact**: Via `/kontakt/` page on site

---

## 📜 License

Proprietary - All rights reserved by babixGO

---

**Last Updated**: January 15, 2026  
**Version**: 2.0.0  
**Architecture**: Single-Domain Unified

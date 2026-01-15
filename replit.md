# babixGO Platform

## Overview
A German website for Monopoly GO services including stickers, partner events, accounts, and dice. Built with pure PHP 8.2+ (no frameworks), designed for deployment to Strato hosting.

## Project Structure
```
babixgo.de/           # Main document root
├── index.php         # Homepage
├── router.php        # PHP built-in server router for Replit
├── shared/           # Shared resources
│   ├── assets/       # CSS, JS, icons, images
│   ├── classes/      # PHP classes (Database, User, Session, etc.)
│   ├── config/       # Database config, autoloader
│   └── partials/     # Header, footer, meta tags, etc.
├── auth/             # Authentication (login, register, logout)
├── user/             # User dashboard and settings
├── admin/            # Admin panel
├── files/            # Download portal
├── assets/           # Domain-specific assets
├── public/           # PWA manifest and service worker
└── [content dirs]    # wuerfel, sticker, accounts, kontakt, etc.
```

## Running the Application
The PHP built-in server runs on port 5000 with `router.php` handling URL routing.

Command: `cd babixgo.de && php -S 0.0.0.0:5000 router.php`

## Database
The app uses MySQL/MariaDB with PDO. Database configuration is loaded from:
- Environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORT/DB_PASSWORD)
- The `.env` file in the root directory

Currently configured for external Strato database.

## Recent Changes
- 2026-01-15: Set up Replit environment with PHP router

## User Preferences
- Language: German (de)
- Design: Material Design 3 Dark Medium Contrast

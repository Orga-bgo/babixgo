# babixGO.de - PHP Website

## Overview
A static website for **babixGO.de** - a Monopoly GO services site built with PHP partials. The site offers services including stickers, partner events, racers, and accounts.

## Project Architecture
- **Language**: PHP 8.4
- **Type**: Static website with PHP-based partials/includes
- **No build process**: All files are directly deployable
- **Deployment**: Autoscale configuration for PHP server

## Project Structure
```
/
├── partials/        # Reusable PHP partials (header, footer, meta, tracking)
├── assets/          # CSS, JS, images, icons
│   ├── css/         # Stylesheets
│   ├── js/          # JavaScript files
│   ├── icons/       # Legacy SVG icons (Social, Notice-Boxes, Stats)
│   ├── material-symbols/  # Material Symbols for h2 headings
│   └── img/         # Images
├── includes/        # PHP helper functions
├── templates/       # Page templates (not production)
├── add/             # Future/planned files (not production)
├── kontakt/         # Contact form with admin section
└── *.php            # Production pages
```

## Key Files
- `index.php` - Main entry point
- `partials/` - Shared components (header, footer, head-meta, etc.)
- `assets/css/style.css` - Main stylesheet (single source of truth)

## Running Locally
The site runs on PHP's built-in development server:
```
php -S 0.0.0.0:5000
```

## Environment Variables
The contact form backend uses these environment variables (for database):
- `BABIXGO_DB_HOST`
- `BABIXGO_DB_NAME`
- `BABIXGO_DB_USER`
- `BABIXGO_DB_PASS`

## Important Documentation
- `Agents.md` - Development rules and conventions
- `DESIGN_SYSTEM.md` - Brand guide and design tokens
- `README.md` - Project overview

## Icon System
H2 headings use Material Symbols with CSS filter-based coloring:
- `/assets/material-symbols/` - SVG icons for h2 headings
- CSS classes: `icon-service`, `icon-game`, `icon-security`, `icon-help`, `icon-price`, `icon-cta`, `icon-info`
- Optional helper: `includes/icon-helper.php` with `h2_with_icon()` function

## CLS Optimization (Core Web Vitals)
All images require accurate width/height attributes for CLS < 0.1:
- Use `identify <image>` (imagemagick) to get intrinsic dimensions
- Gallery images need data-width/data-height on thumb buttons for dynamic updates
- CSS Layout Shift Prevention in style.css (border-box, block images, min-height)
- Material Symbols: 48×48, stat icons: 24×24

## Recent Changes
- January 2026: CLS optimization - all images with correct dimensions, gallery JS updated
- January 2026: Material Symbols icon system for h2 headings (30+ icons across 12 pages)
- January 2026: Initial Replit environment setup with PHP 8.4

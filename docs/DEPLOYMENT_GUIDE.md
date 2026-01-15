# Migration and Deployment Guide

**Repository**: babixGO Monorepo  
**Date**: January 2026  
**Status**: Production-Ready

## Overview

This guide provides step-by-step instructions for deploying the cleaned-up babixGO monorepo to production (Strato webhosting).

## Pre-Deployment Checklist

Before deploying, ensure:

- [ ] All code changes reviewed and tested locally
- [ ] Database credentials configured in `.env` file
- [ ] FTP credentials available for Strato server
- [ ] Backup of current production site created
- [ ] Read CLEANUP_REPORT.md to understand changes

## Deployment Structure

### Strato Directory Structure

```
/var/www/websites/
├── shared/                      # Upload to /shared/
│   ├── assets/
│   ├── classes/
│   ├── config/
│   └── partials/
│
├── downloads/                   # Upload to /downloads/
│   ├── .htaccess               # CRITICAL: Blocks direct access
│   ├── apk/
│   ├── exe/
│   └── scripts/
│
├── babixgo.de/                  # Upload to /babixgo.de/
│   └── [all files]
│
├── auth/                        # Upload to /auth/
│   ├── .htaccess               # Root config
│   └── public/                 # Document root
│
└── files.babixgo.de/            # Upload to /files/ (or /files.babixgo.de/)
    └── public/                 # Document root
```

### Domain Configuration

In Strato Customer Portal, configure subdomains to point to:

| Subdomain | Target Directory | Notes |
|-----------|------------------|-------|
| **babixgo.de** | `/babixgo.de/` | Main site root |
| **auth.babixgo.de** | `/auth/public/` | Auth system document root |
| **files.babixgo.de** | `/files.babixgo.de/public/` | Download portal document root |

**IMPORTANT**: The directory on the server can be `/files/` but the subdomain should be `files.babixgo.de`. The local directory name `files.babixgo.de/` is for clarity in the monorepo.

## Step-by-Step Deployment

### Step 1: Backup Production

```bash
# Connect via FTP/SFTP
# Download complete backup of:
- Current /babixgo.de/
- Current /auth/
- Current /files/
- Current /shared/
- Current /downloads/

# Store backup with date: babixgo_backup_2026-01-14.zip
```

### Step 2: Upload Shared Resources

**Priority**: HIGHEST (other domains depend on this)

```bash
# Via FTP client (FileZilla, WinSCP):
1. Connect to Strato FTP
2. Navigate to /shared/
3. Upload entire /shared/ directory from local repo
4. Verify upload:
   - /shared/assets/css/main.css exists
   - /shared/partials/header.php exists
   - /shared/classes/Database.php exists
   - /shared/config/database.php exists
```

**Verify shared/config/database.php**:
- Ensure correct production credentials
- Or ensure .env file is in place at root

### Step 3: Upload Downloads Directory

```bash
# Via FTP:
1. Navigate to /downloads/
2. Upload /downloads/.htaccess FIRST (security!)
3. Create subdirectories:
   - /downloads/apk/
   - /downloads/exe/
   - /downloads/scripts/bash/
   - /downloads/scripts/python/
   - /downloads/scripts/powershell/
4. Set permissions:
   - Folders: 750 (rwxr-x---)
   - .htaccess: 644 (rw-r--r--)
```

**Test**: Try accessing https://babixgo.de/downloads/ directly
- Should show "403 Forbidden" or "Access Denied"
- If files are listed, .htaccess not working!

### Step 4: Upload babixgo.de (Main Site)

```bash
# Via FTP:
1. Navigate to /babixgo.de/
2. Upload all files from local babixgo.de/ directory
3. Ensure .htaccess uploaded
4. Verify PWA files in /babixgo.de/public/:
   - manifest.json
   - sw.js
   - offline.html (if exists)
```

**Verify**:
- Visit https://babixgo.de/
- Check browser console for errors
- Verify header/footer load (shared partials)
- Check navigation works

### Step 5: Upload auth.babixgo.de

```bash
# Via FTP:
1. Navigate to /auth/
2. Upload .htaccess to /auth/
3. Upload entire /auth/public/ directory
4. Verify PWA files present:
   - /auth/public/manifest.json
   - /auth/public/sw.js
   - /auth/public/offline.html
5. Check admin directory:
   - /auth/public/admin/.htaccess
```

**Verify**:
- Visit https://auth.babixgo.de/
- Should redirect to login page
- Try logging in (if account exists)
- Check admin panel access (if admin user)

### Step 6: Upload files.babixgo.de

```bash
# Via FTP:
1. Navigate to /files/ (or create /files.babixgo.de/)
2. Upload .htaccess to root
3. Upload entire /files.babixgo.de/public/ directory AS /files/public/
4. Verify PWA files:
   - /files/public/manifest.json
   - /files/public/sw.js
   - /files/public/offline.html
```

**Note**: On the server, you can keep the directory named `/files/` as long as the subdomain is `files.babixgo.de`.

**Verify**:
- Visit https://files.babixgo.de/
- Check download listing loads
- Test downloading a file
- Verify downloads increment counter

### Step 7: Configure Subdomains (Strato Portal)

1. Login to Strato Customer Portal
2. Navigate to "Domain-Verwaltung"
3. Select "babixgo.de"
4. Click "Subdomains verwalten"
5. Configure or verify:

**auth.babixgo.de**:
- Document Root: `/auth/public`
- SSL: Enabled
- HTTPS Redirect: Enabled

**files.babixgo.de**:
- Document Root: `/files/public` (or `/files.babixgo.de/public`)
- SSL: Enabled
- HTTPS Redirect: Enabled

6. Save and wait for DNS propagation (5-15 minutes)

### Step 8: Verify Cross-Domain Functionality

**Test Session Sharing**:
1. Open https://auth.babixgo.de/login.php
2. Login with test account
3. Open https://babixgo.de/ in same browser
4. Check if header shows "Logged in as [username]"
5. Open https://files.babixgo.de/
6. Check if logged-in state persists
7. Logout from any domain
8. Verify all three domains show logged-out state

**Test Shared Partials**:
1. Check header/footer render on all domains
2. Verify navigation links work
3. Check icons display correctly
4. Inspect page source to confirm `/shared/` paths resolve

**Test PWA**:
1. On mobile browser, visit each domain
2. Check for "Add to Home Screen" prompt
3. Test offline mode (disable network, reload)
4. Should show custom offline page

### Step 9: File Permissions

Set correct permissions via FTP client or SSH:

```bash
# Shared resources
chmod 755 /shared/
chmod 755 /shared/assets/
chmod 644 /shared/config/database.php
chmod 644 /shared/classes/*.php
chmod 644 /shared/partials/*.php

# Downloads (secure)
chmod 750 /downloads/
chmod 644 /downloads/.htaccess
chmod 750 /downloads/apk/
chmod 750 /downloads/exe/
chmod 750 /downloads/scripts/

# Domain files
chmod 755 /babixgo.de/
chmod 644 /babixgo.de/*.php
chmod 755 /auth/public/
chmod 644 /auth/public/*.php
chmod 755 /files/public/
chmod 644 /files/public/*.php

# Admin protection
chmod 755 /auth/public/admin/
chmod 644 /auth/public/admin/.htaccess
```

### Step 10: Database Migration (If Needed)

If database schema changed:

```bash
# Via phpMyAdmin or MySQL command line:
1. Access Strato phpMyAdmin
2. Select babixgo database
3. Import /shared/create-tables.sql (if new tables added)
4. Or run ALTER statements if schema updated
```

**Verify**:
- Check all tables exist
- Verify user table has correct columns
- Test login/registration functionality

### Step 11: Post-Deployment Verification

**Homepage (babixgo.de)**:
- [ ] Homepage loads without errors
- [ ] Header/footer render correctly
- [ ] Navigation links work
- [ ] Styles load (check browser console)
- [ ] Images display
- [ ] PWA manifest accessible

**Auth (auth.babixgo.de)**:
- [ ] Login page loads
- [ ] Registration works
- [ ] Email verification sends
- [ ] Password reset works
- [ ] Profile page loads
- [ ] Admin panel accessible (for admin users)
- [ ] PWA works

**Files (files.babixgo.de)**:
- [ ] Download portal loads
- [ ] File listing displays
- [ ] Download handler works
- [ ] File downloads successfully
- [ ] Download counter increments
- [ ] PWA works

**Cross-Domain**:
- [ ] Session persists across all domains
- [ ] Logout affects all domains
- [ ] Shared partials render on all domains
- [ ] No CORS errors in console

**Security**:
- [ ] /downloads/ not directly accessible (403 error)
- [ ] .htaccess files active
- [ ] HTTPS enforced on all domains
- [ ] SSL certificates valid
- [ ] Admin panel requires authentication

### Step 12: Monitor Error Logs

```bash
# Check for errors:
1. Access FTP
2. Look for error_log or error_log.txt files in:
   - /
   - /babixgo.de/
   - /auth/public/
   - /files/public/
3. Review any PHP errors
4. Fix issues and re-upload files if needed
```

## Rollback Procedure

If deployment fails:

### Quick Rollback (Restore Backup)

```bash
1. Via FTP, delete newly uploaded files
2. Upload backup files from babixgo_backup_2026-01-14.zip
3. Restore database from backup (if changed)
4. Clear browser cache
5. Test all domains
```

### Git-Based Rollback

```bash
# If using Git deployment:
git revert bfffce4  # Rollback documentation
git revert f11619f  # Rollback files rename
git revert c9c6d47  # Rollback auth cleanup
git revert 8e17664  # Rollback partials consolidation
git push origin main
```

## Troubleshooting

### Issue: "File not found" errors

**Cause**: Incorrect document root configuration

**Solution**:
1. Check Strato subdomain settings
2. Verify document root paths:
   - auth.babixgo.de → /auth/public/
   - files.babixgo.de → /files/public/
3. Check .htaccess rewrite rules

### Issue: "500 Internal Server Error"

**Cause**: PHP syntax error or .htaccess issue

**Solution**:
1. Check error_log file
2. Test .htaccess by temporarily renaming
3. Verify PHP version compatibility (7.4+)
4. Check file permissions

### Issue: Shared partials not loading

**Cause**: Incorrect path or permissions

**Solution**:
1. Verify /shared/ directory uploaded
2. Check permissions: 755 for folders, 644 for files
3. Verify path in code: `dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/'`
4. Test with absolute path temporarily

### Issue: Session not shared across domains

**Cause**: Cookie domain misconfiguration

**Solution**:
1. Check /shared/config/session.php
2. Verify cookie domain is `.babixgo.de` (with leading dot)
3. Ensure HTTPS on all domains
4. Check browser cookie settings (inspect with DevTools)

### Issue: Downloads showing 403 Forbidden

**Cause**: .htaccess in /downloads/ blocking access

**Solution**:
- This is CORRECT behavior!
- Downloads should be served via files.babixgo.de/download.php
- Never access /downloads/ directly

### Issue: PWA not installing

**Cause**: Manifest or service worker error

**Solution**:
1. Check manifest.json is valid JSON
2. Verify icon paths in manifest.json
3. Check browser console for service worker errors
4. Ensure HTTPS enabled
5. Test on different browsers

## Post-Deployment Tasks

### Week 1: Monitoring

- [ ] Check error logs daily
- [ ] Monitor user feedback
- [ ] Test all critical functions
- [ ] Verify analytics tracking
- [ ] Check download counts
- [ ] Monitor comment system

### Week 2: Optimization

- [ ] Review performance metrics
- [ ] Check Core Web Vitals
- [ ] Optimize images if needed
- [ ] Review cache settings
- [ ] Test on multiple devices

### Ongoing: Maintenance

- [ ] Keep shared/partials/version.php updated
- [ ] Document any hotfixes
- [ ] Update CLEANUP_REPORT.md if structure changes
- [ ] Test new features on all domains
- [ ] Maintain backups

## Support & Resources

- **CLEANUP_REPORT.md**: Detailed change log
- **README.md**: Complete structure documentation
- **DESIGN_SYSTEM.md**: Design tokens and components
- **Strato Support**: https://www.strato.de/apps/CustomerService
- **Repository Issues**: Track deployment issues on GitHub

## Deployment Checklist Summary

Pre-Deployment:
- [ ] Code reviewed
- [ ] Backup created
- [ ] Credentials ready
- [ ] Read documentation

Upload (in order):
- [ ] /shared/ (FIRST)
- [ ] /downloads/ (with .htaccess)
- [ ] /babixgo.de/
- [ ] /auth/
- [ ] /files.babixgo.de/

Configuration:
- [ ] Subdomain document roots
- [ ] File permissions
- [ ] Database (if needed)

Verification:
- [ ] All domains load
- [ ] Cross-domain session works
- [ ] Downloads work
- [ ] PWA installs
- [ ] No console errors

Monitoring:
- [ ] Error logs checked
- [ ] User testing
- [ ] Performance verified

---

**Deployment Status**: ✅ READY FOR PRODUCTION  
**Last Updated**: 2026-01-14  
**Next Review**: Post-deployment verification

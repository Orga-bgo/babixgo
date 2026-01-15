# Deployment Checklist - Single Domain Migration

**Version**: 2.0.0  
**Date**: January 15, 2026  
**Migration**: Multi-Domain → Single-Domain

---

## Pre-Deployment Checklist

### 1. Backup Everything ✅

- [ ] **Full repository backup via Git**
  ```bash
  git commit -am "Backup before single-domain migration"
  git tag backup-pre-migration-2.0.0
  git push origin --tags
  ```

- [ ] **Server files backup via FTP**
  - [ ] Download current `/babixgo.de/` directory
  - [ ] Download current `/auth/` directory  
  - [ ] Download current `/files.babixgo.de/` directory
  - [ ] Download `/shared/` directory
  - [ ] Download `/downloads/` directory

- [ ] **Database backup**
  - [ ] Export via phpMyAdmin
  - [ ] Save SQL dump locally
  - [ ] Verify backup is complete

### 2. Verify New Files Locally ✅

- [ ] All auth files exist in `/babixgo.de/auth/`
- [ ] All user files exist in `/babixgo.de/user/`
- [ ] All files portal pages in `/babixgo.de/files/`
- [ ] All admin files in `/babixgo.de/admin/`
- [ ] `.htaccess.new` created
- [ ] `header.new.php` created
- [ ] Error pages (403.php, 500.php) created
- [ ] PWA files updated (manifest.json, sw.js)
- [ ] CSS file created (`user.css`)

### 3. Review Code Changes ✅

- [ ] All path references use correct BASE_PATH
- [ ] Partials use `SHARED_PATH . 'partials/'`
- [ ] Redirects use new paths (/auth/, /user/, /admin/, /files/)
- [ ] No hardcoded domain references
- [ ] Session configuration unchanged (cookie domain = .babixgo.de)

---

## Deployment Steps

### Phase 1: Upload New Files

**Order matters! Follow exactly:**

#### Step 1.1: Upload Auth Section
```
Local: /babixgo.de/auth/
Server: /var/www/babixgo.de/auth/

Files to upload:
- login.php
- register.php
- logout.php
- verify-email.php
- forgot-password.php
- reset-password.php
- includes/ (entire directory)
```

- [ ] Upload `/babixgo.de/auth/` directory
- [ ] Verify file permissions (644 for .php files)
- [ ] Test: Can access https://babixgo.de/auth/login (may show errors, that's ok for now)

#### Step 1.2: Upload User Section
```
Local: /babixgo.de/user/
Server: /var/www/babixgo.de/user/

Files to upload:
- index.php
- profile.php
- edit-profile.php
- settings.php
- my-comments.php
- my-downloads.php
- includes/ (entire directory)
```

- [ ] Upload `/babixgo.de/user/` directory
- [ ] Verify file permissions

#### Step 1.3: Upload Files Section
```
Local: /babixgo.de/files/
Server: /var/www/babixgo.de/files/

Files to upload:
- index.php
- browse.php
- category.php
- download.php
- includes/ (entire directory)
```

- [ ] Upload `/babixgo.de/files/` directory
- [ ] Verify file permissions

#### Step 1.4: Upload Admin Section
```
Local: /babixgo.de/admin/
Server: /var/www/babixgo.de/admin/

Files to upload:
- .htaccess (admin protection)
- index.php
- users.php
- user-edit.php
- downloads.php
- download-edit.php
- comments.php
- includes/ (entire directory)
```

- [ ] Upload `/babixgo.de/admin/` directory
- [ ] Verify `.htaccess` uploaded
- [ ] Verify file permissions

#### Step 1.5: Upload Assets
```
Local: /babixgo.de/assets/css/user.css
Server: /var/www/babixgo.de/assets/css/user.css
```

- [ ] Upload `user.css`
- [ ] Verify accessible via URL

#### Step 1.6: Upload PWA Files
```
Local: /babixgo.de/public/manifest.json
Local: /babixgo.de/public/sw.js
Server: /var/www/babixgo.de/public/
```

- [ ] Backup old manifest.json
- [ ] Upload new manifest.json
- [ ] Backup old sw.js
- [ ] Upload new sw.js

#### Step 1.7: Upload Error Pages
```
Local: /babixgo.de/403.php
Local: /babixgo.de/500.php
Server: /var/www/babixgo.de/
```

- [ ] Upload 403.php
- [ ] Upload 500.php

### Phase 2: Update Shared Resources

#### Step 2.1: Backup Current Header
```bash
# Via FTP
Download: /var/www/shared/partials/header.php
Save as: header.php.backup-[date]
```

- [ ] Download current header.php
- [ ] Save backup locally

#### Step 2.2: Deploy New Header
```
Local: /shared/partials/header.new.php
Server: /var/www/shared/partials/header.php (replace)
```

- [ ] Upload header.new.php
- [ ] Rename to header.php (overwrite old)
- [ ] Verify file permissions (644)

### Phase 3: Update .htaccess

#### Step 3.1: Backup Current .htaccess
```bash
# Via FTP
Download: /var/www/babixgo.de/.htaccess
Save as: .htaccess.backup-[date]
```

- [ ] Download current .htaccess
- [ ] Save backup locally
- [ ] **CRITICAL**: Keep this backup safe!

#### Step 3.2: Deploy New .htaccess
```
Local: /babixgo.de/.htaccess.new
Server: /var/www/babixgo.de/.htaccess (replace)
```

- [ ] Upload .htaccess.new
- [ ] Rename to .htaccess (overwrite old)
- [ ] Verify file permissions (644)
- [ ] **Test immediately** (see Phase 4)

---

## Phase 4: Testing

### Test 1: Main Site
- [ ] Visit: https://babixgo.de/
- [ ] Expected: Homepage loads normally
- [ ] Check: Header shows login/register buttons (if not logged in)
- [ ] Check: No console errors

### Test 2: Authentication
- [ ] Visit: https://babixgo.de/auth/login
- [ ] Expected: Login page loads
- [ ] Try login with existing account
- [ ] Expected: Redirects to /user/ dashboard
- [ ] Check: Header shows username and dropdown menu

### Test 3: User Dashboard
- [ ] Visit: https://babixgo.de/user/
- [ ] Expected: Dashboard shows user info
- [ ] Check: All links work (My Comments, My Downloads, Settings)
- [ ] Try: Click "Edit Profile"
- [ ] Expected: Profile edit page loads

### Test 4: Download Portal
- [ ] Visit: https://babixgo.de/files/
- [ ] Expected: Download listing loads
- [ ] Try: Click a category (e.g., APK)
- [ ] Expected: Category page loads with filtered downloads
- [ ] Try: Click download button
- [ ] Expected: File download starts

### Test 5: Admin Panel (Admin Only)
- [ ] Login as admin user
- [ ] Visit: https://babixgo.de/admin/
- [ ] Expected: Admin dashboard loads
- [ ] Try: Click "Users"
- [ ] Expected: User list loads
- [ ] Check: All admin functions work

### Test 6: Error Pages
- [ ] Visit: https://babixgo.de/nonexistent-page
- [ ] Expected: 404 page shows
- [ ] Visit: https://babixgo.de/admin/ (as non-admin)
- [ ] Expected: 403 page shows

### Test 7: PWA
- [ ] Open Chrome DevTools → Application → Manifest
- [ ] Check: Shortcuts show (Downloads, Profile, Login)
- [ ] Try: Install PWA
- [ ] Expected: Install prompt appears
- [ ] Check: Service worker registered

### Test 8: Cross-Section Navigation
- [ ] Login via /auth/login
- [ ] Navigate to /files/
- [ ] Check: Still logged in (header shows user menu)
- [ ] Navigate to /user/
- [ ] Check: Still logged in
- [ ] Navigate to /admin/ (if admin)
- [ ] Check: Still logged in
- [ ] Click logout from any section
- [ ] Check: Logged out everywhere

---

## Phase 5: Subdomain Configuration (Strato)

### Option A: Redirect Old Subdomains (Recommended)

**Purpose**: Maintain backward compatibility for existing links

#### Strato Panel Steps:
1. Login to Strato Customer Portal
2. Navigate to "Domain-Verwaltung"
3. Select "babixgo.de"
4. Click "Subdomains verwalten"

#### Configure auth.babixgo.de:
- [ ] Type: Redirect
- [ ] Target: https://babixgo.de/auth/
- [ ] Redirect Type: 301 (Permanent)
- [ ] Save changes

#### Configure files.babixgo.de:
- [ ] Type: Redirect
- [ ] Target: https://babixgo.de/files/
- [ ] Redirect Type: 301 (Permanent)
- [ ] Save changes

#### Wait for DNS:
- [ ] Wait 5-15 minutes for DNS propagation
- [ ] Test: Visit https://auth.babixgo.de/login.php
- [ ] Expected: Redirects to https://babixgo.de/auth/login
- [ ] Test: Visit https://files.babixgo.de/
- [ ] Expected: Redirects to https://babixgo.de/files/

### Option B: Remove Old Subdomains

**Warning**: Only do this if you're confident no external links exist

- [ ] Delete auth.babixgo.de subdomain
- [ ] Delete files.babixgo.de subdomain
- [ ] Wait for DNS propagation
- [ ] Verify subdomains no longer resolve

---

## Phase 6: Monitoring

### Immediate Monitoring (First 24 Hours)

#### Check Error Logs
```bash
# Via Strato File Manager or FTP
View: /var/www/logs/error_log
```

- [ ] Check for 404 errors
- [ ] Check for PHP errors
- [ ] Check for path errors
- [ ] Note any unusual patterns

#### Monitor Traffic
- [ ] Check Google Analytics
- [ ] Look for sudden drop in traffic
- [ ] Check bounce rate
- [ ] Verify tracking code still works

#### Test Key User Flows
- [ ] Registration flow
- [ ] Login flow
- [ ] Download flow
- [ ] Profile editing
- [ ] Admin actions

### Week 1 Monitoring

- [ ] Daily error log checks
- [ ] Weekly traffic analysis
- [ ] User feedback monitoring
- [ ] Performance metrics

---

## Phase 7: Cleanup (After 2 Weeks)

**Only proceed if everything is working perfectly!**

### Server Cleanup

#### Remove Old Auth Directory
```bash
# Via FTP or SSH
Delete: /var/www/auth/
```

- [ ] Verify auth.babixgo.de redirect is working
- [ ] Backup old directory locally first
- [ ] Delete `/var/www/auth/` directory

#### Remove Old Files Directory
```bash
# Via FTP or SSH
Delete: /var/www/files.babixgo.de/
```

- [ ] Verify files.babixgo.de redirect is working
- [ ] Backup old directory locally first
- [ ] Delete `/var/www/files.babixgo.de/` directory

### Repository Cleanup

- [ ] Remove `/auth/` from Git repository
- [ ] Remove `/files.babixgo.de/` from Git repository
- [ ] Commit cleanup
- [ ] Tag new version: `v2.0.0`

---

## Rollback Plan

**If something goes wrong, follow these steps:**

### Immediate Rollback (Within 24 Hours)

#### Step 1: Restore .htaccess
```bash
# Via FTP
Upload: .htaccess.backup-[date]
Rename to: .htaccess
```

- [ ] Upload backup .htaccess
- [ ] Overwrite current .htaccess
- [ ] Test site immediately

#### Step 2: Restore Header
```bash
# Via FTP
Upload: header.php.backup-[date]
Rename to: header.php
```

- [ ] Upload backup header.php
- [ ] Overwrite current header.php

#### Step 3: Re-enable Subdomains
- [ ] Strato Panel → Domain Administration
- [ ] Restore auth.babixgo.de pointing to /var/www/auth/public
- [ ] Restore files.babixgo.de pointing to /var/www/files.babixgo.de/public
- [ ] Wait for DNS propagation

#### Step 4: Remove New Directories (Optional)
- [ ] Delete /babixgo.de/auth/ (uploaded in Phase 1)
- [ ] Delete /babixgo.de/user/
- [ ] Delete /babixgo.de/files/
- [ ] Delete /babixgo.de/admin/

#### Step 5: Clear Caches
- [ ] Clear browser cache
- [ ] Clear service worker (DevTools → Application → Storage → Clear)
- [ ] Clear server cache (if applicable)

#### Step 6: Test
- [ ] Test auth.babixgo.de/login.php
- [ ] Test files.babixgo.de/
- [ ] Test main site
- [ ] Verify all working as before

---

## Troubleshooting

### Issue: 404 on /auth/login

**Possible Causes**:
- .htaccess not uploaded correctly
- Clean URL rewrite rules not working
- Directory permissions wrong

**Fix**:
1. Check .htaccess uploaded: `ls -la /var/www/babixgo.de/.htaccess`
2. Check file size matches original
3. Try accessing: `/auth/login.php` (with .php extension)
4. If .php works, rewrite rules are the issue

### Issue: 500 Internal Server Error

**Possible Causes**:
- PHP syntax error
- .htaccess syntax error
- File permissions too restrictive

**Fix**:
1. Check error log: `/var/www/logs/error_log`
2. Look for specific error message
3. Fix syntax or permissions as indicated
4. If .htaccess, try renaming it to disable temporarily

### Issue: Session Lost After Login

**Possible Causes**:
- Session cookie domain wrong
- Session files not writable

**Fix**:
1. Check `/shared/config/session.php`
2. Verify: `ini_set('session.cookie_domain', '.babixgo.de');`
3. Check session directory permissions
4. Clear browser cookies and retry

### Issue: Downloads Not Working

**Possible Causes**:
- /downloads/.htaccess missing or wrong
- File paths incorrect in download.php
- Database filepath wrong

**Fix**:
1. Check `/downloads/.htaccess` exists
2. Content should be: `Order Deny,Allow` / `Deny from all`
3. Verify DOWNLOADS_PATH in download.php
4. Check database `filepath` column

### Issue: Admin Panel Shows 403

**Possible Causes**:
- User role is not 'admin'
- admin-check.php not included
- Session not active

**Fix**:
1. Check database: `SELECT role FROM users WHERE id = ?`
2. Update if needed: `UPDATE users SET role = 'admin' WHERE id = ?`
3. Check admin-check.php is included at top of admin pages
4. Clear session and login again

---

## Post-Deployment Documentation

### Update These Documents:

- [ ] README.md (deploy README.new.md)
- [ ] .github/copilot-instructions.md (update structure)
- [ ] CHANGELOG.md (add v2.0.0 entry)
- [ ] Package/version files (if any)

### Notify Stakeholders:

- [ ] Internal team
- [ ] Users (if breaking changes affect them)
- [ ] Update any external documentation

---

## Success Criteria

Migration is successful when ALL of these are true:

- [x] All sections accessible under babixgo.de
- [x] Authentication works across all sections
- [x] Downloads work correctly
- [x] Admin panel accessible to admins
- [x] No increase in error rate
- [x] No significant drop in traffic
- [x] PWA installs correctly
- [x] Service worker works
- [x] Old subdomain redirects work (if kept)
- [x] All tests pass
- [x] No user complaints

---

## Contact & Support

**Questions during deployment?**
- Check MIGRATION_GUIDE.md
- Check README.md
- Review error logs
- Create GitHub issue if needed

**Emergency rollback needed?**
- Follow Rollback Plan above
- Document what went wrong
- Restore from backups

---

**Deployment Date**: _______________  
**Deployed By**: _______________  
**Status**: ⬜ Success ⬜ Rollback ⬜ Partial  
**Notes**: _____________________________________________

---

**Last Updated**: January 15, 2026  
**Version**: 2.0.0

# Fix Staging Git Repository

## Problem
After copying files to staging directory `stagdash.nicepatrol.id`, Git shows ownership error:
```
fatal: detected dubious ownership in repository at '/www/wwwroot/stagdash.nicepatrol.id'
```

## Solution Steps

### 1. Add Safe Directory (Quick Fix)
```bash
cd /www/wwwroot/stagdash.nicepatrol.id
git config --global --add safe.directory /www/wwwroot/stagdash.nicepatrol.id
```

### 2. Fix File Ownership (Handle .user.ini Issue)
```bash
# First, try to remove immutable attribute from .user.ini
sudo chattr -i /www/wwwroot/stagdash.nicepatrol.id/public/.user.ini 2>/dev/null || true

# Change ownership to current user (skip problematic files)
sudo chown -R $USER:$USER /www/wwwroot/stagdash.nicepatrol.id --exclude=*.user.ini 2>/dev/null || true

# Or change ownership excluding .user.ini files
find /www/wwwroot/stagdash.nicepatrol.id -type f ! -name ".user.ini" -exec sudo chown www-data:www-data {} \;
find /www/wwwroot/stagdash.nicepatrol.id -type d -exec sudo chown www-data:www-data {} \;
```

### 3. Set Proper Permissions
```bash
# Set directory permissions
find /www/wwwroot/stagdash.nicepatrol.id -type d -exec chmod 755 {} \;

# Set file permissions
find /www/wwwroot/stagdash.nicepatrol.id -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache writable
chmod -R 775 /www/wwwroot/stagdash.nicepatrol.id/storage
chmod -R 775 /www/wwwroot/stagdash.nicepatrol.id/bootstrap/cache
```

### 4. Reinitialize Git Remote (If Needed)
```bash
cd /www/wwwroot/stagdash.nicepatrol.id

# Check current remote
git remote -v

# If remote is wrong, update it
git remote set-url origin https://github.com/yourusername/your-repo.git

# Or add if missing
git remote add origin https://github.com/yourusername/your-repo.git
```

### 5. Test Git Operations
```bash
# Check status
git status

# Pull latest changes
git pull origin main

# Or if you need to reset to match remote
git fetch origin
git reset --hard origin/main
```

## Alternative: Fresh Clone Method

If the above doesn't work, you can do a fresh clone:

```bash
# Backup current .env and any custom configs
cp /www/wwwroot/stagdash.nicepatrol.id/.env /tmp/staging-env-backup

# Remove old directory
sudo rm -rf /www/wwwroot/stagdash.nicepatrol.id

# Fresh clone
cd /www/wwwroot
git clone https://github.com/yourusername/your-repo.git stagdash.nicepatrol.id

# Restore .env
cp /tmp/staging-env-backup /www/wwwroot/stagdash.nicepatrol.id/.env

# Set proper ownership
sudo chown -R www-data:www-data /www/wwwroot/stagdash.nicepatrol.id

# Install dependencies
cd /www/wwwroot/stagdash.nicepatrol.id
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

## Quick Commands for Your Case (Updated for .user.ini Issue)

Run these commands in order:

```bash
# 1. Add safe directory
git config --global --add safe.directory /www/wwwroot/stagdash.nicepatrol.id

# 2. Try to remove immutable attribute (might fail, that's OK)
sudo chattr -i /www/wwwroot/stagdash.nicepatrol.id/public/.user.ini 2>/dev/null || echo "Could not remove immutable attribute, continuing..."

# 3. Fix ownership excluding .user.ini files
find /www/wwwroot/stagdash.nicepatrol.id -type f ! -name ".user.ini" -exec sudo chown www-data:www-data {} \; 2>/dev/null || true
find /www/wwwroot/stagdash.nicepatrol.id -type d -exec sudo chown www-data:www-data {} \; 2>/dev/null || true

# 4. Alternative: Just fix the current user ownership for Git
sudo chown $USER:$USER /www/wwwroot/stagdash.nicepatrol.id/.git -R

# 5. Test git
cd /www/wwwroot/stagdash.nicepatrol.id
git status

# 6. Pull updates
git pull origin main
```

## If Still Having Issues - Minimal Fix

If the above is too complex, just fix Git ownership:

```bash
cd /www/wwwroot/stagdash.nicepatrol.id

# Add safe directory
git config --global --add safe.directory $(pwd)

# Fix only .git directory ownership
sudo chown -R $USER:$USER .git/

# Test
git status
git pull origin main
```

## After Git is Working - Clean Modified Files

If you see many modified files after fixing ownership:

```bash
# Option 1: Reset everything (CAREFUL - will lose local changes)
git reset --hard origin/master
git pull origin master

# Option 2: Safe reset (preserve important files)
cp .htaccess .htaccess.backup 2>/dev/null || true
cp public/.user.ini public/.user.ini.backup 2>/dev/null || true
git reset --hard origin/master
cp .htaccess.backup .htaccess 2>/dev/null || true
cp public/.user.ini.backup public/.user.ini 2>/dev/null || true
git pull origin master

# Option 3: Stash changes (if you want to keep some)
git stash
git pull origin master
git stash pop  # Only if you need the changes back
```

## Notes
- Replace `www-data` with your actual web server user if different
- Make sure the GitHub repository URL is correct
- The `.user.ini` file is protected by hosting provider - this is normal
- After reset, staging site should be able to pull updates normally
- Always backup important config files before reset
# Deployment Guide: Hotel Management System to cPanel Server

A comprehensive guide for deploying the HMS Laravel application to a cPanel-hosted server.

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Pre-Deployment Checklist](#pre-deployment-checklist)
- [Step 1: Server Setup](#step-1-server-setup)
- [Step 2: Upload Project Files](#step-2-upload-project-files)
- [Step 3: Database Configuration](#step-3-database-configuration)
- [Step 4: Environment Configuration](#step-4-environment-configuration)
- [Step 5: PHP Configuration](#step-5-php-configuration)
- [Step 6: Composer Installation](#step-6-composer-installation)
- [Step 7: Build Frontend Assets](#step-7-build-frontend-assets)
- [Step 8: Database Migrations](#step-8-database-migrations)
- [Step 9: SSL Certificate](#step-9-ssl-certificate)
- [Step 10: Cronjob Setup](#step-10-cronjob-setup)
- [Post-Deployment](#post-deployment)
- [Troubleshooting](#troubleshooting)

---

## ✅ Prerequisites

Before deploying, ensure:

- **cPanel hosting account** with SSH access enabled
- **PHP 8.3+** installed on server
- **PostgreSQL or MySQL** database available
- **Composer** installed on server (usually pre-installed)
- **Node.js/npm** (optional, for frontend builds)
- **Git** access (for pulling code) or FTP/SFTP client
- **SSL certificate** (free with Let's Encrypt via AutoSSL)
- **Domain name** pointed to your cPanel server

---

## 🔍 Pre-Deployment Checklist

- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false` in production
- [ ] Generate new `APP_KEY`
- [ ] Create database on cPanel
- [ ] Enable SSH access in cPanel
- [ ] Set correct file permissions
- [ ] Backup existing data if updating
- [ ] Test on staging first (recommended)

---

## Step 1: Server Setup

### 1.1 Access cPanel

1. Log in to your cPanel account
2. Navigate to **File Manager** or use SSH (recommended for speed)
3. Go to **public_html** or your domain's root directory

### 1.2 Via SSH (Recommended - Faster)

```bash
# Connect to your server
ssh username@yourdomain.com

# Navigate to your domain directory
cd public_html

# Or if you have a subdomain
cd ~/public_html/subdomain-name
```

### 1.3 Check PHP Version

```bash
php -v
```

Should show PHP 8.3 or higher. If not, change PHP version in cPanel:

1. Go to **MultiPHP Manager** in cPanel
2. Select your domain
3. Choose **PHP 8.3** or higher
4. Click **Apply**

---

## Step 2: Upload Project Files

### Option A: Using Git (Recommended)

```bash
# SSH into your server
ssh username@yourdomain.com
cd public_html

# Clone the repository
git clone https://github.com/your-org/hms.git .

# Or if cloning into a folder
git clone https://github.com/your-org/hms.git hms
cd hms
```

### Option B: Using FTP/SFTP

1. Connect via FTP/SFTP client (FileZilla, WinSCP, etc.)
2. Upload all project files to `public_html/`
3. Ensure `public/` directory content is in `public_html/` OR
4. Keep project in a subfolder and configure accordingly

### Option C: Using File Manager

1. Compress project locally: `tar -czf hms.tar.gz`
2. Upload via cPanel File Manager
3. Extract in `public_html/`

---

## Step 3: Database Configuration

### 3.1 Create Database via cPanel

1. Log in to cPanel
2. Navigate to **MySQL® Databases** or **PostgreSQL Databases**
3. Create new database:
   - Database Name: `hms_production`
   - Enter database password
4. Create database user:
   - Username: `hms_user`
   - Password: (strong password)
5. Add user to database with **ALL PRIVILEGES**

### 3.2 Note Credentials

Save these for `.env` configuration:

```
DB_CONNECTION=mysql  (or pgsql for PostgreSQL)
DB_HOST=localhost
DB_PORT=3306  (or 5432 for PostgreSQL)
DB_DATABASE=cpanelusername_hms_production
DB_USERNAME=cpanelusername_hms_user
DB_PASSWORD=your_strong_password
```

---

## Step 4: Environment Configuration

### 4.1 Create `.env` File

```bash
# Via SSH
cd /path/to/your/project
cp .env.example .env
```

### 4.2 Edit `.env` for Production

```bash
nano .env
```

Update these values:

```env
# App Config
APP_NAME="Hotel Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (from Step 3)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpanelusername_hms_production
DB_USERNAME=cpanelusername_hms_user
DB_PASSWORD=your_strong_password

# Session & Cache (use file-based for simplicity on shared hosting)
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-email-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# App Key (generate in next step)
APP_KEY=

# Optional: Logging
LOG_CHANNEL=stack
LOG_LEVEL=notice
```

Press `Ctrl+O` to save, `Enter` to confirm, `Ctrl+X` to exit nano.

### 4.3 Generate Application Key

```bash
php artisan key:generate
```

This will populate `APP_KEY` in `.env`.

---

## Step 5: PHP Configuration

### 5.1 Increase PHP Limits (via .htaccess)

Create/edit `.htaccess` in your project root:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# PHP Configuration
<IfModule mod_php.c>
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M
</IfModule>
```

### 5.2 Check PHP Extensions

Required extensions:

```bash
php -m | grep -E 'pdo|curl|mbstring|json|xml|bcmath'
```

Contact hosting if any are missing.

---

## Step 6: Composer Installation

### 6.1 Install Dependencies

```bash
# Navigate to project directory
cd /path/to/your/project

# Install Composer dependencies (production only)
composer install --no-dev --optimize-autoloader
```

This will:

- Install all required PHP packages
- Optimize autoloader for production
- Exclude dev dependencies

**Time**: 2-5 minutes depending on server speed

---

## Step 7: Build Frontend Assets

### 7.1 Option A: Build Locally, Upload

```bash
# On your local machine
npm install
npm run build

# Upload the generated 'build' folder to your server
# Or commit to git and pull
```

### 7.2 Option B: Build on Server (if Node.js available)

```bash
# On server via SSH
cd /path/to/your/project
npm install --production
npm run build
```

---

## Step 8: Database Migrations

### 8.1 Run Migrations

```bash
php artisan migrate --force
```

The `--force` flag bypasses confirmation for production.

### 8.2 Seed Database (Optional)

```bash
php artisan db:seed
```

This creates demo data (organizations, hotels, users, etc.).

### 8.3 Verify Database

```bash
# Check if tables were created
mysql -u username -p database_name
mysql> SHOW TABLES;
mysql> exit;
```

---

## Step 9: SSL Certificate

### 9.1 Auto SSL (Recommended)

1. Go to cPanel **AutoSSL**
2. Check your domain
3. Click **Run AutoSSL**
4. Wait for completion (usually instant)
5. Verify HTTPS works

### 9.2 Update `.env`

```env
APP_URL=https://yourdomain.com
```

### 9.3 Force HTTPS (Laravel)

Edit `app/Providers/AppServiceProvider.php`:

```php
public function boot()
{
    if ($this->app->environment('production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
```

---

## Step 10: Cronjob Setup

### 10.1 Add Cronjob

1. Go to cPanel **Cron Jobs**
2. Add new cron job:
   - Common Settings: Select **Once Per Hour** (or preferred interval)
   - Command:
     ```
     /usr/bin/php -q /home/username/public_html/artisan schedule:run
     ```

Replace `/home/username/public_html` with your actual path (get from SSH: `pwd`).

### 10.2 Queue Worker (If Using Queues)

For background jobs, you may need to use a long-running process. Contact hosting or use **Supervisor** if available:

```bash
# Check if supervisor available
which supervisord

# If available, create config file /etc/supervisor/conf.d/hms-worker.conf
```

For now, use `QUEUE_CONNECTION=database` or `QUEUE_CONNECTION=sync` in `.env`.

---

## Post-Deployment

### 11.1 Set Correct Permissions

```bash
# Set directory permissions
chmod -R 755 /path/to/project
chmod -R 755 storage bootstrap/cache

# Set ownership if needed
chown -R username:username /path/to/project
```

### 11.2 Clear Cache

```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 11.3 Test the Application

1. Visit `https://yourdomain.com`
2. Check that:
   - Page loads without errors
   - Database connection works
   - Authentication functions
   - Assets (CSS/JS) load correctly

### 11.4 Monitor Logs

```bash
# View recent error logs
tail -50 storage/logs/laravel.log

# Watch live logs (requires SSH)
tail -f storage/logs/laravel.log
```

---

## 🔧 Troubleshooting

### Issue: "500 Internal Server Error"

**Check error logs:**

```bash
tail storage/logs/laravel.log
```

**Common causes:**

- Missing `.env` file
- Invalid `APP_KEY`
- Database connection failed
- File permissions incorrect

**Fix:**

```bash
chmod -R 755 storage bootstrap/cache
php artisan config:cache
```

### Issue: "Class not found" or "Autoloader errors"

```bash
composer dumpautoload -o
```

### Issue: "Database connection refused"

- Verify credentials in `.env`
- Check database exists in cPanel
- Verify user has privileges:
  ```bash
  mysql -u username -p database_name
  ```

### Issue: "Module not found" or "Extension not installed"

Contact hosting to enable:

- `pdo_mysql` or `pdo_pgsql`
- `curl`
- `json`
- `mbstring`

Visit cPanel **MultiPHP INI Editor** to verify settings.

### Issue: "Storage directory not writable"

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Issue: "Composer out of memory"

```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### Issue: Emails not sending

- Verify MAIL settings in `.env`
- Check cPanel **Email Accounts** exists
- Test with:
  ```bash
  php artisan tinker
  >>> Mail::raw('Test email', function ($msg) { $msg->to('test@example.com'); });
  ```

---

## 📊 Maintenance

### Regular Tasks

**Weekly:**

```bash
php artisan backup:run  # if backup package installed
tail -50 storage/logs/laravel.log  # Review errors
```

**Monthly:**

```bash
php artisan migrate  # Apply pending migrations
composer update  # Update dependencies
npm update && npm run build  # Update frontend
```

**Quarterly:**

```bash
php artisan cache:clear
php artisan config:cache
# Test database backups restore
```

### Update Application

```bash
git pull origin master
composer install --no-dev
npm run build
php artisan migrate --force
php artisan cache:clear
```

---

## 📞 Support & Resources

- **Laravel Docs**: https://laravel.com/docs
- **cPanel Support**: Contact your hosting provider
- **Application Logs**: `storage/logs/laravel.log`
- **PHP Error Logs**: Check cPanel Error Logs

---

## ✨ Optimization Tips

1. **Enable OPcache** (via MultiPHP INI Editor)

   ```
   opcache.enable=1
   opcache.memory_consumption=256
   ```

2. **Set up CDN** for static assets

3. **Enable GZIP compression** in `.htaccess`:

   ```apache
   <IfModule mod_deflate.c>
     AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
   </IfModule>
   ```

4. **Use CloudFlare** for DNS and caching

5. **Regular backups** via cPanel Backups

---

## 🔒 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Use strong database password
- [ ] Enable SSL/HTTPS
- [ ] Set proper file permissions (755 for files, 644 for folders)
- [ ] Keep Laravel, dependencies updated
- [ ] Monitor error logs regularly
- [ ] Use environment variables for secrets
- [ ] Enable firewall/IP restrictions if needed
- [ ] Regular database backups
- [ ] Hide `.env` from web access (Laravel handles this)

---

**Last Updated**: May 13, 2026  
**Laravel Version**: 13.8  
**PHP Version**: 8.3+

# Lead Tracking CRM - Deployment Guide

## Table of Contents
1. [Pre-Deployment Preparation](#pre-deployment-preparation)
2. [VPS Deployment (Linux Server)](#vps-deployment-linux-server)
3. [cPanel Deployment (Shared Hosting)](#cpanel-deployment-shared-hosting)
4. [Cloud Platform Deployment](#cloud-platform-deployment)
5. [Database Migration](#database-migration)
6. [Environment Configuration](#environment-configuration)
7. [SSL Certificate Setup](#ssl-certificate-setup)
8. [Domain Configuration](#domain-configuration)
9. [Post-Deployment Testing](#post-deployment-testing)
10. [Maintenance & Backup](#maintenance--backup)

## Pre-Deployment Preparation

### 1. Optimize for Production

```bash
# Clear and optimize caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Build frontend assets
npm run build
```

### 2. Security Checklist
- [ ] Remove `.env.example` from production
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Use strong database passwords
- [ ] Configure proper file permissions

### 3. Create Production Files

Create `.htaccess` in public folder:
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
```

## VPS Deployment (Linux Server)

### 1. Server Requirements
- Ubuntu 20.04+ or CentOS 8+
- PHP 8.1+
- MySQL 8.0+
- Nginx or Apache
- Composer
- Node.js & npm

### 2. Install Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd -y

# Install MySQL
sudo apt install mysql-server -y

# Install Nginx
sudo apt install nginx -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 3. Configure Nginx

Create `/etc/nginx/sites-available/leadcrm`:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/leadcrm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. Deploy Application

```bash
# Create directory
sudo mkdir -p /var/www/leadcrm
cd /var/www/leadcrm

# Upload your project files (via git, scp, or ftp)
git clone your-repository-url .

# Set permissions
sudo chown -R www-data:www-data /var/www/leadcrm
sudo chmod -R 755 /var/www/leadcrm
sudo chmod -R 775 /var/www/leadcrm/storage
sudo chmod -R 775 /var/www/leadcrm/bootstrap/cache

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Enable site
sudo ln -s /etc/nginx/sites-available/leadcrm /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

# cPanel Deployment Guide - Option B

This guide details how to deploy the Lead Tracking System to cPanel hosting using Option B (copy public/ into public_html).

## Target Configuration

- Domain: https://leadtracking.nikahsatuapps.com/
- Deployment Method: Option B (copy public/ folder contents into public_html)
- SSH Access: Available
- Document Root: Cannot be changed (using public_html)

## Pre-Deployment Checklist

1) Build frontend assets locally
- npm install
- npm run build

2) Optimize Composer dependencies
- composer install --no-dev --optimize-autoloader

## Server Setup Steps

1) Upload files to cPanel
- Upload main application (excluding public/ folder) to a folder above public_html (e.g., /home/username/leadtracking/)
- Upload public folder contents (index.php, .htaccess, favicon.ico, robots.txt) into public_html
- Upload public/build/ assets if present

2) Configure environment
- Create .env from .env.example and fill production values
- APP_ENV=production, APP_DEBUG=false, APP_URL=https://leadtracking.nikahsatuapps.com
- Fill database and mail settings

3) Set file permissions
- storage/ and bootstrap/cache/ must be writable

4) Run Laravel setup commands
- php artisan key:generate
- php artisan migrate --force
- php artisan storage:link
- php artisan config:cache, route:cache, view:cache
- php artisan cache:clear

5) Test application
- Visit the domain and verify homepage, dashboard, charts, and lead forms work

## Troubleshooting

- "Application files not found": ensure core app files are above public_html and index.php can find vendor/ and bootstrap/app.php
- 500 errors: check storage/logs/laravel.log and server error logs
- Database issues: verify .env credentials, run php artisan migrate:status
- Permission issues: ensure storage/ and bootstrap/cache/ are writable
- Cache issues: php artisan config:clear, route:clear, view:clear

## Security Considerations

- Never commit .env to version control
- Use strong, unique passwords
- Generate a secure APP_KEY and keep APP_DEBUG=false in production
- Ensure HTTPS (SSL) is active on the domain

## Maintenance

- To update: pull latest, composer install --no-dev, run migrations, rebuild caches, and copy any new public assets to public_html
- Database backups: use mysqldump via cPanel or SSH
## Cloud Platform Deployment

### AWS EC2 Deployment

1. **Launch EC2 Instance**
   - Choose Ubuntu 20.04 LTS
   - Select t3.micro or larger
   - Configure security groups (HTTP, HTTPS, SSH)

2. **Connect and Setup**
   ```bash
   ssh -i your-key.pem ubuntu@your-ec2-ip
   # Follow VPS deployment steps above
   ```

### DigitalOcean Droplet

1. **Create Droplet**
   - Choose Ubuntu 20.04
   - Select $10/month plan or higher
   - Add SSH key

2. **Deploy Application**
   ```bash
   ssh root@your-droplet-ip
   # Follow VPS deployment steps above
   ```

### Heroku Deployment

1. **Prepare for Heroku**
   ```bash
   # Install Heroku CLI
   # Create Procfile
   echo "web: vendor/bin/heroku-php-apache2 public/" > Procfile
   ```

2. **Deploy**
   ```bash
   heroku create your-app-name
   heroku addons:create cleardb:ignite
   git push heroku main
   heroku run php artisan migrate --seed
   ```

## Database Migration

### 1. Export Local Database

```bash
# Export from XAMPP MySQL
mysqldump -u root -p lead_tracking_crm > database_backup.sql
```

### 2. Import to Production

```bash
# Create production database
mysql -u root -p -e "CREATE DATABASE lead_tracking_crm;"

# Import data
mysql -u root -p lead_tracking_crm < database_backup.sql

# Or run migrations on fresh database
php artisan migrate --seed
```

### 3. Database User Setup

```sql
-- Create dedicated user
CREATE USER 'leadcrm_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON lead_tracking_crm.* TO 'leadcrm_user'@'localhost';
FLUSH PRIVILEGES;
```

## Environment Configuration

### Production .env Template

```env
APP_NAME="Lead Tracking CRM"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lead_tracking_crm
DB_USERNAME=leadcrm_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Key Changes for Production

1. **Generate new APP_KEY**:
   ```bash
   php artisan key:generate
   ```

2. **Update database credentials**
3. **Set APP_DEBUG=false**
4. **Configure proper APP_URL**
5. **Set up mail configuration**

## SSL Certificate Setup

### Using Let's Encrypt (Free)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Manual SSL Certificate

1. Purchase SSL certificate from provider
2. Generate CSR:
   ```bash
   openssl req -new -newkey rsa:2048 -nodes -keyout your-domain.key -out your-domain.csr
   ```
3. Upload certificate files to server
4. Configure Nginx/Apache with SSL

## Domain Configuration

### DNS Settings

```
Type    Name    Value               TTL
A       @       your-server-ip      3600
A       www     your-server-ip      3600
CNAME   api     your-domain.com     3600
```

### Nginx SSL Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/leadcrm/public;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Rest of configuration...
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

## Post-Deployment Testing

### Testing Checklist

- [ ] **Homepage loads correctly**
  ```bash
  curl -I https://your-domain.com
  ```

- [ ] **Database connection works**
  ```bash
  php artisan tinker
  # Test: App\Models\Lead::count()
  ```

- [ ] **API endpoints respond**
  ```bash
  curl -X GET https://your-domain.com/api/leads
  curl -X POST https://your-domain.com/api/webhook/lead \
    -H "Content-Type: application/json" \
    -d '{"name":"Test Lead","email":"test@example.com"}'
  ```

- [ ] **Admin dashboard accessible**
  - Visit: `https://your-domain.com/dashboard`
  - Check charts load
  - Test CSV export
  - Verify ad spend form

- [ ] **SSL certificate valid**
  ```bash
  openssl s_client -connect your-domain.com:443 -servername your-domain.com
  ```

- [ ] **Performance check**
  ```bash
  # Check response times
  curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com
  ```

### Monitoring Setup

```bash
# Install monitoring tools
sudo apt install htop iotop nethogs -y

# Setup log monitoring
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/leadcrm/storage/logs/laravel.log
```

## Maintenance & Backup

### Automated Backups

Create `/home/backup-script.sh`:
```bash
#!/bin/bash

# Variables
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/home/backups"
DB_NAME="lead_tracking_crm"
DB_USER="leadcrm_user"
DB_PASS="your_password"
APP_DIR="/var/www/leadcrm"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C $APP_DIR .

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*backup*" -mtime +7 -delete

echo "Backup completed: $DATE"
```

### Cron Job Setup

```bash
# Make script executable
chmod +x /home/backup-script.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /home/backup-script.sh
```

### Update Procedure

```bash
# 1. Backup current version
./backup-script.sh

# 2. Put site in maintenance mode
php artisan down

# 3. Pull latest changes
git pull origin main

# 4. Update dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 5. Run migrations
php artisan migrate

# 6. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Bring site back up
php artisan up
```

### Security Maintenance

```bash
# Regular security updates
sudo apt update && sudo apt upgrade -y

# Monitor failed login attempts
sudo tail -f /var/log/auth.log | grep "Failed password"

# Check for suspicious files
find /var/www/leadcrm -name "*.php" -mtime -1 -ls

# Monitor disk usage
df -h
du -sh /var/www/leadcrm/*
```

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   ```bash
   # Check Laravel logs
   tail -f /var/www/leadcrm/storage/logs/laravel.log
   
   # Check web server logs
   tail -f /var/log/nginx/error.log
   
   # Fix permissions
   sudo chown -R www-data:www-data /var/www/leadcrm
   sudo chmod -R 755 /var/www/leadcrm
   sudo chmod -R 775 /var/www/leadcrm/storage
   ```

2. **Database Connection Issues**
   ```bash
   # Test connection
   php artisan tinker
   # Run: DB::connection()->getPdo();
   
   # Check MySQL status
   sudo systemctl status mysql
   ```

3. **SSL Certificate Issues**
   ```bash
   # Renew Let's Encrypt
   sudo certbot renew
   
   # Check certificate expiry
   openssl x509 -in /etc/letsencrypt/live/your-domain.com/cert.pem -text -noout | grep "Not After"
   ```

### Performance Optimization

```bash
# Enable OPcache
sudo nano /etc/php/8.1/fpm/php.ini
# Set: opcache.enable=1

# Configure MySQL
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Add optimizations for your server size

# Setup Redis for caching (optional)
sudo apt install redis-server -y
# Update .env: CACHE_DRIVER=redis
```

---

## Quick Deployment Summary

For a quick deployment, the system is designed to work by simply:

1. **Upload files** to your hosting provider
2. **Create production database**
3. **Update .env file** with production settings
4. **Run migrations**: `php artisan migrate --seed`
5. **Set permissions** and configure web server
6. **Install SSL certificate**
7. **Test all functionality**

The Lead Tracking CRM is now ready for production use with webhook integrations for Pabbly Connect and Monday.com!

---

**Support**: For deployment issues, check the Laravel logs and ensure all requirements are met. The system is designed to be deployment-friendly with minimal configuration changes needed.
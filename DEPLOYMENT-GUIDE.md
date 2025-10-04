# Lead Tracking System - Deployment Guide

## Overview
This guide provides step-by-step instructions for deploying the Lead Tracking System to a production environment.

## Prerequisites

### Server Requirements
- **PHP**: 8.1 or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Composer**: Latest version
- **SSL Certificate**: Required for production

### PHP Extensions Required
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD or Imagick (for image processing)
- Zip

## Deployment Steps

### 1. Server Preparation

#### Upload Files
1. Upload all application files to your web server
2. Ensure the document root points to the `public` directory
3. Keep application files outside the web-accessible directory for security

#### Set File Permissions

**For Linux/Unix servers:**
```bash
# Run the provided permission script
chmod +x setup-permissions.sh
./setup-permissions.sh
```

**For Windows servers:**
```cmd
# Run as Administrator
setup-permissions.bat
```

### 2. Environment Configuration

#### Create Production .env File
1. Copy `.env.example` to `.env`
2. Configure the following critical settings:

```env
# Application
APP_NAME="Lead Tracking System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Generate a new application key
APP_KEY=base64:YOUR_32_CHARACTER_KEY_HERE

# Database
DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email_username
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Lead Tracking System"

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_ENCRYPT=true

# Cache
CACHE_PREFIX=lead_tracking_cache_

# Webhook (if using)
WEBHOOK_TOKEN=your_secure_webhook_token
```

#### Generate Application Key
```bash
php artisan key:generate
```

### 3. Database Setup

#### Run Migrations
```bash
# Run all database migrations
php artisan migrate --force

# Verify migration status
php artisan migrate:status
```

#### Create Admin User (Optional)
```bash
# If you have seeders for admin user
php artisan db:seed --class=AdminUserSeeder
```

### 4. Optimize for Production

#### Install Dependencies
```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Or use the production deploy script
composer run production-deploy
```

#### Cache Configuration
```bash
# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### Create Storage Link
```bash
php artisan storage:link
```

### 5. Web Server Configuration

#### Apache Configuration
Ensure `.htaccess` files are present and mod_rewrite is enabled:

```apache
# In public/.htaccess (should already exist)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    listen 443 ssl;
    server_name yourdomain.com;
    root /path/to/your/app/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
}
```

### 6. SSL Certificate

#### Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Generate certificate
sudo certbot --apache -d yourdomain.com
```

#### Commercial SSL
1. Purchase SSL certificate from a trusted CA
2. Install certificate on your web server
3. Configure HTTPS redirects

### 7. Security Hardening

#### File Security
- Ensure `.env` file is not web-accessible
- Remove any unnecessary files (README.md, etc.)
- Set proper file permissions (see step 1)

#### Database Security
- Use a dedicated database user with minimal privileges
- Enable SSL connections to database
- Regular security updates

#### Server Security
- Keep server software updated
- Configure firewall
- Disable unnecessary services
- Regular security monitoring

### 8. Monitoring and Logging

#### Log Configuration
```env
# In .env file
LOG_CHANNEL=daily
LOG_LEVEL=error
```

#### Set up Log Rotation
```bash
# Add to /etc/logrotate.d/laravel
/path/to/your/app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
```

### 9. Backup Strategy

#### Database Backups
```bash
# Create automated backup script
#!/bin/bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

#### File Backups
- Regular backups of application files
- Include uploaded files and storage
- Test restore procedures

### 10. Performance Optimization

#### Enable OPcache
```ini
# In php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
```

#### Database Optimization
- Regular database maintenance
- Index optimization
- Query performance monitoring

## Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] Database connection working
- [ ] User registration/login functional
- [ ] Email sending working
- [ ] File uploads working
- [ ] All forms submitting correctly
- [ ] SSL certificate installed and working
- [ ] Security headers configured
- [ ] Backups configured
- [ ] Monitoring set up
- [ ] Performance optimizations applied
- [ ] Error pages customized
- [ ] Webhook endpoints working (if applicable)

## Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check Laravel logs: `storage/logs/laravel.log`
- Verify file permissions
- Check `.env` configuration
- Ensure all required PHP extensions are installed

#### Database Connection Issues
- Verify database credentials in `.env`
- Check database server accessibility
- Ensure database exists and user has proper privileges

#### Permission Denied Errors
- Run permission setup scripts
- Check web server user ownership
- Verify storage directory permissions

#### Cache Issues
- Clear all caches: `php artisan cache:clear`
- Clear configuration cache: `php artisan config:clear`
- Rebuild caches for production

### Support
For additional support, check:
- Application logs in `storage/logs/`
- Web server error logs
- PHP error logs
- Database logs

## Maintenance

### Regular Tasks
- Monitor application logs
- Update dependencies (test in staging first)
- Database maintenance and optimization
- Security updates
- Backup verification
- Performance monitoring

### Updates
1. Test updates in staging environment
2. Backup production data
3. Put application in maintenance mode
4. Deploy updates
5. Run migrations if needed
6. Clear and rebuild caches
7. Test functionality
8. Remove maintenance mode

---

**Note**: Always test deployment procedures in a staging environment before applying to production.
# Production Security Configuration

## Critical Security Settings

### Environment Variables (.env)
Ensure these settings are properly configured for production:

```env
# Application Security
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_32_CHARACTER_KEY_HERE

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_ENCRYPT=true

# Database Security
DB_HOST=your_secure_db_host
DB_USERNAME=limited_privilege_user
DB_PASSWORD=strong_random_password

# Cache Security
CACHE_PREFIX=your_app_name_cache_

# Mail Security
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# Logging
LOG_LEVEL=error
```

### Web Server Configuration

#### Apache (.htaccess)
- Ensure `.htaccess` files are properly configured
- Block access to sensitive directories
- Enable HTTPS redirects

#### Nginx
- Configure proper SSL/TLS settings
- Block access to sensitive files
- Set proper headers

### File Permissions
```bash
# Set proper permissions
chmod -R 755 /path/to/your/app
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /path/to/your/app
```

### Security Headers
Ensure your web server sends these headers:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security: max-age=31536000; includeSubDomains`

### Database Security
- Use a dedicated database user with minimal privileges
- Enable SSL connections to database
- Regular backups with encryption
- Keep database software updated

### Additional Security Measures
1. **SSL Certificate**: Install valid SSL certificate
2. **Firewall**: Configure server firewall
3. **Updates**: Keep all software updated
4. **Monitoring**: Set up security monitoring
5. **Backups**: Implement automated backups
6. **Rate Limiting**: Configure rate limiting
7. **CSRF Protection**: Enabled by default in Laravel
8. **SQL Injection**: Use Eloquent ORM (already implemented)

### Pre-Deployment Checklist
- [ ] APP_DEBUG=false
- [ ] Strong APP_KEY generated
- [ ] Database credentials secured
- [ ] HTTPS enabled
- [ ] Session cookies secured
- [ ] File permissions set correctly
- [ ] Error pages customized
- [ ] Logging configured
- [ ] Backup strategy implemented
- [ ] Security headers configured
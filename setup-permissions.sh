#!/bin/bash
# File Permissions Setup Script for Lead Tracking System
# Run this script on your production server after deployment

echo "Setting up file permissions for Lead Tracking System..."

# Get the application directory (assuming script is run from app root)
APP_DIR=$(pwd)

# Set ownership to web server user (adjust as needed for your server)
# Common web server users: www-data (Ubuntu/Debian), apache (CentOS/RHEL), nginx
WEB_USER="www-data"

echo "Setting ownership to $WEB_USER..."
sudo chown -R $WEB_USER:$WEB_USER $APP_DIR

# Set base permissions
echo "Setting base directory permissions..."
sudo find $APP_DIR -type d -exec chmod 755 {} \;
sudo find $APP_DIR -type f -exec chmod 644 {} \;

# Set writable permissions for Laravel directories
echo "Setting writable permissions for storage and cache directories..."
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Ensure .env file is not publicly readable
if [ -f "$APP_DIR/.env" ]; then
    echo "Securing .env file..."
    sudo chmod 600 $APP_DIR/.env
fi

# Set executable permissions for artisan
sudo chmod +x $APP_DIR/artisan

# Create storage subdirectories if they don't exist
echo "Creating storage subdirectories..."
sudo mkdir -p $APP_DIR/storage/app/public
sudo mkdir -p $APP_DIR/storage/framework/cache/data
sudo mkdir -p $APP_DIR/storage/framework/sessions
sudo mkdir -p $APP_DIR/storage/framework/views
sudo mkdir -p $APP_DIR/storage/logs

# Set proper permissions for storage subdirectories
sudo chmod -R 775 $APP_DIR/storage/app
sudo chmod -R 775 $APP_DIR/storage/framework
sudo chmod -R 775 $APP_DIR/storage/logs

# Create symbolic link for public storage (if not exists)
if [ ! -L "$APP_DIR/public/storage" ]; then
    echo "Creating storage symbolic link..."
    sudo -u $WEB_USER php $APP_DIR/artisan storage:link
fi

echo "File permissions setup completed!"
echo ""
echo "Summary of permissions set:"
echo "- Application files: 644 (files) / 755 (directories)"
echo "- Storage directory: 775 (writable by web server)"
echo "- Bootstrap/cache: 775 (writable by web server)"
echo "- .env file: 600 (readable only by owner)"
echo "- Artisan: executable"
echo ""
echo "Note: Adjust WEB_USER variable in this script if your web server uses a different user."
echo "Common web server users: www-data (Ubuntu/Debian), apache (CentOS/RHEL), nginx"
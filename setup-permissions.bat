@echo off
REM File Permissions Setup Script for Lead Tracking System (Windows)
REM Run this script as Administrator on your Windows production server

echo Setting up file permissions for Lead Tracking System on Windows...

REM Get the current directory (application root)
set APP_DIR=%~dp0

echo Application directory: %APP_DIR%

REM Create storage subdirectories if they don't exist
echo Creating storage subdirectories...
if not exist "%APP_DIR%storage\app\public" mkdir "%APP_DIR%storage\app\public"
if not exist "%APP_DIR%storage\framework\cache\data" mkdir "%APP_DIR%storage\framework\cache\data"
if not exist "%APP_DIR%storage\framework\sessions" mkdir "%APP_DIR%storage\framework\sessions"
if not exist "%APP_DIR%storage\framework\views" mkdir "%APP_DIR%storage\framework\views"
if not exist "%APP_DIR%storage\logs" mkdir "%APP_DIR%storage\logs"

REM Set permissions for IIS application pool identity
REM Replace "IIS_IUSRS" with your specific application pool identity if different
echo Setting permissions for IIS_IUSRS...

REM Grant full control to storage directory
icacls "%APP_DIR%storage" /grant "IIS_IUSRS:(OI)(CI)F" /T

REM Grant full control to bootstrap/cache directory
icacls "%APP_DIR%bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T

REM Grant read permissions to application files
icacls "%APP_DIR%" /grant "IIS_IUSRS:(OI)(CI)R" /T

REM Remove inheritance and set specific permissions for .env file (if exists)
if exist "%APP_DIR%.env" (
    echo Securing .env file...
    icacls "%APP_DIR%.env" /inheritance:r
    icacls "%APP_DIR%.env" /grant "Administrators:F"
    icacls "%APP_DIR%.env" /grant "SYSTEM:F"
)

REM Create symbolic link for public storage (requires PHP artisan)
echo Creating storage symbolic link...
php "%APP_DIR%artisan" storage:link

echo.
echo File permissions setup completed!
echo.
echo Summary of permissions set:
echo - Storage directory: Full control for IIS_IUSRS
echo - Bootstrap/cache: Full control for IIS_IUSRS
echo - Application files: Read access for IIS_IUSRS
echo - .env file: Restricted access (Administrators only)
echo - Storage symbolic link created
echo.
echo Note: Adjust permissions as needed for your specific IIS configuration.
echo For shared hosting, contact your hosting provider for permission setup.

pause
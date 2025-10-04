<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Ensure cache directories exist in serverless environments
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || 
    (isset($_ENV['AWS_LAMBDA_FUNCTION_NAME']) || isset($_SERVER['AWS_LAMBDA_FUNCTION_NAME']))) {
    
    // Create all necessary cache directories
    $directories = [
        '/tmp/bootstrap/cache',
        '/tmp/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Set cache paths for serverless environment
    $_ENV['APP_SERVICES_CACHE'] = $_ENV['APP_SERVICES_CACHE'] ?? '/tmp/bootstrap/cache/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = $_ENV['APP_PACKAGES_CACHE'] ?? '/tmp/bootstrap/cache/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = $_ENV['APP_CONFIG_CACHE'] ?? '/tmp/bootstrap/cache/config.php';
    $_ENV['APP_ROUTES_CACHE'] = $_ENV['APP_ROUTES_CACHE'] ?? '/tmp/bootstrap/cache/routes-v7.php';
    $_ENV['APP_EVENTS_CACHE'] = $_ENV['APP_EVENTS_CACHE'] ?? '/tmp/bootstrap/cache/events.php';
    $_ENV['VIEW_COMPILED_PATH'] = $_ENV['VIEW_COMPILED_PATH'] ?? '/tmp/views';
    
    // Also set storage paths
    $_ENV['CACHE_PATH'] = $_ENV['CACHE_PATH'] ?? '/tmp/storage/framework/cache';
    // Use distinct env variables for session files vs cookie path to avoid conflicts in serverless
    $_ENV['SESSION_FILES_PATH'] = $_ENV['SESSION_FILES_PATH'] ?? '/tmp/storage/framework/sessions';
    $_ENV['SESSION_COOKIE_PATH'] = $_ENV['SESSION_COOKIE_PATH'] ?? '/';
    $_ENV['LOG_PATH'] = $_ENV['LOG_PATH'] ?? '/tmp/storage/logs';

    // Fallback: if APP_KEY is missing, generate a temporary key persisted in /tmp for the lifetime of the instance
    if ((empty($_ENV['APP_KEY']) && empty(getenv('APP_KEY')))) {
        $tmpKeyFile = '/tmp/app_key';
        $key = null;
        if (is_file($tmpKeyFile)) {
            $key = trim((string) file_get_contents($tmpKeyFile));
        }
        if (!$key) {
            try {
                $random = base64_encode(random_bytes(32));
                $key = 'base64:' . $random;
                file_put_contents($tmpKeyFile, $key);
            } catch (\Throwable $e) {
                // As an absolute last resort, use a predictable fallback to avoid fatal errors
                $key = 'base64:' . base64_encode(str_pad('', 32, '0'));
            }
        }
        $_ENV['APP_KEY'] = $key;
        putenv('APP_KEY='.$key);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

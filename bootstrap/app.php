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
    $_ENV['SESSION_PATH'] = $_ENV['SESSION_PATH'] ?? '/tmp/storage/framework/sessions';
    $_ENV['LOG_PATH'] = $_ENV['LOG_PATH'] ?? '/tmp/storage/logs';
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

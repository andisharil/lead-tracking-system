<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;

$app = Application::configure(basePath: dirname(__DIR__))
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
    })
    // Explicitly load providers to ensure the view system is available in serverless runtime
    ->withProviders(require __DIR__ . '/providers.php')
    ->create();

// Register critical providers immediately after app creation to guarantee 'view' binding in serverless environments
$app->register(FilesystemServiceProvider::class);
$app->register(ViewServiceProvider::class);

// Ensure compiled Blade views are written to a writable location in serverless environments
$envCompiled = getenv('VIEW_COMPILED_PATH') ?: null;
$storageCompiled = rtrim($app->storagePath(), '\\/') . '/framework/views';
$targetCompiled = $envCompiled ?: $storageCompiled;

// Create directory if missing and fallback to system temp when not writable
if (!@is_dir($targetCompiled)) {
    @mkdir($targetCompiled, 0777, true);
}
if (!@is_writable($targetCompiled)) {
    $tmpCompiled = rtrim(sys_get_temp_dir(), '\\/') . '/views';
    if (!@is_dir($tmpCompiled)) {
        @mkdir($tmpCompiled, 0777, true);
    }
    $targetCompiled = $tmpCompiled;
}

// Apply configuration so Blade uses the writable compiled path
$app->make('config')->set('view.compiled', $targetCompiled);

return $app;

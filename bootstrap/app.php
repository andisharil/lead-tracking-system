<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
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

// Prefer a writable compiled views directory (env or fallback)
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

// Make sure the environment knows the compiled path before providers use config
putenv('VIEW_COMPILED_PATH=' . $targetCompiled);
$_ENV['VIEW_COMPILED_PATH'] = $targetCompiled;
$_SERVER['VIEW_COMPILED_PATH'] = $targetCompiled;

// Register critical providers after ensuring compiled path env is set
$app->register(FilesystemServiceProvider::class);
$app->register(ViewServiceProvider::class);
$app->register(TranslationServiceProvider::class);

return $app;

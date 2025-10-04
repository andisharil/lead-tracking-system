<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine application base path to support cPanel Option B (copy public/ into public_html)
// This attempts common relative paths, and if not found, it scans sibling directories of public_html
// to locate a directory that contains vendor/autoload.php and bootstrap/app.php

function findBasePath(string $startDir): ?string {
    $candidates = [
        $startDir . '/../',               // parent of public_html
        dirname($startDir, 2) . '/',      // grandparent
        dirname($startDir, 3) . '/',      // great-grandparent
    ];

    foreach ($candidates as $candidate) {
        if (file_exists($candidate . 'vendor/autoload.php') && file_exists($candidate . 'bootstrap/app.php')) {
            return rtrim($candidate, '/') . '/';
        }
    }

    // Scan immediate subdirectories of the parent directory for a Laravel base
    $parent = realpath($startDir . '/..');
    if ($parent && is_dir($parent)) {
        foreach (glob($parent . '/*', GLOB_ONLYDIR) as $dir) {
            $dir = rtrim($dir, '/') . '/';
            if (file_exists($dir . 'vendor/autoload.php') && file_exists($dir . 'bootstrap/app.php')) {
                return $dir;
            }
        }
    }

    return null;
}

$basePath = findBasePath(__DIR__);

if ($basePath === null) {
    http_response_code(500);
    echo 'Application files not found. Please verify server paths.';
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($basePath . 'storage/framework/maintenance.php')) {
    require $basePath . 'storage/framework/maintenance.php';
}

// Register the Composer autoloader...
require $basePath . 'vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath . 'bootstrap/app.php';

$app->handleRequest(Request::capture());

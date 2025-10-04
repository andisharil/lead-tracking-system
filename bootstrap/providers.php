<?php

return [
    // Ensure Laravel's page rendering (views) system is loaded
    Illuminate\View\ViewServiceProvider::class,
    // Supporting filesystem provider used by the view system
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    // Enable translations used by error pages and UI strings
    Illuminate\Translation\TranslationServiceProvider::class,

    // Application-specific providers
    App\Providers\AppServiceProvider::class,
];

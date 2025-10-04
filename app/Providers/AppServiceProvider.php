<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ensure Laravel's view system is available early in serverless environments (e.g., Vercel)
        // This prevents "Target class [view] does not exist" errors when exceptions try to render views
        $this->app->register(FilesystemServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Check if database connection is available and tables exist
            DB::connection()->getPdo();
            
            // Check if settings table exists before trying to query it
            if (!DB::getSchemaBuilder()->hasTable('settings')) {
                Log::warning('Settings table does not exist, skipping configuration override');
                return;
            }
            
            $settings = Cache::remember('app_settings_config', 3600, function () {
                $rows = DB::table('settings')->get();
                $grouped = [];
                foreach ($rows as $row) {
                    $parts = explode('.', $row->key);
                    $category = $parts[0] ?? 'general';
                    $key = $parts[1] ?? $row->key;
                    if (!isset($grouped[$category])) {
                        $grouped[$category] = [];
                    }
                    $grouped[$category][$key] = $row->value;
                }
                return $grouped;
            });

            // Apply config overrides based on settings
            if (!empty($settings['general']['app_name'])) {
                Config::set('app.name', $settings['general']['app_name']);
            }
            if (!empty($settings['general']['language'])) {
                Config::set('app.locale', $settings['general']['language']);
            }
            if (!empty($settings['general']['timezone'])) {
                Config::set('app.timezone', $settings['general']['timezone']);
            }
            if (isset($settings['general']['app_debug'])) {
                Config::set('app.debug', (bool) $settings['general']['app_debug']);
            }
            if (!empty($settings['general']['session_lifetime'])) {
                Config::set('session.lifetime', (int) $settings['general']['session_lifetime']);
            }

            // Mail settings
            if (!empty($settings['email']['mail_host'])) {
                Config::set('mail.mailers.smtp.host', $settings['email']['mail_host']);
            }
            if (!empty($settings['email']['mail_port'])) {
                Config::set('mail.mailers.smtp.port', (int) $settings['email']['mail_port']);
            }
            if (!empty($settings['email']['mail_username'])) {
                Config::set('mail.mailers.smtp.username', $settings['email']['mail_username']);
            }
            if (!empty($settings['email']['mail_password'])) {
                Config::set('mail.mailers.smtp.password', $settings['email']['mail_password']);
            }
            if (!empty($settings['email']['mail_encryption'])) {
                Config::set('mail.mailers.smtp.encryption', $settings['email']['mail_encryption']);
            }
            if (!empty($settings['email']['mail_from_address'])) {
                Config::set('mail.from.address', $settings['email']['mail_from_address']);
            }
            if (!empty($settings['email']['mail_from_name'])) {
                Config::set('mail.from.name', $settings['email']['mail_from_name']);
            }
        } catch (\Throwable $e) {
            Log::warning('AppServiceProvider boot setup skipped due to error: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = $this->getAllSettings();
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'currency' => 'required|string|max:10',
            'language' => 'required|string|max:10',
            'items_per_page' => 'required|integer|min:5|max:100',
            'session_timeout' => 'required|integer|min:15|max:1440',
            'maintenance_mode' => 'boolean',
            'debug_mode' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $settings = $request->only([
                'app_name', 'app_description', 'timezone', 'date_format',
                'time_format', 'currency', 'language', 'items_per_page',
                'session_timeout', 'maintenance_mode', 'debug_mode'
            ]);

            // Normalize checkbox values so they can be turned OFF
            // If the checkbox is unchecked, it won't be present in the request.
            // We explicitly set both flags using boolean() to ensure 1/0 is stored.
            $settings['maintenance_mode'] = (int) $request->boolean('maintenance_mode');
            $settings['debug_mode'] = (int) $request->boolean('debug_mode');

            foreach ($settings as $key => $value) {
                $this->updateSetting('general.' . $key, $value);
            }

            // Clear cache
            Cache::forget('app_settings');
            Cache::forget('app_settings_config');

            // Extra diagnostic log to trace which logging path is used
            Log::info('SettingsController::updateGeneral about to log activity (no data column expected).', [
                'module' => 'settings',
                'action' => 'general_settings_updated',
                'has_data' => !empty($settings),
            ]);

            // Log activity
            $this->logActivity('general_settings_updated', 'General settings updated', $settings);

            DB::commit();

            return redirect()->back()->with('success', 'General settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating general settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update general settings.');
        }
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string|max:255',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'notification_emails' => 'boolean',
            'lead_notifications' => 'boolean',
            'campaign_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $settings = $request->only([
                'mail_driver', 'mail_host', 'mail_port', 'mail_username',
                'mail_password', 'mail_encryption', 'mail_from_address',
                'mail_from_name', 'notification_emails', 'lead_notifications',
                'campaign_notifications', 'system_notifications'
            ]);

            foreach ($settings as $key => $value) {
                $this->updateSetting('email.' . $key, $value);
            }

            // Clear cache
            Cache::forget('app_settings');
            Cache::forget('app_settings_config');

            // Log activity
            $this->logActivity('email_settings_updated', 'Email settings updated', Arr::except($settings, ['mail_password']));

            DB::commit();

            return redirect()->back()->with('success', 'Email settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating email settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update email settings.');
        }
    }

    /**
     * Update integration settings
     */
    public function updateIntegrations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'webhook_url' => 'nullable|url|max:500',
            'webhook_secret' => 'nullable|string|max:255',
            'webhook_events' => 'array',
            'webhook_events.*' => 'string|in:lead_created,lead_updated,lead_deleted,campaign_created,campaign_updated',
            'api_rate_limit' => 'required|integer|min:10|max:10000',
            'api_timeout' => 'required|integer|min:5|max:300',
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'zapier_webhook' => 'nullable|url|max:500',
            'slack_webhook' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $settings = $request->only([
                'webhook_url', 'webhook_secret', 'webhook_events',
                'api_rate_limit', 'api_timeout', 'google_analytics_id',
                'facebook_pixel_id', 'zapier_webhook', 'slack_webhook'
            ]);

            // Convert webhook_events array to JSON
            if (isset($settings['webhook_events'])) {
                $settings['webhook_events'] = json_encode($settings['webhook_events']);
            }

            foreach ($settings as $key => $value) {
                $this->updateSetting('integrations.' . $key, $value);
            }

            // Clear cache
            Cache::forget('app_settings');
            Cache::forget('app_settings_config');

            // Log activity
            $this->logActivity('integrations_settings_updated', 'Integration settings updated', Arr::except($settings, ['webhook_secret']));

            DB::commit();

            return redirect()->back()->with('success', 'Integration settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating integration settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update integration settings.');
        }
    }

    /**
     * Export settings
     */
    public function exportSettings()
    {
        try {
            $settings = $this->getAllSettings();
            // Ensure latest values by clearing config cache as well
            Cache::forget('app_settings_config');
            
            // Remove sensitive data
            unset($settings['email']['mail_password']);
            unset($settings['integrations']['webhook_secret']);
            
            $filename = 'settings_export_' . date('Y-m-d_H-i-s') . '.json';
            
            // Log activity
            $this->logActivity('settings_exported', 'Settings exported');
            
            return response()->json($settings)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            Log::error('Error exporting settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export settings.');
        }
    }

    /**
     * Get all settings grouped by category
     */
    private function getAllSettings()
    {
        return Cache::remember('app_settings', 3600, function () {
            $settings = DB::table('settings')->get();
            
            $grouped = [];
            foreach ($settings as $setting) {
                $keys = explode('.', $setting->key);
                $category = $keys[0];
                $key = $keys[1] ?? $setting->key;
                
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                
                $grouped[$category][$key] = $setting->value;
            }
            
            return $grouped;
        });
    }

    /**
     * Update a single setting
     */
    private function updateSetting($key, $value)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    /**
     * Log activity
     */
    private function logActivity($action, $description, $data = null)
    {
        try {
            if (!Schema::hasTable('activity_logs')) {
                // Skip logging if the table doesn't exist to avoid breaking settings save
                Log::warning("Skipping activity log: table 'activity_logs' not found", [
                    'module' => 'settings',
                    'action' => $action,
                ]);
                return;
            }

            // Align with activity_logs table columns. No 'data' column exists.
            // If extra data is provided, append it to the description for context.
            $fullDescription = $description;
            if ($data !== null) {
                $encoded = json_encode($data);
                $fullDescription = $description . ' | Details: ' . ($encoded ?: '');
            }

            // Diagnostic log to confirm this path executes and uses no 'data' column
            Log::info('SettingsController::logActivity writing to activity_logs without data column.', [
                'module' => 'settings',
                'action' => $action,
                'has_data' => $data !== null,
                'description' => $fullDescription,
            ]);

            DB::table('activity_logs')->insert([
                'user_id' => Auth::user()?->id,
                'module' => 'settings',
                'action' => $action,
                'description' => $fullDescription,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Do not interrupt the main operation if logging fails
            Log::error('Failed to write activity log: ' . $e->getMessage(), [
                'module' => 'settings',
                'action' => $action,
            ]);
        }
    }

    public function clearCache()
    {
        try {
            Cache::forget('app_settings');
            Cache::forget('app_settings_config');
            return redirect()->back()->with('success', 'Application cache cleared.');
        } catch (\Throwable $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear cache.');
        }
    }
}
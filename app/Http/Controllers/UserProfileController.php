<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $activityLogs = $this->getRecentActivity($user->id);
        $profileStats = $this->getProfileStats($user->id);
        
        return view('user-profile.index', compact('user', 'activityLogs', 'profileStats'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('user-profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'bio' => $request->bio,
                'timezone' => $request->timezone ?? config('app.timezone'),
                'language' => $request->language ?? 'en'
            ];
            
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('uploads/avatars'), $avatarName);
                $updateData['avatar'] = 'uploads/avatars/' . $avatarName;
                
                // Delete old avatar if exists
                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    unlink(public_path($user->avatar));
                }
            }
            
            $user->update($updateData);
            
            // Log activity
            $this->logActivity($user->id, 'profile_updated', 'Profile information updated');
            
            return redirect()->route('user-profile.index')->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
    
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator, 'password');
        }
        
        $user = Auth::user();
        
        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'], 'password');
        }
        
        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            
            // Log activity
            $this->logActivity($user->id, 'password_changed', 'Password changed successfully');
            
            return back()->with('password_success', 'Password changed successfully!');
            
        } catch (\Exception $e) {
            return back()->with('password_error', 'Failed to change password: ' . $e->getMessage());
        }
    }
    
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'lead_notifications' => 'boolean',
            'campaign_notifications' => 'boolean',
            'report_notifications' => 'boolean',
            'system_notifications' => 'boolean',
            'notification_frequency' => 'required|in:instant,daily,weekly,never'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator, 'notifications');
        }
        
        try {
            $notificationSettings = [
                'email_notifications' => $request->boolean('email_notifications'),
                'lead_notifications' => $request->boolean('lead_notifications'),
                'campaign_notifications' => $request->boolean('campaign_notifications'),
                'report_notifications' => $request->boolean('report_notifications'),
                'system_notifications' => $request->boolean('system_notifications'),
                'notification_frequency' => $request->notification_frequency
            ];
            
            $user->update([
                'notification_settings' => json_encode($notificationSettings)
            ]);
            
            // Log activity
            $this->logActivity($user->id, 'notifications_updated', 'Notification preferences updated');
            
            return back()->with('notifications_success', 'Notification preferences updated successfully!');
            
        } catch (\Exception $e) {
            return back()->with('notifications_error', 'Failed to update notifications: ' . $e->getMessage());
        }
    }
    
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'dashboard_layout' => 'required|in:grid,list,compact',
            'items_per_page' => 'required|integer|min:10|max:100',
            'date_format' => 'required|in:Y-m-d,m/d/Y,d/m/Y,d-m-Y',
            'time_format' => 'required|in:12,24',
            'currency' => 'required|string|max:3',
            'auto_refresh' => 'boolean',
            'show_tooltips' => 'boolean',
            'compact_sidebar' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator, 'preferences');
        }
        
        try {
            $preferences = [
                'dashboard_layout' => $request->dashboard_layout,
                'items_per_page' => $request->items_per_page,
                'date_format' => $request->date_format,
                'time_format' => $request->time_format,
                'currency' => $request->currency,
                'auto_refresh' => $request->boolean('auto_refresh'),
                'show_tooltips' => $request->boolean('show_tooltips'),
                'compact_sidebar' => $request->boolean('compact_sidebar')
            ];
            
            $user->update([
                'preferences' => json_encode($preferences)
            ]);
            
            // Log activity
            $this->logActivity($user->id, 'preferences_updated', 'User preferences updated');
            
            return back()->with('preferences_success', 'Preferences updated successfully!');
            
        } catch (\Exception $e) {
            return back()->with('preferences_error', 'Failed to update preferences: ' . $e->getMessage());
        }
    }
    
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirmation' => 'required|in:DELETE'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator, 'delete');
        }
        
        $user = Auth::user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.'], 'delete');
        }
        
        try {
            // Log activity before deletion
            $this->logActivity($user->id, 'account_deleted', 'User account deleted');
            
            // Delete avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            
            // Logout and delete user
            Auth::logout();
            $user->delete();
            
            $redirect = Route::has('login') ? redirect()->route('login') : redirect('/');
            return $redirect->with('success', 'Your account has been deleted successfully.');
            
        } catch (\Exception $e) {
            return back()->with('delete_error', 'Failed to delete account: ' . $e->getMessage());
        }
    }
    
    public function exportData()
    {
        $user = Auth::user();
        
        try {
            $userData = [
                'profile' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $user->company,
                    'position' => $user->position,
                    'bio' => $user->bio,
                    'timezone' => $user->timezone,
                    'language' => $user->language,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ],
                'preferences' => json_decode($user->preferences ?? '{}', true),
                'notification_settings' => json_decode($user->notification_settings ?? '{}', true),
                'activity_logs' => $this->getRecentActivity($user->id, 100)
            ];
            
            $filename = 'user_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            // Log activity
            $this->logActivity($user->id, 'data_exported', 'User data exported');
            
            return response()->json($userData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }
    
    private function getRecentActivity($userId, $limit = 10)
    {
        // This would typically come from an activity_logs table
        // For now, we'll return sample data
        return collect([
            [
                'id' => 1,
                'action' => 'profile_updated',
                'description' => 'Profile information updated',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subHours(2)
            ],
            [
                'id' => 2,
                'action' => 'password_changed',
                'description' => 'Password changed successfully',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(1)
            ],
            [
                'id' => 3,
                'action' => 'login',
                'description' => 'User logged in',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(2)
            ]
        ])->take($limit);
    }
    
    private function getProfileStats($userId)
    {
        // This would typically calculate real stats from the database
        return [
            'total_leads' => 156,
            'converted_leads' => 23,
            'campaigns_created' => 8,
            'reports_generated' => 12,
            'last_login' => now()->subHours(3),
            'account_age_days' => now()->diffInDays(Auth::user()->created_at),
            'profile_completion' => $this->calculateProfileCompletion(Auth::user())
        ];
    }
    
    private function calculateProfileCompletion($user)
    {
        $fields = ['name', 'email', 'phone', 'company', 'position', 'bio', 'avatar'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }
    
    private function logActivity($userId, $action, $description)
    {
        // This would typically save to an activity_logs table
        Log::info('User activity', [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
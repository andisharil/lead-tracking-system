@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-gray-600 mt-1 text-sm sm:text-base">Update your account information and preferences</p>
        </div>
        <a href="{{ route('user-profile.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-200 flex items-center touch-target text-sm sm:text-base w-full sm:w-auto justify-center">
            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="hidden sm:inline">Back to Profile</span><span class="sm:hidden">Back</span>
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('success') }}
        </div>
    @endif

    @if(session('password_success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('password_success') }}
        </div>
    @endif

    @if(session('notifications_success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('notifications_success') }}
        </div>
    @endif

    @if(session('preferences_success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('preferences_success') }}
        </div>
    @endif

    <div class="space-y-4 sm:space-y-6">
        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Profile Information</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Update your basic profile information</p>
            </div>
            <form action="{{ route('user-profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Avatar Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                            <div class="shrink-0">
                                @if($user->avatar)
                                    <img id="avatar-preview" src="{{ asset($user->avatar) }}" alt="Current Avatar" class="h-16 w-16 sm:h-20 sm:w-20 object-cover rounded-full">
                                @else
                                    <div id="avatar-preview" class="h-16 w-16 sm:h-20 sm:w-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg sm:text-xl font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="w-full sm:w-auto">
                                <input type="file" name="avatar" id="avatar" accept="image/*" class="sr-only" onchange="previewAvatar(this)">
                                <label for="avatar" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 touch-target inline-block w-full sm:w-auto text-center">
                                    Change Avatar
                                </label>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company -->
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                        <input type="text" name="company" id="company" value="{{ old('company', $user->company) }}" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('company')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('position')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <select name="timezone" id="timezone" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="UTC" {{ old('timezone', $user->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ old('timezone', $user->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ old('timezone', $user->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="Europe/London" {{ old('timezone', $user->timezone) == 'Europe/London' ? 'selected' : '' }}>London</option>
                            <option value="Europe/Paris" {{ old('timezone', $user->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                            <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                            <option value="Asia/Kuala_Lumpur" {{ old('timezone', $user->timezone) == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>GMT+8 (Kuala Lumpur)</option>
                        </select>
                        @error('timezone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea name="bio" id="bio" rows="3" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target resize-y" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-4 sm:mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition duration-200 text-sm sm:text-base touch-target w-full sm:w-auto">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Change Password</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Update your account password</p>
            </div>
            <form action="{{ route('user-profile.change-password') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                
                @if(session('password_error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('password_error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Current Password -->
                    <div class="md:col-span-2">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                        <input type="password" name="current_password" id="current_password" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('current_password', 'password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                        <input type="password" name="new_password" id="new_password" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                        @error('new_password', 'password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters with mixed case, numbers, and symbols</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                    </div>
                </div>

                <div class="flex justify-end mt-4 sm:mt-6">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition duration-200 text-sm sm:text-base touch-target w-full sm:w-auto">
                        Change Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Notification Preferences</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Choose what notifications you want to receive</p>
            </div>
            <form action="{{ route('user-profile.update-notifications') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                
                @if(session('notifications_error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('notifications_error') }}
                    </div>
                @endif

                @php
                    $notificationSettings = json_decode($user->notification_settings ?? '{}', true);
                @endphp

                <div class="space-y-3 sm:space-y-4">
                    <!-- Email Notifications -->
                    <div class="flex items-start sm:items-center justify-between py-2">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="text-sm font-medium text-gray-900 block">Email Notifications</h4>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Receive notifications via email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 touch-target">
                            <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ ($notificationSettings['email_notifications'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Lead Notifications -->
                    <div class="flex items-start sm:items-center justify-between py-2">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="text-sm font-medium text-gray-900 block">Lead Notifications</h4>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Get notified about new leads and updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 touch-target">
                            <input type="checkbox" name="lead_notifications" value="1" class="sr-only peer" {{ ($notificationSettings['lead_notifications'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Campaign Notifications -->
                    <div class="flex items-start sm:items-center justify-between py-2">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="text-sm font-medium text-gray-900 block">Campaign Notifications</h4>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Updates about campaign performance</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 touch-target">
                            <input type="checkbox" name="campaign_notifications" value="1" class="sr-only peer" {{ ($notificationSettings['campaign_notifications'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Report Notifications -->
                    <div class="flex items-start sm:items-center justify-between py-2">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="text-sm font-medium text-gray-900 block">Report Notifications</h4>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Scheduled reports and analytics updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 touch-target">
                            <input type="checkbox" name="report_notifications" value="1" class="sr-only peer" {{ ($notificationSettings['report_notifications'] ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- System Notifications -->
                    <div class="flex items-start sm:items-center justify-between py-2">
                        <div class="flex-1 min-w-0 pr-3">
                            <h4 class="text-sm font-medium text-gray-900 block">System Notifications</h4>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">System updates and maintenance alerts</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 touch-target">
                            <input type="checkbox" name="system_notifications" value="1" class="sr-only peer" {{ ($notificationSettings['system_notifications'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Notification Frequency -->
                    <div class="pt-3 sm:pt-4 border-t border-gray-200">
                        <label for="notification_frequency" class="block text-sm font-medium text-gray-700 mb-2">Notification Frequency</label>
                        <select name="notification_frequency" id="notification_frequency" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="instant" {{ ($notificationSettings['notification_frequency'] ?? 'instant') == 'instant' ? 'selected' : '' }}>Instant</option>
                            <option value="daily" {{ ($notificationSettings['notification_frequency'] ?? 'instant') == 'daily' ? 'selected' : '' }}>Daily Digest</option>
                            <option value="weekly" {{ ($notificationSettings['notification_frequency'] ?? 'instant') == 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                            <option value="never" {{ ($notificationSettings['notification_frequency'] ?? 'instant') == 'never' ? 'selected' : '' }}>Never</option>
                        </select>
                        @error('notification_frequency', 'notifications')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-4 sm:mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition duration-200 text-sm sm:text-base touch-target w-full sm:w-auto">
                        Update Notifications
                    </button>
                </div>
            </form>
        </div>

        <!-- User Preferences -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">User Preferences</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Customize your application experience</p>
            </div>
            <form action="{{ route('user-profile.update-preferences') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                
                @if(session('preferences_error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('preferences_error') }}
                    </div>
                @endif

                @php
                    $preferences = json_decode($user->preferences ?? '{}', true);
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Dashboard Layout -->
                    <div>
                        <label for="dashboard_layout" class="block text-sm font-medium text-gray-700 mb-2">Dashboard Layout</label>
                        <select name="dashboard_layout" id="dashboard_layout" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="grid" {{ ($preferences['dashboard_layout'] ?? 'grid') == 'grid' ? 'selected' : '' }}>Grid View</option>
                            <option value="list" {{ ($preferences['dashboard_layout'] ?? 'grid') == 'list' ? 'selected' : '' }}>List View</option>
                            <option value="compact" {{ ($preferences['dashboard_layout'] ?? 'grid') == 'compact' ? 'selected' : '' }}>Compact View</option>
                        </select>
                        @error('dashboard_layout', 'preferences')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Items Per Page -->
                    <div>
                        <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-2">Items Per Page</label>
                        <select name="items_per_page" id="items_per_page" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="10" {{ ($preferences['items_per_page'] ?? 25) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($preferences['items_per_page'] ?? 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($preferences['items_per_page'] ?? 25) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($preferences['items_per_page'] ?? 25) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        @error('items_per_page', 'preferences')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Format -->
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                        <select name="date_format" id="date_format" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="Y-m-d" {{ ($preferences['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="m/d/Y" {{ ($preferences['date_format'] ?? 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            <option value="d/m/Y" {{ ($preferences['date_format'] ?? 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="d-m-Y" {{ ($preferences['date_format'] ?? 'Y-m-d') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                        </select>
                        @error('date_format', 'preferences')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time Format -->
                    <div>
                        <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">Time Format</label>
                        <select name="time_format" id="time_format" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="24" {{ ($preferences['time_format'] ?? '24') == '24' ? 'selected' : '' }}>24 Hour</option>
                            <option value="12" {{ ($preferences['time_format'] ?? '24') == '12' ? 'selected' : '' }}>12 Hour</option>
                        </select>
                        @error('time_format', 'preferences')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" id="currency" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base sm:text-sm touch-target">
                            <option value="USD" {{ ($preferences['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ ($preferences['currency'] ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ ($preferences['currency'] ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                            <option value="CAD" {{ ($preferences['currency'] ?? 'USD') == 'CAD' ? 'selected' : '' }}>CAD (C$)</option>
                            <option value="MYR" {{ ($preferences['currency'] ?? 'USD') == 'MYR' ? 'selected' : '' }}>MYR (RM)</option>
                        </select>
                        @error('currency', 'preferences')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Preferences -->
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Additional Settings</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Auto Refresh Dashboard</span>
                                    <p class="text-xs text-gray-500">Automatically refresh dashboard data every 5 minutes</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="auto_refresh" value="1" class="sr-only peer" {{ ($preferences['auto_refresh'] ?? false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Show Tooltips</span>
                                    <p class="text-xs text-gray-500">Display helpful tooltips throughout the application</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_tooltips" value="1" class="sr-only peer" {{ ($preferences['show_tooltips'] ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Compact Sidebar</span>
                                    <p class="text-xs text-gray-500">Use a more compact sidebar layout</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="compact_sidebar" value="1" class="sr-only peer" {{ ($preferences['compact_sidebar'] ?? false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4 sm:mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition duration-200 text-sm sm:text-base touch-target w-full sm:w-auto">
                        Update Preferences
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-lg shadow-md border-l-4 border-red-500 hover:shadow-lg transition-shadow duration-200">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-red-900">Danger Zone</h3>
                <p class="text-xs sm:text-sm text-red-600 mt-1">Irreversible and destructive actions</p>
            </div>
            <div class="p-4 sm:p-6">
                @if(session('delete_error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 text-sm sm:text-base">
                        {{ session('delete_error') }}
                    </div>
                @endif

                <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4">
                    <div class="flex flex-col sm:flex-row">
                        <div class="flex-shrink-0 mb-3 sm:mb-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="sm:ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800">Delete Account</h3>
                            <div class="mt-2 text-xs sm:text-sm text-red-700">
                                <p>Once you delete your account, all of your data will be permanently removed. This action cannot be undone.</p>
                            </div>
                            <div class="mt-3 sm:mt-4">
                                <button type="button" onclick="showDeleteModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 sm:py-2 rounded-lg text-sm transition duration-200 touch-target w-full sm:w-auto">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 p-4">
    <div class="relative top-10 sm:top-20 mx-auto border max-w-md w-full shadow-lg rounded-md bg-white">
        <div class="p-4 sm:p-5">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.93L13.732 4.242a2 2 0 00-3.464 0L3.34 16.07c-.77 1.263.192 2.93 1.732 2.93z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900 mt-2">Delete Account</h3>
                <div class="mt-2 px-2 sm:px-7 py-2 sm:py-3">
                    <p class="text-xs sm:text-sm text-gray-500">
                        Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently removed.
                    </p>
                </div>
                <form action="{{ route('user-profile.delete') }}" method="POST" class="mt-3 sm:mt-4">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4 text-left">
                        <label for="confirm_password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Enter your password to confirm:</label>
                        <input type="password" name="password" id="confirm_password" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 text-base sm:text-sm touch-target">
                        @error('password', 'delete')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4 text-left">
                        <label for="confirm_text" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Type DELETE to confirm:</label>
                        <input type="text" name="confirmation" id="confirm_text" placeholder="Type DELETE to confirm" required class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 text-base sm:text-sm touch-target">
                        @error('confirmation', 'delete')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col sm:flex-row items-center px-2 sm:px-4 py-2 sm:py-3 space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="button" onclick="hideDeleteModal()" class="px-4 py-2.5 sm:py-2 bg-gray-300 text-gray-800 text-sm sm:text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 touch-target">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2.5 sm:py-2 bg-red-600 text-white text-sm sm:text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 touch-target">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with img
                const img = document.createElement('img');
                img.id = 'avatar-preview';
                img.src = e.target.result;
                img.alt = 'Avatar Preview';
                img.className = 'h-16 w-16 object-cover rounded-full';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        hideDeleteModal();
    }
}
</script>
@endsection
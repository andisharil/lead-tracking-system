@extends('layouts.app')

@section('title', 'Settings')

@section('page-title', 'Settings')

@section('page-description', 'Configure your system settings and integrations')

@section('header-actions')
    <button onclick="clearCache()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Clear Cache
    </button>
    <button onclick="exportSettings()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export Settings
    </button>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Settings Tabs -->
    <div class="bg-white rounded-lg shadow">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('general')" id="general-tab" class="tab-button active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    General
                </button>
                <button onclick="showTab('email')" id="email-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                </button>
                <button onclick="showTab('integrations')" id="integrations-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Integrations
                </button>
                <button onclick="showTab('security')" id="security-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Security
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- General Settings -->
            <div id="general-content" class="tab-content">
                <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                <form method="POST" action="{{ route('settings.update-general') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Application Name *</label>
                            <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $settings['general']['app_name'] ?? 'Lead Tracking CRM') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone *</label>
                            <select id="timezone" name="timezone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="UTC" {{ old('timezone', $settings['general']['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                <option value="Europe/London" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>London</option>
                                <option value="Europe/Paris" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                <option value="Asia/Tokyo" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                <option value="Asia/Kuala_Lumpur" {{ old('timezone', $settings['general']['timezone'] ?? '') == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>GMT+8 (Kuala Lumpur)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Date Format *</label>
                            <select id="date_format" name="date_format" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Y-m-d" {{ old('date_format', $settings['general']['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="m/d/Y" {{ old('date_format', $settings['general']['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d/m/Y" {{ old('date_format', $settings['general']['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="M j, Y" {{ old('date_format', $settings['general']['date_format'] ?? '') == 'M j, Y' ? 'selected' : '' }}>Mon DD, YYYY</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">Time Format *</label>
                            <select id="time_format" name="time_format" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="24" {{ old('time_format', $settings['general']['time_format'] ?? '24') == '24' ? 'selected' : '' }}>24 Hour</option>
                                <option value="12" {{ old('time_format', $settings['general']['time_format'] ?? '') == '12' ? 'selected' : '' }}>12 Hour</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language *</label>
                            <select id="language" name="language" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="en" {{ old('language', $settings['general']['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ms" {{ old('language', $settings['general']['language'] ?? '') == 'ms' ? 'selected' : '' }}>Bahasa Malaysia</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency *</label>
                            <select id="currency" name="currency" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="USD" {{ old('currency', $settings['general']['currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency', $settings['general']['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ old('currency', $settings['general']['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                <option value="JPY" {{ old('currency', $settings['general']['currency'] ?? '') == 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                                <option value="CAD" {{ old('currency', $settings['general']['currency'] ?? '') == 'CAD' ? 'selected' : '' }}>CAD (C$)</option>
                                <option value="MYR" {{ old('currency', $settings['general']['currency'] ?? '') == 'MYR' ? 'selected' : '' }}>MYR (RM)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-2">Items Per Page *</label>
                            <input type="number" id="items_per_page" name="items_per_page" value="{{ old('items_per_page', $settings['general']['items_per_page'] ?? 25) }}" min="5" max="100" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes) *</label>
                            <input type="number" id="session_timeout" name="session_timeout" value="{{ old('session_timeout', $settings['general']['session_timeout'] ?? 120) }}" min="15" max="1440" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="app_description" class="block text-sm font-medium text-gray-700 mb-2">Application Description</label>
                        <textarea id="app_description" name="app_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('app_description', $settings['general']['app_description'] ?? '') }}</textarea>
                    </div>
                    
                    <div class="mt-6 space-y-4">
                        <!-- Hidden defaults ensure unchecked checkboxes send 0 -->
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="hidden" name="debug_mode" value="0">
                        <div class="flex items-center">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['general']['maintenance_mode'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="maintenance_mode" class="ml-2 text-sm text-gray-900">Maintenance Mode</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="debug_mode" name="debug_mode" value="1" {{ old('debug_mode', $settings['general']['debug_mode'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="debug_mode" class="ml-2 text-sm text-gray-900">Debug Mode</label>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Save General Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Settings -->
            <div id="email-content" class="tab-content hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Email Settings</h3>
                    <button onclick="testEmailConfig()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                        Test Email
                    </button>
                </div>
                
                <form method="POST" action="{{ route('settings.update-email') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mail_driver" class="block text-sm font-medium text-gray-700 mb-2">Mail Driver *</label>
                            <select id="mail_driver" name="mail_driver" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleSmtpFields()">
                                <option value="smtp" {{ ($settings['email']['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ ($settings['email']['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ ($settings['email']['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ ($settings['email']['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                <option value="postmark" {{ ($settings['email']['mail_driver'] ?? '') == 'postmark' ? 'selected' : '' }}>Postmark</option>
                            </select>
                        </div>
                        
                        <div id="smtp_fields" class="contents">
                            <div>
                                <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                                <input type="text" id="mail_host" name="mail_host" value="{{ $settings['email']['mail_host'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                                <input type="number" id="mail_port" name="mail_port" value="{{ $settings['email']['mail_port'] ?? 587 }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                                <input type="text" id="mail_username" name="mail_username" value="{{ $settings['email']['mail_username'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                                <input type="password" id="mail_password" name="mail_password" value="{{ $settings['email']['mail_password'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                                <select id="mail_encryption" name="mail_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" {{ ($settings['email']['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                                    <option value="tls" {{ ($settings['email']['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['email']['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-2">From Email *</label>
                            <input type="email" id="mail_from_address" name="mail_from_address" value="{{ $settings['email']['mail_from_address'] ?? '' }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-2">From Name *</label>
                            <input type="text" id="mail_from_name" name="mail_from_name" value="{{ $settings['email']['mail_from_name'] ?? 'Lead Tracking CRM' }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Email Notifications</h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_emails" name="notification_emails" value="1" {{ ($settings['email']['notification_emails'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="notification_emails" class="ml-2 text-sm text-gray-900">Enable Email Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="lead_notifications" name="lead_notifications" value="1" {{ ($settings['email']['lead_notifications'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="lead_notifications" class="ml-2 text-sm text-gray-900">Lead Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="campaign_notifications" name="campaign_notifications" value="1" {{ ($settings['email']['campaign_notifications'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="campaign_notifications" class="ml-2 text-sm text-gray-900">Campaign Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="system_notifications" name="system_notifications" value="1" {{ ($settings['email']['system_notifications'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="system_notifications" class="ml-2 text-sm text-gray-900">System Notifications</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Save Email Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Integration Settings -->
            <div id="integrations-content" class="tab-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Integration Settings</h3>
                
                <form method="POST" action="{{ route('settings.update-integrations') }}">
                    @csrf
                    
                    <!-- Webhook Settings -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Webhook Configuration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="webhook_url" class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                                <input type="url" id="webhook_url" name="webhook_url" value="{{ $settings['integrations']['webhook_url'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="webhook_secret" class="block text-sm font-medium text-gray-700 mb-2">Webhook Secret</label>
                                <input type="password" id="webhook_secret" name="webhook_secret" value="{{ $settings['integrations']['webhook_secret'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Webhook Events</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @php
                                    $webhookEvents = json_decode($settings['integrations']['webhook_events'] ?? '[]', true) ?: [];
                                @endphp
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="webhook_events[]" value="lead_created" {{ in_array('lead_created', $webhookEvents) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-900">Lead Created</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="webhook_events[]" value="lead_updated" {{ in_array('lead_updated', $webhookEvents) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-900">Lead Updated</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="webhook_events[]" value="lead_deleted" {{ in_array('lead_deleted', $webhookEvents) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-900">Lead Deleted</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="webhook_events[]" value="campaign_created" {{ in_array('campaign_created', $webhookEvents) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-900">Campaign Created</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="webhook_events[]" value="campaign_updated" {{ in_array('campaign_updated', $webhookEvents) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-900">Campaign Updated</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- API Settings -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 mb-4">API Configuration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="api_rate_limit" class="block text-sm font-medium text-gray-700 mb-2">Rate Limit (requests/hour) *</label>
                                <input type="number" id="api_rate_limit" name="api_rate_limit" value="{{ $settings['integrations']['api_rate_limit'] ?? 1000 }}" min="10" max="10000" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="api_timeout" class="block text-sm font-medium text-gray-700 mb-2">API Timeout (seconds) *</label>
                                <input type="number" id="api_timeout" name="api_timeout" value="{{ $settings['integrations']['api_timeout'] ?? 30 }}" min="5" max="300" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Third-party Integrations -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Third-party Integrations</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                                <input type="text" id="google_analytics_id" name="google_analytics_id" value="{{ $settings['integrations']['google_analytics_id'] ?? '' }}" placeholder="GA-XXXXXXXXX-X" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="facebook_pixel_id" class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                                <input type="text" id="facebook_pixel_id" name="facebook_pixel_id" value="{{ $settings['integrations']['facebook_pixel_id'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="zapier_webhook" class="block text-sm font-medium text-gray-700 mb-2">Zapier Webhook URL</label>
                                <input type="url" id="zapier_webhook" name="zapier_webhook" value="{{ $settings['integrations']['zapier_webhook'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="slack_webhook" class="block text-sm font-medium text-gray-700 mb-2">Slack Webhook URL</label>
                                <input type="url" id="slack_webhook" name="slack_webhook" value="{{ $settings['integrations']['slack_webhook'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Save Integration Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Settings -->
            <div id="security-content" class="tab-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Security Settings</h3>
                
                <form method="POST" action="{{ route('settings.update-security') }}">
                    @csrf
                    
                    <!-- Password Policy -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Password Policy</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="password_min_length" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Minimum Length *</label>
                                <input type="number" id="password_min_length" name="password_min_length" value="{{ $settings['security']['password_min_length'] ?? 8 }}" min="6" max="50" required class="w-full px-3 py-3 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-3 sm:space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="password_require_uppercase" name="password_require_uppercase" value="1" {{ ($settings['security']['password_require_uppercase'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="password_require_uppercase" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Require Uppercase Letters</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="password_require_lowercase" name="password_require_lowercase" value="1" {{ ($settings['security']['password_require_lowercase'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="password_require_lowercase" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Require Lowercase Letters</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="password_require_numbers" name="password_require_numbers" value="1" {{ ($settings['security']['password_require_numbers'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="password_require_numbers" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Require Numbers</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="password_require_symbols" name="password_require_symbols" value="1" {{ ($settings['security']['password_require_symbols'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="password_require_symbols" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Require Special Characters</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Login Security -->
                    <div class="mb-8">
                        <h4 class="text-sm sm:text-md font-medium text-gray-900 mb-4">Login Security</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="login_attempts" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Max Login Attempts *</label>
                                <input type="number" id="login_attempts" name="login_attempts" value="{{ $settings['security']['login_attempts'] ?? 5 }}" min="3" max="10" required class="w-full px-3 py-3 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
                            </div>
                            
                            <div>
                                <label for="lockout_duration" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Lockout Duration (minutes) *</label>
                                <input type="number" id="lockout_duration" name="lockout_duration" value="{{ $settings['security']['lockout_duration'] ?? 15 }}" min="5" max="1440" required class="w-full px-3 py-3 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-3 sm:space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="two_factor_auth" name="two_factor_auth" value="1" {{ ($settings['security']['two_factor_auth'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="two_factor_auth" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Enable Two-Factor Authentication</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="session_secure" name="session_secure" value="1" {{ ($settings['security']['session_secure'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="session_secure" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Secure Session Cookies</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="force_https" name="force_https" value="1" {{ ($settings['security']['force_https'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-4 h-4 sm:w-3 sm:h-3 touch-target">
                                <label for="force_https" class="ml-3 sm:ml-2 text-xs sm:text-sm text-gray-900">Force HTTPS</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 sm:py-2 rounded-lg font-medium transition-colors duration-200 touch-target text-sm sm:text-base w-full sm:w-auto">
                            Save Security Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div id="testEmailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 sm:w-96 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900 mb-4">Test Email Configuration</h3>
            
            <div class="mb-4">
                <label for="test_email_address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" id="test_email_address" placeholder="Enter email address" class="w-full px-3 py-3 sm:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                <button onclick="closeTestEmailModal()" class="px-4 py-3 sm:py-2 bg-gray-500 text-white text-sm sm:text-base font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 touch-target transition-colors duration-200">
                    Cancel
                </button>
                <button onclick="sendTestEmail()" class="px-4 py-3 sm:py-2 bg-blue-500 text-white text-sm sm:text-base font-medium rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 touch-target transition-colors duration-200">
                    Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// Toggle SMTP fields based on mail driver
function toggleSmtpFields() {
    const mailDriver = document.getElementById('mail_driver').value;
    const smtpFields = document.getElementById('smtp_fields');
    
    if (mailDriver === 'smtp') {
        smtpFields.style.display = 'contents';
    } else {
        smtpFields.style.display = 'none';
    }
}

// Test email functionality
function testEmailConfig() {
    document.getElementById('testEmailModal').classList.remove('hidden');
}

function closeTestEmailModal() {
    document.getElementById('testEmailModal').classList.add('hidden');
    document.getElementById('test_email_address').value = '';
}

function sendTestEmail() {
    const email = document.getElementById('test_email_address').value;
    
    if (!email) {
        alert('Please enter an email address.');
        return;
    }
    
    fetch('{{ route("settings.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ test_email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test email sent successfully!');
            closeTestEmailModal();
        } else {
            alert('Failed to send test email: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the test email.');
    });
}

// Clear cache
function clearCache() {
    if (confirm('Are you sure you want to clear the application cache?')) {
        window.location.href = '{{ route("settings.clear-cache") }}';
    }
}

// Export settings
function exportSettings() {
    window.location.href = '{{ route("settings.export") }}';
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    toggleSmtpFields();
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('testEmailModal');
        if (event.target === modal) {
            closeTestEmailModal();
        }
    });
});
</script>
@endsection
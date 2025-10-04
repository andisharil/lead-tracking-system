@extends('layouts.app')

@section('title', 'User Profile')

@section('page-title', 'User Profile')

@section('page-description', 'Manage your account settings and preferences')

@section('header-actions')
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
        <a href="{{ route('user-profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center touch-target text-sm sm:text-base">
            <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span class="hidden sm:inline">Edit Profile</span>
        </a>
        <button onclick="exportUserData()" class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center touch-target text-sm sm:text-base">
            <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="hidden sm:inline">Export Data</span>
        </button>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="text-center">
                    <div class="relative inline-block">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Profile Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full mx-auto object-cover">
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full mx-auto bg-blue-500 flex items-center justify-center text-white text-xl sm:text-2xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 w-5 h-5 sm:w-6 sm:h-6 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mt-3 sm:mt-4 truncate">{{ $user->name }}</h2>
                    <p class="text-sm sm:text-base text-gray-600 truncate">{{ $user->email }}</p>
                    @if($user->position && $user->company)
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">{{ $user->position }} at {{ $user->company }}</p>
                    @endif
                </div>

                <div class="mt-4 sm:mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs sm:text-sm font-medium text-gray-700">Profile Completion</span>
                        <span class="text-xs sm:text-sm text-gray-600">{{ $profileStats['profile_completion'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $profileStats['profile_completion'] }}%"></div>
                    </div>
                </div>

                <div class="mt-4 sm:mt-6 space-y-2 sm:space-y-3">
                    @if($user->phone)
                        <div class="flex items-center text-xs sm:text-sm text-gray-600">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="truncate">{{ $user->phone }}</span>
                        </div>
                    @endif
                    @if($user->timezone)
                        <div class="flex items-center text-xs sm:text-sm text-gray-600">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="truncate">{{ $user->timezone }}</span>
                        </div>
                    @endif
                    <div class="flex items-center text-xs sm:text-sm text-gray-600">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 2m8-2l2 2m-2-2v10a2 2 0 01-2 2H8a2 2 0 01-2-2V9"></path>
                        </svg>
                        <span class="truncate">Member for {{ $profileStats['account_age_days'] }} days</span>
                    </div>
                    <div class="flex items-center text-xs sm:text-sm text-gray-600">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="truncate">Last login: {{ $profileStats['last_login']->diffForHumans() }}</span>
                    </div>
                </div>

                @if($user->bio)
                    <div class="mt-4 sm:mt-6">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-700 mb-2">Bio</h3>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">{{ $user->bio }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats and Activity -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0 mr-2 sm:mr-3">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Leads</p>
                            <p class="text-sm sm:text-lg font-semibold text-gray-900">{{ number_format($profileStats['total_leads']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-1.5 sm:p-2 bg-green-100 rounded-lg flex-shrink-0 mr-2 sm:mr-3">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Converted</p>
                            <p class="text-sm sm:text-lg font-semibold text-gray-900">{{ number_format($profileStats['converted_leads']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-1.5 sm:p-2 bg-purple-100 rounded-lg flex-shrink-0 mr-2 sm:mr-3">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Campaigns</p>
                            <p class="text-sm sm:text-lg font-semibold text-gray-900">{{ number_format($profileStats['campaigns_created']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-1.5 sm:p-2 bg-orange-100 rounded-lg flex-shrink-0 mr-2 sm:mr-3">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Reports</p>
                            <p class="text-sm sm:text-lg font-semibold text-gray-900">{{ number_format($profileStats['reports_generated']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-4 sm:p-6">
                    @if($activityLogs->count() > 0)
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($activityLogs as $log)
                                <div class="flex items-start space-x-2 sm:space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($log['action'] == 'profile_updated')
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </div>
                                        @elseif($log['action'] == 'password_changed')
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                        @elseif($log['action'] == 'login')
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-medium text-gray-900 leading-relaxed">{{ $log['description'] }}</p>
                                        <div class="flex items-center mt-1 text-xs text-gray-500 space-x-4">
                                            <span>{{ $log['created_at']->diffForHumans() }}</span>
                                            <span>IP: {{ $log['ip_address'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 sm:py-8">
                            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-xs sm:text-sm font-medium text-gray-900">No activity yet</h3>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500 leading-relaxed">Your recent activity will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportUserData() {
    if (confirm('Export your user data as JSON?')) {
        window.location.href = '{{ route("user-profile.export-data") }}';
    }
}
</script>
@endsection
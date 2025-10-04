@extends('layouts.app')

@section('title', 'Team Management')

@section('page-title', 'Team Management')

@section('page-description', 'Manage team members, roles, and permissions')

@section('header-actions')
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
        <button onclick="exportTeam()" class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center text-sm sm:text-base touch-target">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="hidden sm:inline">Export</span>
        </button>
        <a href="{{ route('team-management.roles') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center text-sm sm:text-base touch-target">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span class="hidden sm:inline">Manage Roles</span>
        </a>
        <a href="{{ route('team-management.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center text-sm sm:text-base touch-target">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="hidden sm:inline">Add Member</span>
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

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

    <!-- Team Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-full bg-blue-100 mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Members</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-full bg-green-100 mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Active</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['active_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-full bg-red-100 mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Inactive</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['inactive_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-full bg-purple-100 mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Roles</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['total_roles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-full bg-yellow-100 mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Recent Logins</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['recent_logins'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-full bg-indigo-100 mr-3 sm:mr-4 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['pending_invitations'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 sm:p-6">
            <form method="GET" action="{{ route('team-management.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
                <div>
                    <label for="search" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search" value="{{ $search ?? request('search') }}" placeholder="Name, email, company..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm touch-target">
                </div>
                
                <div>
                    <label for="role" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm touch-target">
                        <option value="">All Roles</option>
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->name }}" {{ (($role ?? request('role')) === $roleOption->name) ? 'selected' : '' }}>{{ $roleOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm touch-target">
                        <option value="">All Status</option>
                        <option value="active" {{ (($status ?? request('status')) === 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (($status ?? request('status')) === 'inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort_by" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    @php($currentSortBy = $sortBy ?? request('sort_by') ?? 'created_at')
                    <select id="sort_by" name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm touch-target">
                        <option value="created_at" {{ $currentSortBy === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="name" {{ $currentSortBy === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ $currentSortBy === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="last_login_at" {{ $currentSortBy === 'last_login_at' ? 'selected' : '' }}>Last Login</option>
                    </select>
                </div>
                
                <div class="flex items-end sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200 text-sm touch-target">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Team Members Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
            <h3 class="text-base sm:text-lg font-medium text-gray-900">Team Members ({{ $users->total() }})</h3>
            <div class="flex items-center space-x-2">
                <span class="text-xs sm:text-sm text-gray-500">{{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Role</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Company</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Last Login</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 touch-target">
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium text-xs sm:text-sm">
                                            {{ strtoupper(substr($user->name ?? $user->email, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $user->name ?? $user->email }}</div>
                                        <div class="text-xs sm:text-sm text-gray-500 truncate">{{ $user->email }}</div>
                                        @if($user->phone)
                                            <div class="text-xs text-gray-500 truncate sm:hidden">{{ $user->phone }}</div>
                                        @endif
                                        <!-- Show roles on mobile -->
                                        <div class="md:hidden mt-1">
                                            @foreach($user->roles->take(2) as $userRole)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                    {{ $userRole->name }}
                                                </span>
                                            @endforeach
                                            @if($user->roles->count() > 2)
                                                <span class="text-xs text-gray-500">+{{ $user->roles->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 hidden md:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $userRole)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $userRole->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 hidden lg:table-cell">
                                <div class="text-xs sm:text-sm text-gray-900">{{ $user->company ?: '-' }}</div>
                                @if($user->position)
                                    <div class="text-xs sm:text-sm text-gray-500">{{ $user->position }}</div>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                @if($user->deleted_at)
                                    <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="hidden sm:inline">Inactive</span>
                                        <span class="sm:hidden">✕</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="hidden sm:inline">Active</span>
                                        <span class="sm:hidden">✓</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <a href="{{ route('team-management.show', $user) }}" class="text-blue-600 hover:text-blue-900 touch-target">
                                        <span class="hidden sm:inline">View</span>
                                        <span class="sm:hidden">👁</span>
                                    </a>
                                    <a href="{{ route('team-management.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 touch-target">
                                        <span class="hidden sm:inline">Edit</span>
                                        <span class="sm:hidden">✏</span>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button onclick="confirmDelete(this)" data-url="{{ route('team-management.destroy', $user) }}" data-id="{{ $user->id }}" data-name="{{ $user->name }}" class="text-red-600 hover:text-red-900 touch-target">
                                            <span class="hidden sm:inline">Delete</span>
                                            <span class="sm:hidden">🗑</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No team members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Activity -->
    @if(isset($recentActivity) && $recentActivity->count() > 0)
        <div class="mt-6 sm:mt-8 bg-white rounded-lg shadow">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Recent Activity</h3>
                <a href="{{ route('team-management.activity-logs') }}" class="text-blue-600 hover:text-blue-900 text-xs sm:text-sm font-medium touch-target">
                    View All
                </a>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-3 sm:space-y-4">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start space-x-2 sm:space-x-3">
                            <div class="flex-shrink-0">
                                @if($activity->action === 'user_created')
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                @elseif($activity->action === 'user_updated')
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                @elseif($activity->action === 'user_deleted')
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                                <p class="text-xs sm:text-sm text-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500">{{ optional($activity->user)->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 sm:w-96 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100">
                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900 mt-4">Delete Team Member</h3>
            <div class="mt-2 px-4 sm:px-7 py-3">
                <p class="text-xs sm:text-sm text-gray-500" id="deleteMessage"></p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm sm:text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 mb-2 touch-target">
                        Delete
                    </button>
                </form>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-sm sm:text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 touch-target">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 sm:w-96 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900">Export Team Data</h3>
                <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 touch-target">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('team-management.export') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Export Format</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm touch-target">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Include Fields</label>
                    <div class="space-y-2">
                        <label class="flex items-center touch-target">
                            <input type="checkbox" name="fields[]" value="name" checked class="mr-2 touch-target">
                            <span class="text-xs sm:text-sm">Name</span>
                        </label>
                        <label class="flex items-center touch-target">
                            <input type="checkbox" name="fields[]" value="email" checked class="mr-2 touch-target">
                            <span class="text-xs sm:text-sm">Email</span>
                        </label>
                        <label class="flex items-center touch-target">
                            <input type="checkbox" name="fields[]" value="roles" checked class="mr-2 touch-target">
                            <span class="text-xs sm:text-sm">Roles</span>
                        </label>
                        <label class="flex items-center touch-target">
                            <input type="checkbox" name="fields[]" value="company" class="mr-2 touch-target">
                            <span class="text-xs sm:text-sm">Company</span>
                        </label>
                        <label class="flex items-center touch-target">
                            <input type="checkbox" name="fields[]" value="status" class="mr-2 touch-target">
                            <span class="text-xs sm:text-sm">Status</span>
                        </label>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 text-xs sm:text-sm touch-target">
                        Export
                    </button>
                    <button type="button" onclick="closeExportModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-xs sm:text-sm touch-target">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(el) {
    const userName = el.dataset.name;
    const actionUrl = el.dataset.url || (el.dataset.id ? `/team-management/${el.dataset.id}` : '');
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete ${userName}? This action cannot be undone.`;
    document.getElementById('deleteForm').action = actionUrl;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function exportTeam() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const deleteModal = document.getElementById('deleteModal');
    const exportModal = document.getElementById('exportModal');
    
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
    if (event.target === exportModal) {
        closeExportModal();
    }
});
</script>
@endsection
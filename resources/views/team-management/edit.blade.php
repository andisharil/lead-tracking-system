@extends('layouts.app')

@section('title', 'Edit Team Member')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Team Member</h1>
            <p class="text-gray-600">Update {{ $user->name }}'s information, roles and permissions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('team-management.show', $user) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Profile
            </a>
            <a href="{{ route('team-management.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Team
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
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

    <form method="POST" action="{{ route('team-management.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <input type="text" id="company" name="company" value="{{ old('company', $user->company) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('company') border-red-500 @enderror">
                    @error('company')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position/Title</label>
                    <input type="text" id="position" name="position" value="{{ old('position', $user->position) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Account Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Status</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Info</label>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div>Created: {{ $user->created_at->format('M d, Y') }}</div>
                        <div>Last Login: {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</div>
                        <div>Email Verified: {{ $user->email_verified_at ? 'Yes' : 'No' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Roles -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Roles *</h3>
            <p class="text-sm text-gray-600 mb-4">Select one or more roles for this team member</p>
            
            @error('roles')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $message }}
                </div>
            @enderror
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($roles as $role)
                    @php
                        $hasRole = $user->roles->contains($role->id);
                        $isChecked = in_array($role->id, old('roles', $user->roles->pluck('id')->toArray()));
                    @endphp
                    <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ $isChecked ? 'bg-blue-50 border-blue-200' : '' }}">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ $isChecked ? 'checked' : '' }}>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                            @if($role->description)
                                <div class="text-sm text-gray-500">{{ $role->description }}</div>
                            @endif
                            @if($role->permissions->count() > 0)
                                <div class="mt-2">
                                    <div class="text-xs text-gray-500 mb-1">Permissions:</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                +{{ $role->permissions->count() - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        
        <!-- Additional Permissions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Permissions</h3>
            <p class="text-sm text-gray-600 mb-4">Grant additional permissions beyond those provided by roles</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($permissions as $permission)
                    @php
                        $hasPermission = $user->permissions->contains($permission->id);
                        $isChecked = in_array($permission->id, old('permissions', $user->permissions->pluck('id')->toArray()));
                    @endphp
                    <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ $isChecked ? 'bg-blue-50 border-blue-200' : '' }}">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ $isChecked ? 'checked' : '' }}>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                            @if($permission->description)
                                <div class="text-sm text-gray-500">{{ $permission->description }}</div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-between">
            <div class="flex space-x-4">
                <button type="button" onclick="resetPassword()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Reset Password
                </button>
            </div>
            
            <div class="flex space-x-4">
                <a href="{{ route('team-management.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Update Team Member
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Reset Password</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to reset {{ $user->name }}'s password? A new temporary password will be generated and sent to their email.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="confirmResetPassword()" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Reset
                </button>
                <button onclick="closeResetPasswordModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Role and permission management
document.addEventListener('DOMContentLoaded', function() {
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
    
    // Update visual feedback when roles change
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateRoleVisualFeedback();
        });
    });
    
    // Update visual feedback when permissions change
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updatePermissionVisualFeedback();
        });
    });
    
    function updateRoleVisualFeedback() {
        roleCheckboxes.forEach(checkbox => {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('bg-blue-50', 'border-blue-200');
            } else {
                label.classList.remove('bg-blue-50', 'border-blue-200');
            }
        });
    }
    
    function updatePermissionVisualFeedback() {
        permissionCheckboxes.forEach(checkbox => {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('bg-blue-50', 'border-blue-200');
            } else {
                label.classList.remove('bg-blue-50', 'border-blue-200');
            }
        });
    }
});

// Reset password functionality
function resetPassword() {
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

function confirmResetPassword() {
    // Create a form to submit the reset password request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("team-management.reset-password", $user) }}';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add method override
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'POST';
    form.appendChild(methodField);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
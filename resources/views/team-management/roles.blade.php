@extends('layouts.app')

@section('title', 'Manage Roles')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Roles</h1>
            <p class="text-gray-600">Create and manage user roles and permissions</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openCreateRoleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Role
            </button>
            <a href="{{ route('team-management.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $role->name }}</h3>
                    <div class="flex space-x-2">
                        <button onclick="editRole({{ $role->id }})" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        @if(!in_array($role->name, ['Super Admin', 'Admin', 'User']))
                            <button onclick="deleteRole({{ $role->id }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                
                @if($role->description)
                    <p class="text-gray-600 text-sm mb-4">{{ $role->description }}</p>
                @endif
                
                <div class="mb-4">
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>Permissions</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ $role->permissions->count() }}
                        </span>
                    </div>
                    
                    @if($role->permissions->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($role->permissions->take(6) as $permission)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                            @if($role->permissions->count() > 6)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600">
                                    +{{ $role->permissions->count() - 6 }} more
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">No permissions assigned</p>
                    @endif
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Users with this role</span>
                        <span class="font-medium">{{ $role->users->count() }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($roles->count() === 0)
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No roles found</h3>
            <p class="text-gray-600 mb-4">Get started by creating your first role.</p>
            <button onclick="openCreateRoleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                Create Role
            </button>
        </div>
    @endif
</div>

<!-- Create Role Modal -->
<div id="createRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Role</h3>
            
            <form id="createRoleForm" method="POST" action="{{ route('team-management.store-role') }}">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="create_role_name" class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                        <input type="text" id="create_role_name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="create_role_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="create_role_description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                        <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="text-sm text-gray-900">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCreateRoleModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="editRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Role</h3>
            
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="edit_role_name" class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                        <input type="text" id="edit_role_name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="edit_role_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="edit_role_description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                        <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="editPermissionsList">
                                <!-- Permissions will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditRoleModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Role Modal -->
<div id="deleteRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Role</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="deleteRoleMessage">
                    Are you sure you want to delete this role? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="confirmDeleteRole()" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Delete
                </button>
                <button onclick="closeDeleteRoleModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRoleId = null;

// Create Role Modal
function openCreateRoleModal() {
    document.getElementById('createRoleModal').classList.remove('hidden');
}

function closeCreateRoleModal() {
    document.getElementById('createRoleModal').classList.add('hidden');
    document.getElementById('createRoleForm').reset();
}

// Edit Role Modal
function editRole(roleId) {
    currentRoleId = roleId;
    
    // Fetch role data
    fetch(`/team-management/roles/${roleId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_role_name').value = data.role.name;
            document.getElementById('edit_role_description').value = data.role.description || '';
            
            // Update form action
            document.getElementById('editRoleForm').action = `/team-management/roles/${roleId}`;
            
            // Load permissions
            const permissionsList = document.getElementById('editPermissionsList');
            permissionsList.innerHTML = '';
            
            data.permissions.forEach(permission => {
                const isChecked = data.role.permissions.some(p => p.id === permission.id);
                const label = document.createElement('label');
                label.className = 'flex items-center space-x-2';
                label.innerHTML = `
                    <input type="checkbox" name="permissions[]" value="${permission.id}" ${isChecked ? 'checked' : ''} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="text-sm text-gray-900">${permission.name}</span>
                `;
                permissionsList.appendChild(label);
            });
            
            document.getElementById('editRoleModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching role data:', error);
            alert('Error loading role data');
        });
}

function closeEditRoleModal() {
    document.getElementById('editRoleModal').classList.add('hidden');
    currentRoleId = null;
}

// Delete Role Modal
function deleteRole(roleId) {
    currentRoleId = roleId;
    document.getElementById('deleteRoleModal').classList.remove('hidden');
}

function closeDeleteRoleModal() {
    document.getElementById('deleteRoleModal').classList.add('hidden');
    currentRoleId = null;
}

function confirmDeleteRole() {
    if (currentRoleId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/team-management/roles/${currentRoleId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const createModal = document.getElementById('createRoleModal');
    const editModal = document.getElementById('editRoleModal');
    const deleteModal = document.getElementById('deleteRoleModal');
    
    if (event.target === createModal) {
        closeCreateRoleModal();
    }
    if (event.target === editModal) {
        closeEditRoleModal();
    }
    if (event.target === deleteModal) {
        closeDeleteRoleModal();
    }
});
</script>
@endsection
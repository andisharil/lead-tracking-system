<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use Carbon\Carbon;

class TeamManagementController extends Controller
{
    /**
     * Display team management dashboard
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $status = $request->get('status');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query = User::with(['roles', 'permissions'])
            ->where('id', '!=', Auth::id()); // Exclude current user
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }
        
        if ($role) {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }
        
        if ($status) {
            if ($status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        }
        
        $users = $query->orderBy($sortBy, $sortOrder)->paginate(15);
        $roles = Role::all();
        $permissions = Permission::all();
        
        // Get team statistics
        $stats = $this->getTeamStats();
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity();
        
        // Expose total users explicitly for the view
        $totalUsers = $stats['total_users'];
        
        return view('team-management.index', compact(
            'users', 'roles', 'permissions', 'stats', 'recentActivity', 'totalUsers',
            'search', 'role', 'status', 'sortBy', 'sortOrder'
        ));
    }
    
    /**
     * Show form for creating new team member
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        
        return view('team-management.create', compact('roles', 'permissions'));
    }
    
    /**
     * Store new team member
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'send_welcome_email' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'email_verified_at' => now(),
                'created_by' => Auth::id(),
            ]);
            
            // Assign roles
            $user->roles()->attach($request->roles);
            
            // Assign permissions
            if ($request->permissions) {
                $user->permissions()->attach($request->permissions);
            }
            
            // Log activity
            $this->logActivity('user_created', "Created new team member: {$user->name}", $user->id);
            
            // Send welcome email if requested
            if ($request->send_welcome_email) {
                // TODO: Implement welcome email functionality
            }
            
            DB::commit();
            
            return redirect()->route('team-management.index')
                ->with('success', 'Team member created successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create team member: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show team member details
     */
    public function show(User $user)
    {
        $user->load(['roles', 'permissions', 'activityLogs' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(20);
        }]);
        
        // Get user statistics
        $userStats = $this->getUserStats($user->id);
        
        return view('team-management.show', compact('user', 'userStats'));
    }
    
    /**
     * Show form for editing team member
     */
    public function edit(User $user)
    {
        $user->load(['roles', 'permissions']);
        $roles = Role::all();
        $permissions = Permission::all();
        
        return view('team-management.edit', compact('user', 'roles', 'permissions'));
    }
    
    /**
     * Update team member
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'status' => 'required|in:active,inactive',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'updated_by' => Auth::id(),
            ]);
            
            // Update status
            if ($request->status === 'inactive') {
                $user->delete(); // Soft delete
            } else {
                $user->restore();
            }
            
            // Sync roles
            $user->roles()->sync($request->roles);
            
            // Sync permissions
            $user->permissions()->sync($request->permissions ?? []);
            
            // Log activity
            $this->logActivity('user_updated', "Updated team member: {$user->name}", $user->id);
            
            DB::commit();
            
            return redirect()->route('team-management.show', $user)
                ->with('success', 'Team member updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update team member: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete team member
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting self
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You cannot delete your own account.');
            }
            
            // Soft delete user
            $user->delete();
            
            // Log activity
            $this->logActivity('user_deleted', "Deleted team member: {$user->name}", $user->id);
            
            return redirect()->route('team-management.index')
                ->with('success', 'Team member deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete team member: ' . $e->getMessage());
        }
    }
    
    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed',
            'send_notification' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'password')
                ->withInput();
        }
        
        try {
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password),
                'updated_by' => Auth::id(),
            ]);
            
            // Log activity
            $this->logActivity('password_reset', "Reset password for: {$user->name}", $user->id);
            
            // Send notification if requested
            if ($request->send_notification) {
                // TODO: Implement password reset notification
            }
            
            return redirect()->back()
                ->with('password_success', 'Password reset successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('password_error', 'Failed to reset password: ' . $e->getMessage());
        }
    }
    
    /**
     * Manage roles and permissions
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('team-management.roles', compact('roles', 'permissions'));
    }
    
    /**
     * Create new role
     */
    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'role')
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Create role
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => Auth::id(),
            ]);
            
            // Assign permissions
            if ($request->permissions) {
                $role->permissions()->attach($request->permissions);
            }
            
            // Log activity
            $this->logActivity('role_created', "Created new role: {$role->name}");
            
            DB::commit();
            
            return redirect()->back()
                ->with('role_success', 'Role created successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('role_error', 'Failed to create role: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update role
     */
    public function updateRole(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'role_edit')
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Update role
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_by' => Auth::id(),
            ]);
            
            // Sync permissions
            $role->permissions()->sync($request->permissions ?? []);
            
            // Log activity
            $this->logActivity('role_updated', "Updated role: {$role->name}");
            
            DB::commit();
            
            return redirect()->back()
                ->with('role_success', 'Role updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('role_error', 'Failed to update role: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete role
     */
    public function destroyRole(Role $role)
    {
        try {
            // Check if role is in use
            $usersCount = $role->users()->count();
            if ($usersCount > 0) {
                return redirect()->back()
                    ->with('role_error', "Cannot delete role '{$role->name}' as it is assigned to {$usersCount} user(s).");
            }
            
            // Delete role
            $role->delete();
            
            // Log activity
            $this->logActivity('role_deleted', "Deleted role: {$role->name}");
            
            return redirect()->back()
                ->with('role_success', 'Role deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('role_error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
    
    /**
     * Activity logs
     */
    public function activityLogs(Request $request)
    {
        $search = $request->get('search');
        $user = $request->get('user');
        $action = $request->get('action');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $query = ActivityLog::with('user')
            ->where('module', 'team_management')
            ->orderBy('created_at', 'desc');
        
        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }
        
        if ($user) {
            $query->where('user_id', $user);
        }
        
        if ($action) {
            $query->where('action', $action);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $logs = $query->paginate(20);
        $users = User::select('id', 'name')->get();
        $actions = ActivityLog::where('module', 'team_management')
            ->distinct()
            ->pluck('action');
        
        return view('team-management.activity-logs', compact(
            'logs', 'users', 'actions', 'search', 'user', 'action', 'dateFrom', 'dateTo'
        ));
    }
    
    /**
     * Export team data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $includeRoles = $request->get('include_roles', false);
        $includePermissions = $request->get('include_permissions', false);
        
        $users = User::with(['roles', 'permissions'])->get();
        
        if ($format === 'csv') {
            return $this->exportToCsv($users, $includeRoles, $includePermissions);
        } else {
            return $this->exportToExcel($users, $includeRoles, $includePermissions);
        }
    }
    
    /**
     * Get team statistics
     */
    private function getTeamStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::whereNull('deleted_at')->count(),
            'inactive_users' => User::whereNotNull('deleted_at')->count(),
            'total_roles' => Role::count(),
            'recent_logins' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count(),
            'pending_invitations' => 0, // TODO: Implement invitations
        ];
    }
    
    /**
     * Get recent activity
     */
    private function getRecentActivity()
    {
        return ActivityLog::with('user')
            ->where('module', 'team_management')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get user statistics
     */
    private function getUserStats($userId)
    {
        return [
            'total_leads' => 0, // TODO: Implement lead counting
            'total_campaigns' => 0, // TODO: Implement campaign counting
            'last_login' => User::find($userId)->last_login_at,
            'account_created' => User::find($userId)->created_at,
            'total_activities' => ActivityLog::where('user_id', $userId)->count(),
        ];
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $description, $targetUserId = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'team_management',
            'action' => $action,
            'description' => $description,
            'target_user_id' => $targetUserId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * Export to CSV
     */
    private function exportToCsv($users, $includeRoles, $includePermissions)
    {
        $filename = 'team_members_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($users, $includeRoles, $includePermissions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            $csvHeaders = ['ID', 'Name', 'Email', 'Phone', 'Company', 'Position', 'Status', 'Created At'];
            if ($includeRoles) $csvHeaders[] = 'Roles';
            if ($includePermissions) $csvHeaders[] = 'Permissions';
            
            fputcsv($file, $csvHeaders);
            
            foreach ($users as $user) {
                $row = [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->company,
                    $user->position,
                    $user->deleted_at ? 'Inactive' : 'Active',
                    $user->created_at->format('Y-m-d H:i:s'),
                ];
                
                if ($includeRoles) {
                    $row[] = $user->roles->pluck('name')->join(', ');
                }
                
                if ($includePermissions) {
                    $row[] = $user->permissions->pluck('name')->join(', ');
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export to Excel (simplified CSV for now)
     */
    private function exportToExcel($users, $includeRoles, $includePermissions)
    {
        // For now, return CSV with .xlsx extension
        // TODO: Implement proper Excel export with PhpSpreadsheet
        return $this->exportToCsv($users, $includeRoles, $includePermissions);
    }
}
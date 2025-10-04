@extends('layouts.app')

@section('title', 'Webhook Logs')

@section('page-title', 'Webhook Logs')

@section('page-description', 'Monitor webhook activity, errors, and performance')

@section('header-actions')
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
        <button onclick="openBulkRetryModal()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg font-medium transition-colors text-sm sm:text-base touch-target">
            <svg class="w-4 h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span class="hidden sm:inline">Bulk Retry</span>
            <span class="sm:hidden">Retry</span>
        </button>
        <button onclick="openClearLogsModal()" class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg font-medium transition-colors text-sm sm:text-base touch-target">
            <svg class="w-4 h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <span class="hidden sm:inline">Clear Old Logs</span>
            <span class="sm:hidden">Clear</span>
        </button>
        <button onclick="openExportModal()" class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg font-medium transition-colors text-sm sm:text-base touch-target">
            <svg class="w-4 h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="hidden sm:inline">Export</span>
            <span class="sm:hidden">Export</span>
        </button>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2.5 sm:py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2.5 sm:py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-2 sm:ml-3 lg:ml-4 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Webhooks</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate">{{ number_format($stats['total_webhooks']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-1.5 sm:p-2 bg-green-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-2 sm:ml-3 lg:ml-4 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Success Rate</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate">{{ $stats['success_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-1.5 sm:p-2 bg-red-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-2 sm:ml-3 lg:ml-4 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Failed Today</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate">{{ number_format($stats['failed_webhooks']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-1.5 sm:p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-2 sm:ml-3 lg:ml-4 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Avg Response Time</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate">{{ number_format($stats['avg_processing_time'] ?? 0) }}ms</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('webhook-logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                    <input type="text" name="source" value="{{ request('source') }}" placeholder="Filter by source..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Webhook Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}" class="log-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Success
                                    </span>
                                @elseif($log->status === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->source }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="truncate max-w-xs block" title="{{ $log->endpoint }}">{{ $log->endpoint }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ strtoupper($log->method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->processing_time_ms }}ms</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('webhook-logs.show', $log->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    @if($log->status === 'failed')
                                        <button onclick="retryWebhook({{ $log->id }})" class="text-yellow-600 hover:text-yellow-900">Retry</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No webhook logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Activity -->
    @if($recentActivity->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center">
                                    @if($activity->status === 'success')
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                    @elseif($activity->status === 'failed')
                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-3"></div>
                                    @else
                                        <div class="w-2 h-2 bg-yellow-400 rounded-full mr-3"></div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $activity->source }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity->endpoint }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                                    @if($activity->status === 'failed' && $activity->error_message)
                                        <p class="text-xs text-red-600 truncate max-w-xs">{{ $activity->error_message }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Bulk Retry Modal -->
<div id="bulkRetryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Retry Webhooks</h3>
            <p class="text-sm text-gray-600 mb-4">This will retry all selected failed webhooks. Are you sure?</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeBulkRetryModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="performBulkRetry()" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                    Retry Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div id="clearLogsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Clear Old Logs</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delete logs older than (days):</label>
                <input type="number" id="clearDays" value="30" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeClearLogsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="performClearLogs()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Clear Logs
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Export Webhook Logs</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Format:</label>
                <select id="exportFormat" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="csv">CSV</option>
                    <option value="json">JSON</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeExportModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="performExport()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    Export
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal functions
function openBulkRetryModal() {
    const selected = document.querySelectorAll('.log-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one webhook log to retry.');
        return;
    }
    document.getElementById('bulkRetryModal').classList.remove('hidden');
}

function closeBulkRetryModal() {
    document.getElementById('bulkRetryModal').classList.add('hidden');
}

function openClearLogsModal() {
    document.getElementById('clearLogsModal').classList.remove('hidden');
}

function closeClearLogsModal() {
    document.getElementById('clearLogsModal').classList.add('hidden');
}

function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.log-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Individual retry function
function retryWebhook(id) {
    if (!confirm('Are you sure you want to retry this webhook?')) {
        return;
    }

    fetch(`/webhook-logs/${id}/retry`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Webhook retried successfully!');
            location.reload();
        } else {
            alert('Retry failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while retrying the webhook.');
    });
}

// Bulk retry function
function performBulkRetry() {
    const selected = Array.from(document.querySelectorAll('.log-checkbox:checked')).map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Please select at least one webhook log to retry.');
        return;
    }

    fetch('/webhook-logs/bulk-retry', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids: selected })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeBulkRetryModal();
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during bulk retry.');
    });
}

// Clear logs function
function performClearLogs() {
    const days = document.getElementById('clearDays').value;
    
    if (!days || days < 1) {
        alert('Please enter a valid number of days.');
        return;
    }

    if (!confirm(`Are you sure you want to delete all webhook logs older than ${days} days? This action cannot be undone.`)) {
        return;
    }

    fetch('/webhook-logs/clear-old', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ days: parseInt(days) })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeClearLogsModal();
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while clearing logs.');
    });
}

// Export function
function performExport() {
    const format = document.getElementById('exportFormat').value;
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    
    window.location.href = '/webhook-logs/export?' + params.toString();
    closeExportModal();
}
</script>
@endsection
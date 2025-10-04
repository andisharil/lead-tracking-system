@extends('layouts.app')

@section('title', 'Dashboard - Lead Tracking CRM')
@section('page-title', 'Dashboard')
@section('page-description', 'Welcome to your Lead Tracking CRM')

@section('content')
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Total Leads</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalLeads }}</p>
                            <p class="text-xs text-gray-500">This Month</p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Successful Leads</p>
                            <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $successfulLeads }}</p>
                            <p class="text-xs text-gray-500">This Month</p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Conversion Rate</p>
                            <p class="text-xl sm:text-2xl font-bold text-purple-600">{{ $conversionRate }}%</p>
                            <p class="text-xs text-gray-500">This Month</p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Cost per Lead</p>
                            <p class="text-xl sm:text-2xl font-bold text-orange-600">RM{{ $costPerLead }}</p>
                            <p class="text-xs text-gray-500">This Month</p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Cost per Conversion</p>
                            <p class="text-xl sm:text-2xl font-bold text-red-600">RM{{ $costPerConversion }}</p>
                            <p class="text-xs text-gray-500">This Month</p>
                        </div>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
                <!-- Leads by Source Chart -->
                <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Leads by Source (This Month)</h3>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="sourceChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Leads by Location Chart -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Leads by Location</h3>
                            <p class="text-sm text-gray-600">Geographic distribution of your leads</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="relative h-80 bg-white rounded-lg p-4 shadow-inner border border-gray-100">
                        <canvas id="locationChart" class="w-full h-full"></canvas>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                Active Locations
                            </span>
                            <span class="font-medium">{{ $locations->count() ?? 0 }} Total</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Trend Chart -->
            <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6 sm:mb-8 hover:shadow-md transition-shadow">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Daily Leads Trend (This Month)</h3>
                <div class="relative w-full h-48 sm:h-64">
                    <canvas id="dailyChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Recent Leads Table -->
            <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6 sm:mb-8 hover:shadow-md transition-shadow">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4 mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Recent Leads (Latest 20)</h3>
                    <a href="{{ route('export.csv') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition-colors text-center touch-target">
                        Export CSV
                    </a>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Phone</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Location</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentLeads as $lead)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $lead->name }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden">{{ $lead->phone }}</div>
                                            <div class="text-xs text-gray-500 md:hidden">{{ $lead->location->name }}</div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $lead->phone }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $lead->location->name }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500">{{ $lead->source->name }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($lead->status === 'successful') bg-green-100 text-green-800
                                                @elseif($lead->status === 'lost') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($lead->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $lead->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 sm:px-6 py-4 text-center text-sm text-gray-500">No leads found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ad Spend Form -->
            <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6 sm:mb-8 hover:shadow-md transition-shadow">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Add/Update Ad Spend</h3>
                <form action="{{ route('ad-spend.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                        <input type="month" id="month" name="month" value="{{ $currentMonth }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
                    </div>
                    <div>
                        <label for="source_id" class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                        <select id="source_id" name="source_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
                            <option value="">Select Source</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="amount_spent" class="block text-sm font-medium text-gray-700 mb-2">Amount Spent (RM)</label>
                        <input type="number" id="amount_spent" name="amount_spent" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 touch-target">
                    </div>
                    <div class="flex items-end sm:col-span-2 lg:col-span-1">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors touch-target">
                            Update Spend
                        </button>
                    </div>
                </form>
             </div>
@endsection

@section('scripts')
<script>
        // Leads by Source Chart
        const sourceData = @json($leadsBySource);
        const sourceCtx = document.getElementById('sourceChart').getContext('2d');
        new Chart(sourceCtx, {
            type: 'bar',
            data: {
                labels: sourceData.map(item => item.name || 'Unknown'),
                datasets: [{
                    label: 'Leads',
                    data: sourceData.map(item => item.count || 0),
                    backgroundColor: [
                        '#2563EB', '#DC2626', '#059669', '#D97706', '#7C3AED'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });

        // Leads by Location Chart
        const locationData = @json($leadsByLocation);
        const locationCtx = document.getElementById('locationChart').getContext('2d');
        new Chart(locationCtx, {
            type: 'bar',
            data: {
                labels: locationData.map(item => item.name || 'Unknown'),
                datasets: [{
                    label: 'Leads',
                    data: locationData.map(item => item.count || 0),
                    backgroundColor: [
                        '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
                        '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1', '#14B8A6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true },
                    y: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });

        // Daily Leads Trend Chart
        const dailyData = @json($dailyLeads);
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date || ''),
                datasets: [{
                    label: 'Daily Leads',
                    data: dailyData.map(item => item.count || 0),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
</script>
@endsection
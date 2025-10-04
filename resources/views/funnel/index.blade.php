@extends('layouts.app')

@section('title', 'Conversion Funnel')

@section('page-title', 'Conversion Funnel')

@section('page-description', 'Visualize lead progression through conversion stages')

@section('header-actions')
<div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
    <button onclick="window.print()" class="inline-flex items-center px-3 sm:px-4 py-2 border border-gray-300 rounded-md shadow-sm text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 touch-target transition-colors">
        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        Print Report
    </button>
    <button onclick="exportData()" class="inline-flex items-center px-3 sm:px-4 py-2 border border-transparent rounded-md shadow-sm text-xs sm:text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 touch-target transition-colors">
        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export
    </button>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
        <form method="GET" action="{{ route('funnel.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="start_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" 
                       class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm touch-target transition-colors">
            </div>
            <div>
                <label for="end_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" 
                       class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm touch-target transition-colors">
            </div>
            <div>
                <label for="source_id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Source</label>
                <select id="source_id" name="source_id" 
                        class="w-full px-3 py-2 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm touch-target transition-colors">
                    <option value="">All Sources</option>
                    @foreach(($sources ?? []) as $source)
                        <option value="{{ $source->id }}" {{ ($sourceId ?? '') == $source->id ? 'selected' : '' }}>
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col justify-end">
                <button type="submit" class="w-full px-4 py-2 sm:py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium touch-target transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-500">Total Leads</p>
                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ number_format($totalLeads ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-500">Success Rate</p>
                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ ($conversionRates['overall_success_rate'] ?? 0) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-500">Conversion Rate</p>
                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ ($conversionRates['new_to_successful'] ?? 0) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-500">Drop-off Rate</p>
                    <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ ($conversionRates['drop_off_rate'] ?? 0) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Funnel Visualization -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
        <!-- Funnel Chart -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Conversion Funnel</h3>
            <div class="relative h-64 sm:h-80">
                <canvas id="funnelChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Funnel Stages Detail -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Stage Breakdown</h3>
            <div class="space-y-3 sm:space-y-4">
                @foreach(($funnelChartData ?? []) as $stage)
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg touch-target">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full mr-2 sm:mr-3 flex-shrink-0
                                @if($stage['status'] === 'new') bg-blue-500
                                @elseif($stage['status'] === 'successful') bg-green-500
                                @else bg-red-500
                                @endif"></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm sm:text-base font-medium text-gray-900 truncate">{{ $stage['stage'] }}</p>
                                <p class="text-xs sm:text-sm text-gray-500">{{ $stage['percentage'] }}% of total</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-3">
                            <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ number_format($stage['count']) }}</p>
                            <p class="text-xs sm:text-sm text-gray-500">leads</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Funnel by Source -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-6 sm:mb-8 hover:shadow-md transition-shadow">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Funnel Performance by Source</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">New Leads</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Qualified</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Successful</th>
                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(($funnelBySource ?? []) as $sourceName => $sourceData)
                        @php
                            $newCount = $sourceData->where('status', 'new')->first()->count ?? 0;
                            $successfulCount = $sourceData->where('status', 'successful')->first()->count ?? 0;
                            $lostCount = $sourceData->where('status', 'lost')->first()->count ?? 0;
                            $totalCount = $newCount + $successfulCount + $lostCount;
                            $successRate = $totalCount > 0 ? round(($successfulCount / $totalCount) * 100, 2) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 touch-target">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900">
                                <div class="truncate max-w-32 sm:max-w-none">{{ $sourceName }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900 hidden sm:table-cell">{{ number_format($newCount) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-500 hidden md:table-cell">{{ number_format($lostCount) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-green-600 font-medium">{{ number_format($successfulCount) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($successRate >= 50) bg-green-100 text-green-800
                                    @elseif($successRate >= 25) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $successRate }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>

    <!-- Timeline Chart -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Funnel Timeline</h3>
        <div class="relative h-48 sm:h-64">
            <canvas id="timelineChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
// Funnel Chart
const funnelCtx = document.getElementById('funnelChart').getContext('2d');
const funnelData = @json($funnelChartData ?? []);

new Chart(funnelCtx, {
    type: 'doughnut',
    data: {
        labels: funnelData.map(item => item.stage),
        datasets: [{
            data: funnelData.map(item => item.count),
            backgroundColor: [
                '#3B82F6', // Blue for New
                '#10B981', // Green for Successful
                '#EF4444'  // Red for Lost
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const item = funnelData[context.dataIndex];
                        return `${item.stage}: ${item.count} leads (${item.percentage}%)`;
                    }
                }
            }
        }
    }
});

// Timeline Chart
const timelineCtx = document.getElementById('timelineChart').getContext('2d');
const timelineRaw = @json($timeProgression ?? (object)[]);
const timelineData = (timelineRaw && typeof timelineRaw === 'object' && !Array.isArray(timelineRaw)) ? timelineRaw : {};

// Prepare timeline chart data using explicit {x, y} points and robust date sorting
const dates = Object.keys(timelineData).sort((a, b) => new Date(a) - new Date(b));
const seriesFor = (status) => dates.map(date => ({
    x: date,
    y: (timelineData[date].find(item => item.status === status)?.count) || 0
}));

new Chart(timelineCtx, {
    type: 'line',
    data: {
        datasets: [
            {
                label: 'New Leads',
                data: seriesFor('new'),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                spanGaps: true
            },
            {
                label: 'Successful',
                data: seriesFor('successful'),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                spanGaps: true
            },
            {
                label: 'Lost',
                data: seriesFor('lost'),
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                spanGaps: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        normalized: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                type: 'time',
                time: {
                    parser: 'yyyy-MM-dd',
                    displayFormats: {
                        day: 'MMM dd'
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
});
</script>
@endsection
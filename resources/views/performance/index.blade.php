@extends('layouts.app')

@section('title', 'Performance Metrics')

@section('page-title', 'Performance Metrics')

@section('page-description', 'ROI analysis, conversion rates, and source effectiveness')

@section('header-actions')
<div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
    <button onclick="exportData()" class="touch-target px-3 sm:px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-md hover:bg-green-700 transition-colors">
        Export Report
    </button>
    <button onclick="refreshData()" class="touch-target px-3 sm:px-4 py-2 bg-blue-600 text-white text-xs sm:text-sm rounded-md hover:bg-blue-700 transition-colors">
        Refresh Data
    </button>
</div>
@endsection

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <form method="GET" action="{{ route('performance.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" 
                           class="touch-target w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" 
                           class="touch-target w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="source_id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Source</label>
                    <select id="source_id" name="source_id" 
                            class="touch-target w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Sources</option>
                        @foreach(($sources ?? []) as $source)
                            <option value="{{ $source->id }}" {{ ($sourceId ?? '') == $source->id ? 'selected' : '' }}>
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="touch-target w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Leads</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($totalLeads ?? 0) }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-blue-100 rounded-full flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Successful Leads</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600">{{ number_format($successfulLeads ?? 0) }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 rounded-full flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Conversion Rate</p>
                    <p class="text-xl sm:text-2xl font-bold text-purple-600">{{ $conversionRate ?? 0 }}%</p>
                </div>
                <div class="p-2 sm:p-3 bg-purple-100 rounded-full flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Revenue</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600">${{ number_format($totalRevenue ?? 0, 2) }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-yellow-100 rounded-full flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Avg Deal Size</p>
                    <p class="text-xl sm:text-2xl font-bold text-indigo-600">${{ number_format($averageDealSize ?? 0, 2) }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-indigo-100 rounded-full flex-shrink-0 ml-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Monthly Performance Trend -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Monthly Performance Trend</h3>
            <div class="relative h-64 sm:h-80">
                <canvas id="monthlyTrendChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Source Quality Score -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Source Quality Score</h3>
            <div class="relative h-64 sm:h-80">
                <canvas id="qualityScoreChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Source Performance Table -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Source Performance Analysis</h3>
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Total Leads</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Successful</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conv. Rate</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Revenue</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Avg Deal Size</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quality Score</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(($sourceQuality ?? []) as $source)
                        <tr class="hover:bg-gray-50 touch-target">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900">
                                <div class="truncate max-w-32 sm:max-w-none">{{ $source->name }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">{{ number_format($source->total_leads) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden md:table-cell">{{ number_format($source->successful_leads) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $source->conversion_rate >= 20 ? 'bg-green-100 text-green-800' : 
                                       ($source->conversion_rate >= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $source->conversion_rate }}%
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden lg:table-cell">${{ number_format($source->revenue, 2) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden xl:table-cell">${{ number_format($source->avg_deal_size, 2) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2 min-w-12">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($source->quality_score, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium whitespace-nowrap">{{ $source->quality_score }}/100</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cost Analysis Table -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">ROI & Cost Analysis</h3>
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Total Cost</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Revenue</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Cost/Lead</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Cost/Acquisition</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(($costAnalysis ?? []) as $cost)
                        <tr class="hover:bg-gray-50 touch-target">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900">
                                <div class="truncate max-w-32 sm:max-w-none">{{ $cost['source_name'] }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">${{ number_format($cost['total_cost'], 2) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden md:table-cell">${{ number_format($cost['revenue'], 2) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $cost['roi'] >= 100 ? 'bg-green-100 text-green-800' : 
                                       ($cost['roi'] >= 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $cost['roi'] }}%
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden lg:table-cell">${{ number_format($cost['cost_per_lead'], 2) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden xl:table-cell">${{ number_format($cost['cost_per_acquisition'], 2) }}</td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                <span class="{{ $cost['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    ${{ number_format($cost['profit'], 2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
            </table>
        </div>
    </div>
</div>


<script>
// Monthly Performance Trend Chart
const monthlyDataRaw = @json($monthlyTrend ?? []);
const monthlyData = Array.isArray(monthlyDataRaw) ? monthlyDataRaw : Object.values(monthlyDataRaw || {});
const monthlyLabels = monthlyData.map(item => {
    const date = new Date(item.month + '-01');
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
});

const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(monthlyTrendCtx, {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Total Leads',
            data: monthlyData.map(item => item.total_leads),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Successful Leads',
            data: monthlyData.map(item => item.successful_leads),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }, {
            label: 'Revenue ($)',
            data: monthlyData.map(item => item.revenue),
            borderColor: 'rgb(251, 191, 36)',
            backgroundColor: 'rgba(251, 191, 36, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                position: 'left'
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});

// Source Quality Score Chart
const qualityDataRaw = @json(($sourceQuality ?? collect())->take(10));
const qualityData = Array.isArray(qualityDataRaw) ? qualityDataRaw : Object.values(qualityDataRaw || {});
const qualityLabels = qualityData.map(item => item.name);
const qualityScores = qualityData.map(item => item.quality_score);

const qualityScoreCtx = document.getElementById('qualityScoreChart').getContext('2d');
new Chart(qualityScoreCtx, {
    type: 'bar',
    data: {
        labels: qualityLabels,
        datasets: [{
            label: 'Quality Score',
            data: qualityScores,
            backgroundColor: 'rgba(99, 102, 241, 0.8)',
            borderColor: 'rgb(99, 102, 241)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>
@endsection
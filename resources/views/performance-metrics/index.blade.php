@extends('layouts.app')

@section('title', 'Performance Metrics')

@section('page-title', 'Performance Metrics')

@section('page-description', 'ROI analysis, conversion tracking, and source effectiveness')

@section('header-actions')
    <button onclick="exportData('csv')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export CSV
    </button>
    <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Refresh
    </button>
@endsection

@section('content')

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form method="GET" action="{{ route('performance-metrics.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="source_id" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                    <select id="source_id" name="source_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Sources</option>
                        @foreach(($sources ?? []) as $source)
                            <option value="{{ $source->id }}" {{ ($sourceId ?? '') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="campaign_id" class="block text-sm font-medium text-gray-700 mb-1">Campaign</label>
                    <select id="campaign_id" name="campaign_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Campaigns</option>
                        @foreach(($campaigns ?? []) as $campaign)
                            <option value="{{ $campaign->id }}" {{ ($campaignId ?? '') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- ROI Analysis Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total ROI</p>
                        <p class="text-2xl font-bold {{ ($roiAnalysis['roi'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($roiAnalysis['roi'] ?? 0, 1) }}%
                        </p>
                        @if(($roiAnalysis['roi_change'] ?? 0) != 0)
                            <p class="text-xs {{ ($roiAnalysis['roi_change'] ?? 0) > 0 ? 'text-green-500' : 'text-red-500' }}">
                                {{ ($roiAnalysis['roi_change'] ?? 0) > 0 ? '+' : '' }}{{ number_format($roiAnalysis['roi_change'] ?? 0, 1) }}% from previous period
                            </p>
                        @endif
                    </div>
                    <div class="p-3 {{ ($roiAnalysis['roi'] ?? 0) >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-full">
                        <svg class="w-6 h-6 {{ ($roiAnalysis['roi'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">ROAS</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($roiAnalysis['roas'] ?? 0, 2) }}x</p>
                        <p class="text-xs text-gray-500">Return on Ad Spend</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cost Per Lead</p>
                        <p class="text-2xl font-bold text-orange-600">${{ number_format($roiAnalysis['cost_per_lead'] ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500">Average acquisition cost</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($roiAnalysis['conversion_rate'] ?? 0, 1) }}%</p>
                        <p class="text-xs text-gray-500">{{ $roiAnalysis['closed_leads'] ?? 0 }} of {{ $roiAnalysis['total_leads'] ?? 0 }} leads</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Trends Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Performance Trends</h3>
                <div class="flex space-x-2">
                    <button onclick="updateTrendChart('roi')" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">ROI</button>
                    <button onclick="updateTrendChart('conversion')" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Conversion</button>
                    <button onclick="updateTrendChart('cost')" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Cost</button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        <!-- Source & Campaign Effectiveness -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Source Effectiveness -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Source Effectiveness</h3>
                <div class="space-y-4">
                    @foreach(($sourceEffectiveness ?? collect())->take(5) as $source)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $source['source_name'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $source['source_type'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium {{ $source['roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($source['roi'], 1) }}% ROI
                                    </p>
                                    <p class="text-xs text-gray-500">Quality: {{ number_format($source['quality_score'], 0) }}/100</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Leads</p>
                                    <p class="font-medium">{{ $source['total_leads'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Conv. Rate</p>
                                    <p class="font-medium">{{ number_format($source['conversion_rate'], 1) }}%</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Revenue</p>
                                    <p class="font-medium">${{ number_format($source['total_revenue'], 0) }}</p>
                                </div>
                            </div>
                            <!-- Quality Score Bar -->
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Quality Score</span>
                                    <span>{{ number_format($source['quality_score'], 0) }}/100</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($source['quality_score'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Campaign Effectiveness -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Campaign Effectiveness</h3>
                <div class="space-y-4">
                    @foreach(($campaignEffectiveness ?? collect())->take(5) as $campaign)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $campaign['campaign_name'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $campaign['campaign_type'] }} • {{ ucfirst($campaign['campaign_status']) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium {{ $campaign['roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($campaign['roi'], 1) }}% ROI
                                    </p>
                                    <p class="text-xs text-gray-500">Score: {{ number_format($campaign['performance_score'], 0) }}/100</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Leads</p>
                                    <p class="font-medium">{{ $campaign['total_leads'] }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Conv. Rate</p>
                                    <p class="font-medium">{{ number_format($campaign['conversion_rate'], 1) }}%</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Profit</p>
                                    <p class="font-medium {{ $campaign['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($campaign['profit'], 0) }}
                                    </p>
                                </div>
                            </div>
                            <!-- Performance Score Bar -->
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Performance Score</span>
                                    <span>{{ number_format($campaign['performance_score'], 0) }}/100</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($campaign['performance_score'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Conversion Tracking -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Conversion Tracking</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Stage Conversions -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Conversion by Stage</h4>
                    <div class="space-y-3">
                        @foreach(($conversionTracking['stage_conversions'] ?? []) as $stage => $data)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $stage) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-gray-900">{{ $data['count'] }} ({{ number_format($data['percentage'], 1) }}%)</span>
                                    <div class="text-xs text-gray-500">${{ number_format($data['value'], 0) }}</div>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 ml-6">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Key Metrics -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Key Conversion Metrics</h4>
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Overall Conversion Rate</span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($conversionTracking['overall_conversion_rate'] ?? 0, 1) }}%</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Win Rate</span>
                                <span class="text-lg font-bold text-green-600">{{ number_format($conversionTracking['win_rate'] ?? 0, 1) }}%</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg. Time to Close</span>
                                <span class="text-lg font-bold text-purple-600">{{ number_format($conversionTracking['avg_time_to_close'] ?? 0, 0) }} days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Benchmarks -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Performance Benchmarks</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(($benchmarks ?? []) as $metric => $data)
                    <div class="text-center">
                        <div class="mb-2">
                            <span class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $metric) }}</span>
                        </div>
                        <div class="relative">
                            <div class="text-2xl font-bold {{ $data['status'] === 'above' ? 'text-green-600' : ($data['status'] === 'below' ? 'text-red-600' : 'text-yellow-600') }}">
                                @if(in_array($metric, ['conversion_rate', 'roi']))
                                    {{ number_format($data['current'], 1) }}%
                                @else
                                    ${{ number_format($data['current'], 0) }}
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Industry: 
                                @if(in_array($metric, ['conversion_rate', 'roi']))
                                    {{ number_format($data['benchmark'], 1) }}%
                                @else
                                    ${{ number_format($data['benchmark'], 0) }}
                                @endif
                            </div>
                            <div class="text-xs mt-1 {{ $data['percentage_diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                                {{ $data['percentage_diff'] > 0 ? '+' : '' }}{{ number_format($data['percentage_diff'], 1) }}% vs industry
                            </div>
                        </div>
                        <div class="mt-2">
                            @if($data['status'] === 'above')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Above Average
                                </span>
                            @elseif($data['status'] === 'below')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Below Average
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    On Par
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cost Analysis -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Cost Analysis</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Platform Breakdown -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Cost by Platform</h4>
                    <div class="h-64">
                        <canvas id="platformCostChart"></canvas>
                    </div>
                </div>

                <!-- Daily Spend Trend -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Daily Spend Trend</h4>
                    <div class="h-64">
                        <canvas id="dailySpendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


<script>
// Chart configurations
let trendsChart, platformCostChart, dailySpendChart;

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeTrendsChart();
    initializePlatformCostChart();
    initializeDailySpendChart();
});

function initializeTrendsChart() {
    const ctx = document.getElementById('trendsChart').getContext('2d');
    const trendsData = @json($performanceTrends ?? []);
    
    trendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendsData.map(item => item.week_label),
            datasets: [{
                label: 'ROI (%)',
                data: trendsData.map(item => item.roi),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'ROI (%)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}

function updateTrendChart(metric) {
    const trendsData = @json($performanceTrends ?? []);
    let data, label, color;
    
    switch(metric) {
        case 'roi':
            data = trendsData.map(item => item.roi);
            label = 'ROI (%)';
            color = 'rgb(59, 130, 246)';
            break;
        case 'conversion':
            data = trendsData.map(item => item.conversion_rate);
            label = 'Conversion Rate (%)';
            color = 'rgb(16, 185, 129)';
            break;
        case 'cost':
            data = trendsData.map(item => item.cost_per_lead);
            label = 'Cost Per Lead ($)';
            color = 'rgb(245, 101, 101)';
            break;
    }
    
    trendsChart.data.datasets[0].data = data;
    trendsChart.data.datasets[0].label = label;
    trendsChart.data.datasets[0].borderColor = color;
    trendsChart.data.datasets[0].backgroundColor = color.replace('rgb', 'rgba').replace(')', ', 0.1)');
    trendsChart.options.scales.y.title.text = label;
    trendsChart.update();
    
    // Update button states
    document.querySelectorAll('[onclick^="updateTrendChart"]').forEach(btn => {
        btn.className = 'px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200';
    });
    event.target.className = 'px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200';
}

function initializePlatformCostChart() {
    const ctx = document.getElementById('platformCostChart').getContext('2d');
    const platformData = @json($costAnalysis['platform_breakdown'] ?? (object)[]);
    
    const labels = Object.keys(platformData);
    const data = Object.values(platformData).map(item => item.total_spend);
    
    platformCostChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 101, 101)',
                    'rgb(245, 158, 11)',
                    'rgb(139, 92, 246)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function initializeDailySpendChart() {
    const ctx = document.getElementById('dailySpendChart').getContext('2d');
    const dailyData = @json($costAnalysis['daily_spend_trend'] ?? []);
    
    dailySpendChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(item => item.date),
            datasets: [{
                label: 'Daily Spend ($)',
                data: dailyData.map(item => item.total_spend),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Spend ($)'
                    }
                }
            }
        }
    });
}

function exportData(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    
    const exportUrl = '{{ route("performance-metrics.export") }}?' + params.toString();
    window.open(exportUrl, '_blank');
}

function refreshData() {
    window.location.reload();
}
</script>
@endsection
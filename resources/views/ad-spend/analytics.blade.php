@extends('layouts.app')

@section('title', 'Ad Spend Analytics')

@section('page-title', 'Ad Spend Analytics')

@section('page-description', 'Detailed analysis of advertising costs and performance')

@section('header-actions')
    <a href="{{ route('ad-spend.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Ad Spend
    </a>
    <button onclick="exportData()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md font-medium transition-colors">
        Export Data
    </button>
    <a href="{{ route('ad-spend.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium transition-colors">
        Add Record
    </a>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-7xl mx-auto">

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="source_id" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                    <select name="source_id" id="source_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Sources</option>
                        @foreach($sources as $source)
                            <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Spent</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($analytics['total_spent'], 2) }}</p>
                        @if($analytics['spend_change'] !== null)
                            <p class="text-sm {{ $analytics['spend_change'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $analytics['spend_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['spend_change'], 1) }}% vs last period
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Impressions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['total_impressions']) }}</p>
                        @if($analytics['impressions_change'] !== null)
                            <p class="text-sm {{ $analytics['impressions_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $analytics['impressions_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['impressions_change'], 1) }}% vs last period
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Clicks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['total_clicks']) }}</p>
                        @if($analytics['clicks_change'] !== null)
                            <p class="text-sm {{ $analytics['clicks_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $analytics['clicks_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['clicks_change'], 1) }}% vs last period
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Conversions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['total_conversions']) }}</p>
                        @if($analytics['conversions_change'] !== null)
                            <p class="text-sm {{ $analytics['conversions_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $analytics['conversions_change'] >= 0 ? '+' : '' }}{{ number_format($analytics['conversions_change'], 1) }}% vs last period
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Spend Trend Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Spend Trend</h2>
                    <div class="flex space-x-2">
                        <button onclick="toggleSpendChart('daily')" class="px-3 py-1 text-sm bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition-colors" id="daily-btn">Daily</button>
                        <button onclick="toggleSpendChart('weekly')" class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors" id="weekly-btn">Weekly</button>
                        <button onclick="toggleSpendChart('monthly')" class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors" id="monthly-btn">Monthly</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="spendChart"></canvas>
                </div>
            </div>
            
            <!-- Performance Metrics Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Performance Metrics</h2>
                <div class="h-64">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Source Performance & ROI Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Source Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Performance by Source</h2>
                <div class="space-y-4">
                    @foreach($analytics['source_performance'] as $source)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-medium text-gray-900">{{ $source['name'] }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $source['type'] }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Spent:</span>
                                <span class="font-medium">${{ number_format($source['total_spent'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Clicks:</span>
                                <span class="font-medium">{{ number_format($source['total_clicks']) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Conversions:</span>
                                <span class="font-medium">{{ number_format($source['total_conversions']) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">CPC:</span>
                                <span class="font-medium">${{ $source['avg_cpc'] }}</span>
                            </div>
                        </div>
                        
                        @if($source['total_impressions'] > 0)
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>CTR: {{ $source['ctr'] }}%</span>
                                <span>Conv Rate: {{ $source['conversion_rate'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($source['ctr'] * 10, 100) }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- ROI Analysis -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">ROI Analysis</h2>
                
                <div class="space-y-6">
                    <!-- Overall ROI -->
                    <div class="text-center p-6 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg">
                        <div class="text-3xl font-bold {{ $analytics['overall_roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $analytics['overall_roi'] >= 0 ? '+' : '' }}{{ number_format($analytics['overall_roi'], 1) }}%
                        </div>
                        <div class="text-sm text-gray-600 mt-1">Overall ROI</div>
                        <div class="text-xs text-gray-500 mt-2">
                            Revenue: ${{ number_format($analytics['total_revenue'], 2) }} | Spent: ${{ number_format($analytics['total_spent'], 2) }}
                        </div>
                    </div>
                    
                    <!-- Cost Efficiency Metrics -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-xl font-bold text-gray-900">${{ $analytics['avg_cpc'] }}</div>
                            <div class="text-sm text-gray-600">Avg CPC</div>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-xl font-bold text-gray-900">${{ $analytics['avg_cpa'] }}</div>
                            <div class="text-sm text-gray-600">Avg CPA</div>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-xl font-bold text-gray-900">{{ $analytics['avg_ctr'] }}%</div>
                            <div class="text-sm text-gray-600">Avg CTR</div>
                        </div>
                        
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-xl font-bold text-gray-900">{{ $analytics['avg_conversion_rate'] }}%</div>
                            <div class="text-sm text-gray-600">Avg Conv Rate</div>
                        </div>
                    </div>
                    
                    <!-- Recommendations -->
                    <div class="border-t pt-4">
                        <h3 class="font-medium text-gray-900 mb-3">Recommendations</h3>
                        <div class="space-y-2 text-sm">
                            @if($analytics['avg_ctr'] < 2)
                                <div class="flex items-start space-x-2">
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2 flex-shrink-0"></div>
                                    <p class="text-gray-600">Consider improving ad copy and targeting to increase CTR above 2%</p>
                                </div>
                            @endif
                            
                            @if($analytics['overall_roi'] < 100)
                                <div class="flex items-start space-x-2">
                                    <div class="w-2 h-2 bg-red-400 rounded-full mt-2 flex-shrink-0"></div>
                                    <p class="text-gray-600">Focus on higher-converting sources to improve ROI</p>
                                </div>
                            @endif
                            
                            @if($analytics['avg_conversion_rate'] < 5)
                                <div class="flex items-start space-x-2">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
                                    <p class="text-gray-600">Optimize landing pages to improve conversion rates</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Performance Table -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Platform Performance Breakdown</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impressions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CTR</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPC</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conv Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($analytics['platform_performance'] as $platform)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $platform['platform'] ?: 'Not Specified' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($platform['total_spent'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($platform['total_impressions']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($platform['total_clicks']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($platform['total_conversions']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $platform['ctr'] }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ $platform['avg_cpc'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $platform['conversion_rate'] }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No platform data available for the selected period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
// Chart data from backend
const chartData = @json($analytics['chart_data']);

// Spend Trend Chart
const spendCtx = document.getElementById('spendChart').getContext('2d');
let spendChart = new Chart(spendCtx, {
    type: 'line',
    data: {
        labels: chartData.daily.labels,
        datasets: [{
            label: 'Daily Spend',
            data: chartData.daily.spend,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Spend: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Impressions', 'Clicks', 'Conversions'],
        datasets: [{
            data: [
                {{ $analytics['total_impressions'] }},
                {{ $analytics['total_clicks'] }},
                {{ $analytics['total_conversions'] }}
            ],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(168, 85, 247, 0.8)',
                'rgba(249, 115, 22, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});

// Toggle spend chart view
function toggleSpendChart(period) {
    // Update button states
    document.querySelectorAll('[id$="-btn"]').forEach(btn => {
        btn.className = 'px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors';
    });
    document.getElementById(period + '-btn').className = 'px-3 py-1 text-sm bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition-colors';
    
    // Update chart data
    spendChart.data.labels = chartData[period].labels;
    spendChart.data.datasets[0].data = chartData[period].spend;
    spendChart.data.datasets[0].label = period.charAt(0).toUpperCase() + period.slice(1) + ' Spend';
    spendChart.update();
}

// Export functionality
function exportData() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route("ad-spend.analytics") }}?' + params.toString();
}
</script>
@endsection
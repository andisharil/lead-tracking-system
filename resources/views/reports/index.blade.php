@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('page-title', 'Reports Dashboard')

@section('page-description', 'Advanced analytics and performance insights')

@section('header-actions')
    <button onclick="exportReport('csv')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export CSV
    </button>
    <button onclick="exportReport('pdf')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
        </svg>
        Export PDF
    </button>
@endsection

@section('content')

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="source_id" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                <select id="source_id" name="source_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Sources</option>
                    @foreach(($sources ?? []) as $source)
                        <option value="{{ $source->id }}" {{ ($sourceId ?? '') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="campaign_id" class="block text-sm font-medium text-gray-700 mb-1">Campaign</label>
                <select id="campaign_id" name="campaign_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Campaigns</option>
                    @foreach(($campaigns ?? []) as $campaign)
                        <option value="{{ $campaign->id }}" {{ ($campaignId ?? '') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="metric" class="block text-sm font-medium text-gray-700 mb-1">Metric</label>
                <select id="metric" name="metric" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="leads" {{ ($metric ?? '') == 'leads' ? 'selected' : '' }}>Leads</option>
                    <option value="revenue" {{ ($metric ?? '') == 'revenue' ? 'selected' : '' }}>Revenue</option>
                    <option value="conversions" {{ ($metric ?? '') == 'conversions' ? 'selected' : '' }}>Conversions</option>
                    <option value="conversion_rate" {{ ($metric ?? '') == 'conversion_rate' ? 'selected' : '' }}>Conversion Rate</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="new" {{ ($status ?? '') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="successful" {{ ($status ?? '') == 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="lost" {{ ($status ?? '') == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Overview Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Leads</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(($overview['total_leads'] ?? 0)) }}</p>
                    @if(($overview['leads_change'] ?? null) !== null)
                        <p class="text-sm {{ ($overview['leads_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($overview['leads_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format(($overview['leads_change'] ?? 0), 1) }}% from previous period
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format(($overview['total_revenue'] ?? 0), 2) }}</p>
                    @if(($overview['revenue_change'] ?? null) !== null)
                        <p class="text-sm {{ ($overview['revenue_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($overview['revenue_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format(($overview['revenue_change'] ?? 0), 1) }}% from previous period
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(($overview['conversion_rate'] ?? 0), 1) }}%</p>
                    @if(($overview['conversion_change'] ?? null) !== null)
                        <p class="text-sm {{ ($overview['conversion_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($overview['conversion_change'] ?? 0) >= 0 ? '+' : '' }}{{ number_format(($overview['conversion_change'] ?? 0), 1) }}% from previous period
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H2v-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">ROI</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(($overview['roi'] ?? 0), 1) }}%</p>
                    <p class="text-sm text-gray-500">Cost per lead: ${{ number_format(($overview['cost_per_lead'] ?? 0), 2) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Performance Trend</h3>
                <div class="flex space-x-2">
                    <button onclick="changeChartType('line')" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded {{ ($chartType ?? '') == 'line' ? 'bg-blue-600 text-white' : '' }}">Line</button>
                    <button onclick="changeChartType('bar')" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded {{ ($chartType ?? '') == 'bar' ? 'bg-blue-600 text-white' : '' }}">Bar</button>
                </div>
            </div>
            <div class="relative h-72">
                <canvas id="trendChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Conversion Funnel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Status Distribution</h3>
            <div class="relative h-72">
                <canvas id="conversionChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Performance Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Source Performance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Sources</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 text-sm font-medium text-gray-600">Source</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-600">Leads</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-600">Revenue</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-600">ROI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($sourcePerformance ?? []) as $source)
                        <tr class="border-b border-gray-100">
                            <td class="py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $source['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $source['type'] }}</p>
                                </div>
                            </td>
                            <td class="text-right py-3">
                                <p class="font-medium text-gray-900">{{ $source['total_leads'] }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($source['conversion_rate'], 1) }}% conv.</p>
                            </td>
                            <td class="text-right py-3">
                                <p class="font-medium text-gray-900">${{ number_format($source['total_revenue'], 0) }}</p>
                                <p class="text-sm text-gray-500">${{ number_format($source['cost_per_lead'], 0) }}/lead</p>
                            </td>
                            <td class="text-right py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $source['roi'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ number_format($source['roi'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Campaign Performance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Campaigns</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 text-sm font-medium text-gray-600">Campaign</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-600">Leads</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-600">Revenue</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-600">Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($campaignPerformance ?? []) as $campaign)
                        <tr class="border-b border-gray-100">
                            <td class="py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $campaign['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $campaign['type'] }} • {{ $campaign['status'] }}</p>
                                </div>
                            </td>
                            <td class="text-right py-3">
                                <p class="font-medium text-gray-900">{{ $campaign['total_leads'] }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($campaign['conversion_rate'], 1) }}% conv.</p>
                            </td>
                            <td class="text-right py-3">
                                <p class="font-medium text-gray-900">${{ number_format($campaign['total_revenue'], 0) }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($campaign['roi'], 1) }}% ROI</p>
                            </td>
                            <td class="text-right py-3">
                                <p class="font-medium text-gray-900">${{ number_format($campaign['budget'], 0) }}</p>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($campaign['budget_utilization'], 100) }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($campaign['budget_utilization'], 1) }}% used</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Analytics -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Analytics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">${{ number_format(($revenueAnalytics['total_revenue'] ?? 0), 2) }}</p>
                <p class="text-sm text-gray-600">Total Revenue</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">${{ number_format(($revenueAnalytics['avg_deal_size'] ?? 0), 2) }}</p>
                <p class="text-sm text-gray-600">Average Deal Size</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">${{ number_format(($revenueAnalytics['largest_deal'] ?? 0), 2) }}</p>
                <p class="text-sm text-gray-600">Largest Deal</p>
            </div>
        </div>
    </div>

    <!-- Trend Analysis -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Analysis</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach(($trendAnalysis ?? []) as $key => $trend)
            <div class="text-center">
                <div class="flex items-center justify-center mb-2">
                    <span class="text-2xl font-bold text-gray-900">
                        @if($key === 'avg_value')
                            ${{ number_format(($trend['current'] ?? 0), 2) }}
                        @else
                            {{ number_format(($trend['current'] ?? 0)) }}
                        @endif
                    </span>
                    @if(($trend['change'] ?? null) !== null)
                        <span class="ml-2 flex items-center text-sm {{ ($trend['direction'] ?? '') === 'up' ? 'text-green-600' : (($trend['direction'] ?? '') === 'down' ? 'text-red-600' : 'text-gray-600') }}">
                            @if(($trend['direction'] ?? '') === 'up')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(($trend['direction'] ?? '') === 'down')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            {{ number_format(abs(($trend['change'] ?? 0)), 1) }}%
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
// Chart data from backend
const chartData = @json($chartData ?? []);
const conversionData = @json($conversionMetrics ?? []);

// Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: '{{ $chartType ?? 'line' }}',
    data: {
        labels: chartData.labels,
        datasets: [{
            label: chartData.metric.charAt(0).toUpperCase() + chartData.metric.slice(1),
            data: chartData.data,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Conversion Chart
const conversionCtx = document.getElementById('conversionChart').getContext('2d');
const conversionChart = new Chart(conversionCtx, {
    type: 'doughnut',
    data: {
        labels: ['New', 'Contacted', 'Qualified', 'Proposal', 'Negotiation', 'Closed', 'Lost'],
        datasets: [{
            data: [
                conversionData.new,
                conversionData.contacted,
                conversionData.qualified,
                conversionData.proposal,
                conversionData.negotiation,
                conversionData.closed,
                conversionData.lost
            ],
            backgroundColor: [
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#6B7280'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Functions
function changeChartType(type) {
    const url = new URL(window.location);
    url.searchParams.set('chart_type', type);
    window.location = url;
}

function exportReport(format) {
    if (format === 'pdf') {
        const baseUrl = `${window.location.origin}/reports/export-pdf`;
        const params = new URLSearchParams();
        const currentParams = new URLSearchParams(window.location.search);

        const getVal = (id) => document.getElementById(id)?.value || '';
        const dateFrom = getVal('date_from') || currentParams.get('date_from') || '';
        const dateTo = getVal('date_to') || currentParams.get('date_to') || '';
        const sourceId = getVal('source_id') || currentParams.get('source_id') || '';
        const campaignId = getVal('campaign_id') || currentParams.get('campaign_id') || '';
        const metric = getVal('metric') || currentParams.get('metric') || 'leads';
        const chartType = currentParams.get('chart_type') || 'line';
        const status = getVal('status') || currentParams.get('status') || '';

        if (dateFrom) params.set('date_from', dateFrom);
        if (dateTo) params.set('date_to', dateTo);
        if (sourceId) params.set('source_id', sourceId);
        if (campaignId) params.set('campaign_id', campaignId);
        if (metric) params.set('metric', metric);
        if (chartType) params.set('chart_type', chartType);
        if (status) params.set('status', status);

        const url = `${baseUrl}?${params.toString()}`;
        window.open(url, '_blank');
        return;
    }
    // CSV
    const url = new URL(window.location);
    url.searchParams.set('export', 'csv');
    const statusVal = document.getElementById('status')?.value || '';
    if (statusVal) {
        url.searchParams.set('status', statusVal);
    }
    window.location = url;
}
</script>
@endsection
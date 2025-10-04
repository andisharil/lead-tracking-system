@extends('layouts.app')

@section('title', 'Ad Spend Analytics')

@section('page-title', 'Ad Spend Analytics')

@section('page-description', 'Detailed breakdown of advertising costs vs returns')

@section('header-actions')
    <button onclick="exportData()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export Data
    </button>
    <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Refresh
    </button>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('ad-spend-analytics.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select name="date_range" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="7" {{ ($dateRange ?? '7') == '7' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ ($dateRange ?? '') == '30' ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ ($dateRange ?? '') == '90' ? 'selected' : '' }}>Last 90 days</option>
                    <option value="365" {{ ($dateRange ?? '') == '365' ? 'selected' : '' }}>Last year</option>
                    <option value="custom" {{ ($dateRange ?? '') == 'custom' ? 'selected' : '' }}>Custom range</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                <select name="source" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Sources</option>
                    @foreach(($sources ?? []) as $sourceOption)
                        <option value="{{ $sourceOption->id }}" {{ ($source ?? '') == $sourceOption->id ? 'selected' : '' }}>
                            {{ $sourceOption->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Campaign</label>
                <select name="campaign" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Campaigns</option>
                    @foreach(($campaigns ?? []) as $campaignOption)
                        <option value="{{ $campaignOption->id }}" {{ ($campaign ?? '') == $campaignOption->id ? 'selected' : '' }}>
                            {{ $campaignOption->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                <select name="platform" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Platforms</option>
                    @foreach(($platforms ?? []) as $platformOption)
                        <option value="{{ $platformOption }}" {{ ($platform ?? '') == $platformOption ? 'selected' : '' }}>
                            {{ ucfirst($platformOption) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Overview Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Spend</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format(($overview['total_spend'] ?? 0), 2) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format(($overview['total_revenue'] ?? 0), 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">ROI</p>
                    <p class="text-2xl font-bold {{ ($overview['roi'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format(($overview['roi'] ?? 0), 1) }}%
                    </p>
                </div>
                <div class="p-3 {{ ($overview['roi'] ?? 0) >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-full">
                    <svg class="w-6 h-6 {{ ($overview['roi'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">ROAS</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format(($overview['roas'] ?? 0), 2) }}x</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Cost Per Lead</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format(($overview['cost_per_lead'] ?? 0), 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Cost Per Conversion</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format(($overview['cost_per_conversion'] ?? 0), 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Profit</p>
                    <p class="text-2xl font-bold {{ ($overview['profit'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format(($overview['profit'] ?? 0), 2) }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Leads</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(($overview['total_leads'] ?? 0)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Spend Trend Chart -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Daily Spend Trend</h2>
            <div class="flex space-x-2">
                <button onclick="toggleTrendMetric('spend')" id="trend-spend-btn" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg">
                    Spend
                </button>
                <button onclick="toggleTrendMetric('revenue')" id="trend-revenue-btn" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-lg">
                    Revenue
                </button>
                <button onclick="toggleTrendMetric('profit')" id="trend-profit-btn" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-lg">
                    Profit
                </button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="dailyTrendChart"></canvas>
        </div>
    </div>

    <!-- Platform Performance -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Platform Performance</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaigns</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spend</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Used</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(($platformPerformance ?? []) as $platform)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ ucfirst($platform['platform']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $platform['campaigns'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${{ number_format($platform['budget'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-red-600">${{ number_format($platform['spend'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-green-600">${{ number_format($platform['revenue'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $platform['roi'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($platform['roi'], 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full progress-bar" data-width="{{ min($platform['budget_utilization'], 100) }}"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ number_format($platform['budget_utilization'], 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Source Breakdown -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Source Breakdown</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spend</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leads</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost/Lead</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conv. Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(($sourceBreakdown ?? []) as $source)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $source['name'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ ucfirst($source['type']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-red-600">${{ number_format($source['spend'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-green-600">${{ number_format($source['revenue'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $source['leads'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${{ number_format($source['cost_per_lead'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $source['roi'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($source['roi'], 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($source['conversion_rate'], 1) }}%</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Campaign Breakdown -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Campaign Breakdown</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spend</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Used</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(($campaignBreakdown ?? []) as $campaign)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $campaign['name'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ ucfirst($campaign['platform']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${{ number_format($campaign['budget'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-red-600">${{ number_format($campaign['spend'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-green-600">${{ number_format($campaign['revenue'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $campaign['roi'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($campaign['roi'], 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full progress-bar" data-width="{{ min($campaign['budget_utilization'], 100) }}"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ number_format($campaign['budget_utilization'], 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('campaigns.show', $campaign['id']) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('campaigns.edit', $campaign['id']) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ROI Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Performers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Top Performers (ROI)</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Sources</h3>
                    @foreach((($roiAnalysis['top_sources'] ?? collect())->take(3)) as $source)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-900">{{ $source['name'] }}</span>
                        <span class="text-sm font-medium text-green-600">{{ number_format($source['roi'], 1) }}%</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Campaigns</h3>
                    @foreach((($roiAnalysis['top_campaigns'] ?? collect())->take(3)) as $campaign)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-900">{{ $campaign['name'] }}</span>
                        <span class="text-sm font-medium text-green-600">{{ number_format($campaign['roi'], 1) }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Worst Performers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Needs Attention (ROI)</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Sources</h3>
                    @foreach((($roiAnalysis['worst_sources'] ?? collect())->take(3)) as $source)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-900">{{ $source['name'] }}</span>
                        <span class="text-sm font-medium text-red-600">{{ number_format($source['roi'], 1) }}%</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Campaigns</h3>
                    @foreach($roiAnalysis['worst_campaigns']->take(3) as $campaign)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-900">{{ $campaign['name'] }}</span>
                        <span class="text-sm font-medium text-red-600">{{ number_format($campaign['roi'], 1) }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Utilization -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Budget Utilization</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spend</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Efficiency</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(($budgetUtilization ?? []) as $budget)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $budget['campaign_name'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ ucfirst($budget['platform']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${{ number_format($budget['budget'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-red-600">${{ number_format($budget['spend'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm {{ $budget['remaining'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($budget['remaining'], 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="{{ $budget['utilization'] > 90 ? 'bg-red-600' : ($budget['utilization'] > 70 ? 'bg-yellow-600' : 'bg-green-600') }} h-2 rounded-full progress-bar" data-width="{{ min($budget['utilization'], 100) }}"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ number_format($budget['utilization'], 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($budget['efficiency'], 2) }}x</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
// Daily Trend Chart
let dailyTrendChart;
const dailyTrendData = @json($dailySpendTrend ?? []);
let currentTrendMetric = 'spend';

function initializeDailyTrendChart() {
    const ctx = document.getElementById('dailyTrendChart').getContext('2d');
    
    dailyTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyTrendData.map(d => d.date),
            datasets: [{
                label: 'Spend',
                data: dailyTrendData.map(d => d.spend),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
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
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function toggleTrendMetric(metric) {
    currentTrendMetric = metric;
    
    // Update button styles
    document.querySelectorAll('[id^="trend-"]').forEach(btn => {
        btn.className = 'px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-lg';
    });
    document.getElementById(`trend-${metric}-btn`).className = 'px-3 py-1 text-sm bg-blue-600 text-white rounded-lg';
    
    // Update chart data
    let data, label, color;
    switch(metric) {
        case 'spend':
            data = dailyTrendData.map(d => d.spend);
            label = 'Spend';
            color = 'rgb(239, 68, 68)';
            break;
        case 'revenue':
            data = dailyTrendData.map(d => d.revenue);
            label = 'Revenue';
            color = 'rgb(34, 197, 94)';
            break;
        case 'profit':
            data = dailyTrendData.map(d => d.profit);
            label = 'Profit';
            color = 'rgb(59, 130, 246)';
            break;
    }
    
    dailyTrendChart.data.datasets[0].data = data;
    dailyTrendChart.data.datasets[0].label = label;
    dailyTrendChart.data.datasets[0].borderColor = color;
    dailyTrendChart.data.datasets[0].backgroundColor = color.replace('rgb', 'rgba').replace(')', ', 0.1)');
    dailyTrendChart.update();
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("ad-spend-analytics.export") }}?' + params.toString();
    
    fetch(exportUrl)
        .then(response => response.json())
        .then(data => {
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'ad-spend-analytics-' + new Date().toISOString().split('T')[0] + '.json';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Export failed:', error);
            alert('Export failed. Please try again.');
        });
}

function refreshData() {
    window.location.reload();
}

function applyProgressBarWidths() {
    document.querySelectorAll('.progress-bar').forEach(el => {
        const w = parseFloat(el.dataset.width || '0');
        const safe = Math.min(100, Math.max(0, isNaN(w) ? 0 : w));
        el.style.width = safe + '%';
    });
}

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDailyTrendChart();
    applyProgressBarWidths();
});
</script>
@endsection
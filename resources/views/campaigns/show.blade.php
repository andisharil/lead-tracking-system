@extends('layouts.app')

@section('title', $campaign->name)
@section('page-title', $campaign->name)
@section('page-description', 'View campaign details and performance metrics')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('campaigns.edit', $campaign) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <i class="fas fa-edit mr-2"></i>Edit Campaign
    </a>
    <a href="{{ route('campaigns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Back to Campaigns
    </a>
</div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Campaign Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Campaign Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Campaign Name</label>
                    <p class="text-gray-900 font-medium">{{ $campaign->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Platform</label>
                    <p class="text-gray-900">{{ $campaign->platform }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst($campaign->type) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($campaign->status === 'active') bg-green-100 text-green-800
                        @elseif($campaign->status === 'paused') bg-yellow-100 text-yellow-800
                        @elseif($campaign->status === 'completed') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tracking Code</label>
                    <p class="text-gray-900 font-mono text-sm">{{ $campaign->tracking_code ?: 'Not set' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Budget</label>
                    <p class="text-gray-900 font-medium">${{ number_format($campaign->budget ?? 0, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Daily Budget</label>
                    <p class="text-gray-900">${{ number_format($campaign->daily_budget ?? 0, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Target Audience</label>
                    <p class="text-gray-900">{{ $campaign->target_audience ?: 'Not specified' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                    <p class="text-gray-900">{{ $campaign->description ?: 'No description provided' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Start Date</label>
                    <p class="text-gray-900">{{ $campaign->start_date ? $campaign->start_date->format('M j, Y') : 'Not set' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">End Date</label>
                    <p class="text-gray-900">{{ $campaign->end_date ? $campaign->end_date->format('M j, Y') : 'Not set' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                    <p class="text-gray-900">{{ $campaign->created_at->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-gray-900">{{ $campaign->updated_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Stats</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Leads</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $metrics['leads_count'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Conversions</span>
                    <span class="text-2xl font-bold text-green-600">{{ $metrics['conversions'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Conversion Rate</span>
                    <span class="text-2xl font-bold text-purple-600">{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Revenue</span>
                    <span class="text-2xl font-bold text-indigo-600">${{ number_format($metrics['total_revenue'] ?? 0, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">ROI</span>
                    <span class="text-2xl font-bold {{ ($metrics['roi'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($metrics['roi'] ?? 0, 1) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Performance Trend (Last 30 Days)</h2>
            <div class="flex space-x-2">
                <button onclick="updateChart('leads')" class="chart-toggle px-3 py-1 text-sm rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors" data-metric="leads">
                    Leads
                </button>
                <button onclick="updateChart('conversions')" class="chart-toggle px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors" data-metric="conversions">
                    Conversions
                </button>
                <button onclick="updateChart('revenue')" class="chart-toggle px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors" data-metric="revenue">
                    Revenue
                </button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Detailed Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Leads</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $metrics['leads_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Conversions</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $metrics['conversions'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-times-circle text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Lost Leads</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ ($metrics['leads_count'] ?? 0) - ($metrics['conversions'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Conversion Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg Deal Size</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format(($metrics['conversions'] ?? 0) > 0 ? ($metrics['total_revenue'] ?? 0) / $metrics['conversions'] : 0, 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($metrics['total_revenue'] ?? 0, 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-coins text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Cost Per Lead</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format(($metrics['leads_count'] ?? 0) > 0 ? ($campaign->budget ?? 0) / $metrics['leads_count'] : 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-{{ ($metrics['roi'] ?? 0) >= 0 ? 'green' : 'red' }}-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-chart-pie text-{{ ($metrics['roi'] ?? 0) >= 0 ? 'green' : 'red' }}-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">ROI</p>
                    <p class="text-2xl font-semibold text-{{ ($metrics['roi'] ?? 0) >= 0 ? 'green' : 'red' }}-600">{{ number_format($metrics['roi'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Recent Leads from this Campaign</h2>
                <a href="{{ route('leads.index', ['campaign' => $campaign->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    View All Leads
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentLeads ?? [] as $lead)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $lead->name }}</div>
                                <div class="text-sm text-gray-500">{{ $lead->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($lead->status === 'converted') bg-green-100 text-green-800
                                @elseif($lead->status === 'qualified') bg-blue-100 text-blue-800
                                @elseif($lead->status === 'contacted') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($lead->value ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $lead->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('leads.show', $lead) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No leads found for this campaign yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
let performanceChart;
const chartData = @json($chartData ?? []);

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels || [],
            datasets: [{
                label: 'Leads',
                data: chartData.leads || [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

// Update chart function
function updateChart(metric) {
    // Update button states
    document.querySelectorAll('.chart-toggle').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-700');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    const activeBtn = document.querySelector(`[data-metric="${metric}"]`);
    activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
    activeBtn.classList.add('bg-blue-100', 'text-blue-700');
    
    // Update chart data
    const colors = {
        leads: { border: 'rgb(59, 130, 246)', bg: 'rgba(59, 130, 246, 0.1)' },
        conversions: { border: 'rgb(34, 197, 94)', bg: 'rgba(34, 197, 94, 0.1)' },
        revenue: { border: 'rgb(168, 85, 247)', bg: 'rgba(168, 85, 247, 0.1)' }
    };
    
    performanceChart.data.datasets[0] = {
        label: metric.charAt(0).toUpperCase() + metric.slice(1),
        data: chartData[metric] || [],
        borderColor: colors[metric].border,
        backgroundColor: colors[metric].bg,
        tension: 0.1
    };
    
    performanceChart.update();
}
</script>
@endsection
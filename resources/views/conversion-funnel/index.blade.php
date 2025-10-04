@extends('layouts.app')

@section('title', 'Conversion Funnel')

@section('page-title', 'Conversion Funnel')

@section('page-description', 'Visual representation of lead progression through stages')

@section('header-actions')
    <button onclick="exportFunnelData()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export Data
    </button>
    <button onclick="refreshFunnel()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Refresh
    </button>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <form method="GET" action="{{ route('conversion-funnel.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ ($dateFrom ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ ($dateTo ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                <select name="source_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Sources</option>
                    @foreach(($sources ?? []) as $source)
                        <option value="{{ $source->id }}" {{ ($sourceId ?? '') == $source->id ? 'selected' : '' }}>
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Campaign</label>
                <select name="campaign_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Campaigns</option>
                    @foreach(($campaigns ?? []) as $campaign)
                        <option value="{{ $campaign->id }}" {{ ($campaignId ?? '') == $campaign->id ? 'selected' : '' }}>
                            {{ $campaign->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overall Conversion Rate</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format(($conversionRates['overall_conversion_rate'] ?? 0), 1) }}%</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Leads</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format(($conversionRates['total_leads'] ?? 0)) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Closed Won</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format(($conversionRates['closed_leads'] ?? 0)) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg. Time to Close</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format(($funnelVelocity['avg_time_to_close'] ?? 0), 1) }} days</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Funnel Visualization -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Funnel</h3>
                <div class="funnel-container" id="funnelChart">
                    @foreach(($funnelData ?? []) as $index => $stage)
                        <div class="funnel-stage" data-stage="{{ $stage['stage'] }}" onclick="showStageDetails('{{ $stage['stage'] }}')"
                             style="width: {{ 100 - ($index * 10) }}%; background: linear-gradient(135deg, 
                             @if($index == 0) #3B82F6, #1E40AF
                             @elseif($index == 1) #10B981, #059669
                             @elseif($index == 2) #F59E0B, #D97706
                             @elseif($index == 3) #EF4444, #DC2626
                             @elseif($index == 4) #8B5CF6, #7C3AED
                             @elseif($index == 5) #06B6D4, #0891B2
                             @else #6B7280, #4B5563
                             @endif);">
                            <div class="funnel-stage-content">
                                <div class="funnel-stage-label">{{ $stage['label'] }}</div>
                                <div class="funnel-stage-count">{{ number_format($stage['count']) }}</div>
                                <div class="funnel-stage-percentage">{{ number_format($stage['percentage'], 1) }}%</div>
                                @if($stage['total_value'] > 0)
                                    <div class="funnel-stage-value">${{ number_format($stage['total_value']) }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Stage Performance Over Time -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stage Performance Over Time</h3>
                <canvas id="stagePerformanceChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Conversion Rates -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stage Conversion Rates</h3>
                <div class="space-y-3">
                    @foreach(($conversionRates['stage_conversions'] ?? []) as $conversion)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($conversion['from_stage']) }} → {{ ucfirst($conversion['to_stage']) }}</p>
                                <p class="text-xs text-gray-600">{{ $conversion['from_count'] }} → {{ $conversion['to_count'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold 
                                    @if($conversion['conversion_rate'] >= 70) text-green-600
                                    @elseif($conversion['conversion_rate'] >= 50) text-yellow-600
                                    @else text-red-600
                                    @endif">
                                    {{ number_format($conversion['conversion_rate'], 1) }}%
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Drop-off Analysis -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Drop-off Analysis</h3>
                @if(($dropOffAnalysis['highest_dropoff'] ?? null))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-semibold text-red-800">Highest Drop-off</h4>
                        <p class="text-sm text-red-700">{{ ucfirst(($dropOffAnalysis['highest_dropoff']['stage'] ?? '')) }} → {{ ucfirst(($dropOffAnalysis['highest_dropoff']['next_stage'] ?? '')) }}</p>
                        <p class="text-lg font-bold text-red-800">{{ number_format(($dropOffAnalysis['highest_dropoff']['drop_off_rate'] ?? 0), 1) }}%</p>
                        <p class="text-xs text-red-600">{{ ($dropOffAnalysis['highest_dropoff']['drop_off_count'] ?? 0) }} leads lost</p>
                    </div>
                @endif
                
                <div class="space-y-2">
                    @foreach(($dropOffAnalysis['stage_dropoffs'] ?? []) as $dropoff)
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-700">{{ ucfirst($dropoff['stage']) }}</span>
                            <span class="text-sm font-medium text-red-600">-{{ number_format($dropoff['drop_off_rate'], 1) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Funnel Velocity -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stage Velocity</h3>
                <div class="space-y-3">
                    @foreach(($funnelVelocity['stage_velocity'] ?? []) as $stage => $velocity)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ str_replace('_', ' → ', ucwords(str_replace('_', ' ', $stage), '_')) }}</p>
                                <p class="text-xs text-gray-600">Median: {{ $velocity['median_days'] }} days</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-blue-600">{{ number_format($velocity['avg_days'], 1) }} days</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Source & Campaign Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Source Comparison -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Sources by Conversion Rate</h3>
            <div class="space-y-3">
                @foreach(($sourceFunnelComparison ?? collect())->take(5) as $source)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $source['source_name'] }}</p>
                            <p class="text-xs text-gray-600">{{ $source['total_leads'] }} leads • ${{ number_format($source['total_value']) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">{{ number_format($source['conversion_rate'], 1) }}%</p>
                            <p class="text-xs text-gray-600">${{ number_format($source['avg_deal_size']) }} avg</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Campaign Comparison -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Campaigns by Conversion Rate</h3>
            <div class="space-y-3">
                @foreach(($campaignFunnelComparison ?? collect())->take(5) as $campaign)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $campaign['campaign_name'] }}</p>
                            <p class="text-xs text-gray-600">{{ $campaign['total_leads'] }} leads • ${{ number_format($campaign['total_value']) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">{{ number_format($campaign['conversion_rate'], 1) }}%</p>
                            <p class="text-xs text-gray-600">${{ number_format($campaign['avg_deal_size']) }} avg</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Stage Details Modal -->
<div id="stageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Stage Details</h3>
                    <button onclick="closeStageModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6" id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
.funnel-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px 0;
}

.funnel-stage {
    position: relative;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    clip-path: polygon(0 0, calc(100% - 30px) 0, 100% 50%, calc(100% - 30px) 100%, 0 100%, 30px 50%);
    margin: 2px 0;
}

.funnel-stage:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.funnel-stage-content {
    text-align: center;
    padding: 0 40px;
}

.funnel-stage-label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.funnel-stage-count {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 2px;
}

.funnel-stage-percentage {
    font-size: 12px;
    opacity: 0.9;
}

.funnel-stage-value {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 2px;
}
</style>


<script>
// Stage Performance Chart
const stageCtx = document.getElementById('stagePerformanceChart').getContext('2d');
const stageChart = new Chart(stageCtx, {
    type: 'line',
    data: {
        labels: @json(($stagePerformance['weeks'] ?? [])),
        datasets: [
            {
                label: 'New',
                data: @json((($stagePerformance['stage_data'] ?? [])['new'] ?? [])),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            },
            {
                label: 'Contacted',
                data: @json((($stagePerformance['stage_data'] ?? [])['contacted'] ?? [])),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            },
            {
                label: 'Qualified',
                data: @json((($stagePerformance['stage_data'] ?? [])['qualified'] ?? [])),
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4
            },
            {
                label: 'Proposal',
                data: @json((($stagePerformance['stage_data'] ?? [])['proposal'] ?? [])),
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            },
            {
                label: 'Negotiation',
                data: @json((($stagePerformance['stage_data'] ?? [])['negotiation'] ?? [])),
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4
            },
            {
                label: 'Closed',
                data: @json((($stagePerformance['stage_data'] ?? [])['closed'] ?? [])),
                borderColor: '#06B6D4',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                tension: 0.4
            }
        ]
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
                position: 'bottom'
            }
        }
    }
});

// Show stage details
function showStageDetails(stage) {
    const modal = document.getElementById('stageModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    modalTitle.textContent = `${stage.charAt(0).toUpperCase() + stage.slice(1)} Stage Details`;
    modalContent.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Loading...</p></div>';
    
    modal.classList.remove('hidden');
    
    // Fetch stage details
    const params = new URLSearchParams({
        stage: stage,
        date_from: '{{ ($dateFrom ?? '') }}',
        date_to: '{{ ($dateTo ?? '') }}',
        source_id: '{{ ($sourceId ?? '') }}',
        campaign_id: '{{ ($campaignId ?? '') }}'
    });
    
    fetch(`{{ route('conversion-funnel.stage-details') }}?${params}`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
            html += '<thead class="bg-gray-50"><tr>';
            html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>';
            html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>';
            html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>';
            html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>';
            html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>';
            html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
            
            data.leads.forEach(lead => {
                html += '<tr>';
                html += `<td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">${lead.name}</div><div class="text-sm text-gray-500">${lead.email}</div></td>`;
                html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${lead.source ? lead.source.name : 'N/A'}</td>`;
                html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${lead.campaign ? lead.campaign.name : 'N/A'}</td>`;
                html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${lead.value ? parseFloat(lead.value).toLocaleString() : '0'}</td>`;
                html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(lead.created_at).toLocaleDateString()}</td>`;
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            
            if (data.pagination.total > 20) {
                html += `<div class="mt-4 text-sm text-gray-600 text-center">Showing 20 of ${data.pagination.total} leads</div>`;
            }
            
            modalContent.innerHTML = html;
        })
        .catch(error => {
            modalContent.innerHTML = '<div class="text-center py-8 text-red-600">Error loading stage details</div>';
        });
}

function closeStageModal() {
    document.getElementById('stageModal').classList.add('hidden');
}

function refreshFunnel() {
    window.location.reload();
}

function exportFunnelData() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.open(`{{ route('conversion-funnel.index') }}?${params}`, '_blank');
}

// Close modal when clicking outside
document.getElementById('stageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStageModal();
    }
});
</script>
@endsection
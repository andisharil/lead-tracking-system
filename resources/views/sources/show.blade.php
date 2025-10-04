@extends('layouts.app')

@section('title', $source->name)

@section('page-title')
    <div class="flex items-center space-x-4">
        <span>{{ $source->name }}</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            @switch($source->type)
                @case('website') bg-blue-100 text-blue-800 @break
                @case('social_media') bg-purple-100 text-purple-800 @break
                @case('email') bg-green-100 text-green-800 @break
                @case('referral') bg-yellow-100 text-yellow-800 @break
                @case('advertising') bg-red-100 text-red-800 @break
                @case('direct') bg-gray-100 text-gray-800 @break
                @default bg-gray-100 text-gray-800
            @endswitch">
            {{ ucfirst(str_replace('_', ' ', $source->type)) }}
        </span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            {{ $source->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ ucfirst($source->status) }}
        </span>
    </div>
@endsection

@section('page-description', 'View source details and performance metrics')

@section('header-actions')
    <div class="flex space-x-3">
        <a href="{{ route('sources.edit', $source->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-edit mr-2"></i>Edit Source
        </a>
        <a href="{{ route('sources.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Sources
        </a>
    </div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Source Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Source Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $source->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $source->type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($source->status) }}</p>
                </div>
                @if($source->tracking_code)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tracking Code</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $source->tracking_code }}</p>
                </div>
                @endif
                @if($source->cost_per_lead)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cost Per Lead</label>
                    <p class="mt-1 text-sm text-gray-900">${{ number_format($source->cost_per_lead, 2) }}</p>
                </div>
                @endif
                @if($source->monthly_budget)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Monthly Budget</label>
                    <p class="mt-1 text-sm text-gray-900">${{ number_format($source->monthly_budget, 2) }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ date('M j, Y g:i A', strtotime($source->created_at)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ date('M j, Y g:i A', strtotime($source->updated_at)) }}</p>
                </div>
            </div>
            @if($source->description)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <p class="mt-1 text-sm text-gray-900">{{ $source->description }}</p>
            </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Leads</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics->total_leads }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Conversions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics->conversions }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics->conversion_rate }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($metrics->total_revenue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Trend (Last 30 Days)</h3>
            <div class="h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Performance Metrics -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Total Leads</span>
                        <span class="text-sm font-bold text-gray-900">{{ $metrics->total_leads }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Conversions</span>
                        <span class="text-sm font-bold text-green-600">{{ $metrics->conversions }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Lost Leads</span>
                        <span class="text-sm font-bold text-red-600">{{ $metrics->lost }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Conversion Rate</span>
                        <span class="text-sm font-bold text-gray-900">{{ $metrics->conversion_rate }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Average Deal Size</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($metrics->avg_deal_size, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Total Revenue</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($metrics->total_revenue, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Leads</h3>
                <div class="space-y-3">
                    @forelse($recentLeads as $lead)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $lead->name }}</p>
                                <p class="text-xs text-gray-500">{{ $lead->email }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @switch($lead->status)
                                        @case('new') bg-blue-100 text-blue-800 @break
                                        @case('contacted') bg-yellow-100 text-yellow-800 @break
                                        @case('qualified') bg-purple-100 text-purple-800 @break
                                        @case('proposal') bg-orange-100 text-orange-800 @break
                                        @case('closed_won') bg-green-100 text-green-800 @break
                                        @case('closed_lost') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ date('M j', strtotime($lead->created_at)) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No leads found for this source</p>
                    @endforelse
                </div>
                @if(count($recentLeads) > 0)
                    <div class="mt-4 text-center">
                        <a href="{{ route('leads.index', ['source' => $source->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Leads →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script>
// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const chartData = @json($performanceChart);

const labels = chartData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});

const leadsData = chartData.map(item => item.leads_count);
const conversionsData = chartData.map(item => item.conversions);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Leads',
            data: leadsData,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }, {
            label: 'Conversions',
            data: conversionsData,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        }
    }
});
</script>
@endsection
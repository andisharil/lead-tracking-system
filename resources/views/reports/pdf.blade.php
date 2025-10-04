@extends('layouts.app')

@section('title', 'Reports Export - PDF')
@section('page-title', 'Reports Export')
@section('page-description', 'Ready to Print or Save as PDF')

@section('content')
<div class="bg-white p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reports Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 text-sm text-gray-600">
                <div>
                    <p><span class="font-semibold">Date Range:</span> {{ $dateFrom }} to {{ $dateTo }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">Source:</span> {{ $selectedSourceName ?? 'All Sources' }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">Campaign:</span> {{ $selectedCampaignName ?? 'All Campaigns' }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">Status:</span> {{ $selectedStatusName ?? 'All Status' }}</p>
                </div>
            </div>
        </div>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md print:hidden">Print / Save as PDF</button>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="border rounded p-4">
            <p class="text-sm text-gray-600">Total Leads</p>
            <p class="text-2xl font-bold">{{ number_format($overview['total_leads'] ?? 0) }}</p>
        </div>
        <div class="border rounded p-4">
            <p class="text-sm text-gray-600">Total Revenue</p>
            <p class="text-2xl font-bold">${{ number_format($overview['total_revenue'] ?? 0, 2) }}</p>
        </div>
        <div class="border rounded p-4">
            <p class="text-sm text-gray-600">Conversion Rate</p>
            <p class="text-2xl font-bold">{{ number_format($overview['conversion_rate'] ?? 0, 1) }}%</p>
        </div>
        <div class="border rounded p-4">
            <p class="text-sm text-gray-600">ROI</p>
            <p class="text-2xl font-bold">{{ number_format($overview['roi'] ?? 0, 1) }}%</p>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Leads</h3>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Date</th>
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Email</th>
                    <th class="text-left py-2">Phone</th>
                    <th class="text-left py-2">Source</th>
                    <th class="text-left py-2">Campaign</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-right py-2">Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $lead)
                <tr class="border-b">
                    <td class="py-2">{{ $lead->created_at->format('Y-m-d') }}</td>
                    <td class="py-2">{{ $lead->name }}</td>
                    <td class="py-2">{{ $lead->email }}</td>
                    <td class="py-2">{{ $lead->phone }}</td>
                    <td class="py-2">{{ $lead->source->name ?? 'N/A' }}</td>
                    <td class="py-2">{{ $lead->campaign->name ?? 'N/A' }}</td>
                    <td class="py-2">{{ ucfirst($lead->status) }}</td>
                    <td class="py-2 text-right">${{ number_format($lead->value, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Source Performance -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Top Sources</h3>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Source</th>
                    <th class="text-right py-2">Leads</th>
                    <th class="text-right py-2">Revenue</th>
                    <th class="text-right py-2">ROI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sourcePerformance as $source)
                <tr class="border-b">
                    <td class="py-2">{{ $source['name'] }}</td>
                    <td class="py-2 text-right">{{ $source['total_leads'] }}</td>
                    <td class="py-2 text-right">${{ number_format($source['total_revenue'], 0) }}</td>
                    <td class="py-2 text-right">{{ number_format($source['roi'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Campaign Performance -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Top Campaigns</h3>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Campaign</th>
                    <th class="text-right py-2">Leads</th>
                    <th class="text-right py-2">Revenue</th>
                    <th class="text-right py-2">Budget</th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaignPerformance as $campaign)
                <tr class="border-b">
                    <td class="py-2">{{ $campaign['name'] }}</td>
                    <td class="py-2 text-right">{{ $campaign['total_leads'] }}</td>
                    <td class="py-2 text-right">${{ number_format($campaign['total_revenue'], 0) }}</td>
                    <td class="py-2 text-right">${{ number_format($campaign['budget'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('styles')
<style>
@media print {
    /* Hide interactive/Chrome elements */
    .print\:hidden { display: none !important; }
    header, nav, .no-print, #mobile-sidebar-overlay, .w-64.bg-gray-900 { display: none !important; }

    /* Reset layout to full width for print */
    body { background: #fff; }
    .min-h-screen { min-height: auto !important; }
    main { padding: 0 !important; }
}
</style>
@endsection
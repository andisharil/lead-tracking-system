@extends('layouts.app')

@section('title', 'Ad Spend Details')

@section('page-title', 'Ad Spend Details')

@section('page-description', '{{ $adSpend->source->name }} - {{ $adSpend->spend_date->format("M d, Y") }}')

@section('header-actions')
    <a href="{{ route('ad-spend.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Ad Spend
    </a>
    <a href="{{ route('ad-spend.edit', $adSpend) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium transition-colors">
        Edit Record
    </a>
    <form action="{{ route('ad-spend.destroy', $adSpend) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this ad spend record?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md font-medium transition-colors">
            Delete
        </button>
    </form>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">

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
                        <p class="text-sm font-medium text-gray-600">Amount Spent</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($adSpend->amount_spent, 2) }}</p>
                    </div>
                </div>
            </div>
            
            @if($adSpend->impressions)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Impressions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($adSpend->impressions) }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($adSpend->clicks)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Clicks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($adSpend->clicks) }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($adSpend->conversions)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Conversions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($adSpend->conversions) }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $adSpend->source->type }}</span>
                                <span class="text-gray-900">{{ $adSpend->source->name }}</span>
                            </div>
                        </div>
                        
                        @if($adSpend->campaign)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Campaign</label>
                            <a href="{{ route('campaigns.show', $adSpend->campaign) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $adSpend->campaign->name }}
                            </a>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Spend Date</label>
                            <p class="text-gray-900">{{ $adSpend->spend_date->format('F j, Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount Spent</label>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($adSpend->amount_spent, 2) }}</p>
                        </div>
                    </div>
                    
                    @if($adSpend->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <p class="text-gray-900">{{ $adSpend->description }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Platform & Ad Details -->
                @if($adSpend->platform || $adSpend->ad_type || $adSpend->target_audience)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Platform & Ad Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @if($adSpend->platform)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                            <p class="text-gray-900">{{ $adSpend->platform }}</p>
                        </div>
                        @endif
                        
                        @if($adSpend->ad_type)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ad Type</label>
                            <p class="text-gray-900">{{ $adSpend->ad_type }}</p>
                        </div>
                        @endif
                        
                        @if($adSpend->target_audience)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                            <p class="text-gray-900">{{ $adSpend->target_audience }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Performance Metrics -->
                @if($adSpend->impressions || $adSpend->clicks || $adSpend->conversions)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Performance Metrics</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        @if($adSpend->impressions)
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($adSpend->impressions) }}</div>
                            <div class="text-sm text-gray-600">Impressions</div>
                        </div>
                        @endif
                        
                        @if($adSpend->clicks)
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($adSpend->clicks) }}</div>
                            <div class="text-sm text-gray-600">Clicks</div>
                        </div>
                        @endif
                        
                        @if($adSpend->conversions)
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($adSpend->conversions) }}</div>
                            <div class="text-sm text-gray-600">Conversions</div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Calculated Metrics -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Calculated Metrics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @if($adSpend->impressions && $adSpend->clicks)
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-xl font-bold text-blue-600">{{ number_format(($adSpend->clicks / $adSpend->impressions) * 100, 2) }}%</div>
                                <div class="text-sm text-gray-600">CTR (Click-Through Rate)</div>
                            </div>
                            @endif
                            
                            @if($adSpend->clicks)
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-xl font-bold text-green-600">${{ number_format($adSpend->amount_spent / $adSpend->clicks, 2) }}</div>
                                <div class="text-sm text-gray-600">CPC (Cost Per Click)</div>
                            </div>
                            @endif
                            
                            @if($adSpend->impressions)
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-xl font-bold text-purple-600">${{ number_format(($adSpend->amount_spent / $adSpend->impressions) * 1000, 2) }}</div>
                                <div class="text-sm text-gray-600">CPM (Cost Per Mille)</div>
                            </div>
                            @endif
                            
                            @if($adSpend->conversions)
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <div class="text-xl font-bold text-orange-600">${{ number_format($adSpend->amount_spent / $adSpend->conversions, 2) }}</div>
                                <div class="text-sm text-gray-600">CPA (Cost Per Acquisition)</div>
                            </div>
                            @endif
                            
                            @if($adSpend->clicks && $adSpend->conversions)
                            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                                <div class="text-xl font-bold text-indigo-600">{{ number_format(($adSpend->conversions / $adSpend->clicks) * 100, 2) }}%</div>
                                <div class="text-sm text-gray-600">Conversion Rate</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('ad-spend.edit', $adSpend) }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Record
                        </a>
                        
                        <a href="{{ route('ad-spend.create') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Record
                        </a>
                        
                        <a href="{{ route('ad-spend.analytics') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Analytics
                        </a>
                    </div>
                </div>
                
                <!-- Related Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source Details</label>
                            <a href="{{ route('sources.show', $adSpend->source) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                View {{ $adSpend->source->name }} Details
                            </a>
                        </div>
                        
                        @if($adSpend->campaign)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Details</label>
                            <a href="{{ route('campaigns.show', $adSpend->campaign) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                View {{ $adSpend->campaign->name }} Campaign
                            </a>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                            <p class="text-sm text-gray-600">{{ $adSpend->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        
                        @if($adSpend->updated_at != $adSpend->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                            <p class="text-sm text-gray-600">{{ $adSpend->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
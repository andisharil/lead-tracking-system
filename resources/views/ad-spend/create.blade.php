@extends('layouts.app')

@section('title', 'Add Ad Spend Record')

@section('page-title', 'Add Ad Spend Record')

@section('page-description', 'Track your advertising expenses and performance')

@section('header-actions')
    <a href="{{ route('ad-spend.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Ad Spend
    </a>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">

        <!-- Form -->
        <form action="{{ route('ad-spend.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="source_id" class="block text-sm font-medium text-gray-700 mb-2">Source *</label>
                        <select name="source_id" id="source_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('source_id') border-red-500 @enderror">
                            <option value="">Select a source</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}" {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                    {{ $source->name }} ({{ $source->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('source_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="campaign_id" class="block text-sm font-medium text-gray-700 mb-2">Campaign</label>
                        <select name="campaign_id" id="campaign_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('campaign_id') border-red-500 @enderror">
                            <option value="">Select a campaign (optional)</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('campaign_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="amount_spent" class="block text-sm font-medium text-gray-700 mb-2">Amount Spent *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="amount_spent" id="amount_spent" step="0.01" min="0" value="{{ old('amount_spent') }}" required class="w-full border border-gray-300 rounded-md pl-7 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount_spent') border-red-500 @enderror" placeholder="0.00">
                        </div>
                        @error('amount_spent')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="spend_date" class="block text-sm font-medium text-gray-700 mb-2">Spend Date *</label>
                        <input type="date" name="spend_date" id="spend_date" value="{{ old('spend_date', date('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('spend_date') border-red-500 @enderror">
                        @error('spend_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" placeholder="Optional description of the ad spend...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Platform & Ad Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Platform & Ad Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                        <select name="platform" id="platform" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('platform') border-red-500 @enderror">
                            <option value="">Select platform</option>
                            <option value="Google Ads" {{ old('platform') == 'Google Ads' ? 'selected' : '' }}>Google Ads</option>
                            <option value="Facebook Ads" {{ old('platform') == 'Facebook Ads' ? 'selected' : '' }}>Facebook Ads</option>
                            <option value="Instagram Ads" {{ old('platform') == 'Instagram Ads' ? 'selected' : '' }}>Instagram Ads</option>
                            <option value="LinkedIn Ads" {{ old('platform') == 'LinkedIn Ads' ? 'selected' : '' }}>LinkedIn Ads</option>
                            <option value="Twitter Ads" {{ old('platform') == 'Twitter Ads' ? 'selected' : '' }}>Twitter Ads</option>
                            <option value="TikTok Ads" {{ old('platform') == 'TikTok Ads' ? 'selected' : '' }}>TikTok Ads</option>
                            <option value="YouTube Ads" {{ old('platform') == 'YouTube Ads' ? 'selected' : '' }}>YouTube Ads</option>
                            <option value="Bing Ads" {{ old('platform') == 'Bing Ads' ? 'selected' : '' }}>Bing Ads</option>
                            <option value="Other" {{ old('platform') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('platform')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="ad_type" class="block text-sm font-medium text-gray-700 mb-2">Ad Type</label>
                        <select name="ad_type" id="ad_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('ad_type') border-red-500 @enderror">
                            <option value="">Select ad type</option>
                            <option value="Search" {{ old('ad_type') == 'Search' ? 'selected' : '' }}>Search</option>
                            <option value="Display" {{ old('ad_type') == 'Display' ? 'selected' : '' }}>Display</option>
                            <option value="Video" {{ old('ad_type') == 'Video' ? 'selected' : '' }}>Video</option>
                            <option value="Social" {{ old('ad_type') == 'Social' ? 'selected' : '' }}>Social</option>
                            <option value="Shopping" {{ old('ad_type') == 'Shopping' ? 'selected' : '' }}>Shopping</option>
                            <option value="Remarketing" {{ old('ad_type') == 'Remarketing' ? 'selected' : '' }}>Remarketing</option>
                            <option value="Native" {{ old('ad_type') == 'Native' ? 'selected' : '' }}>Native</option>
                            <option value="Other" {{ old('ad_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('ad_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                        <input type="text" name="target_audience" id="target_audience" value="{{ old('target_audience') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('target_audience') border-red-500 @enderror" placeholder="e.g., Age 25-45, Homeowners">
                        @error('target_audience')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Performance Metrics</h2>
                <p class="text-gray-600 mb-6">Add performance data to track the effectiveness of your ad spend.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="impressions" class="block text-sm font-medium text-gray-700 mb-2">Impressions</label>
                        <input type="number" name="impressions" id="impressions" min="0" value="{{ old('impressions') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('impressions') border-red-500 @enderror" placeholder="0">
                        @error('impressions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="clicks" class="block text-sm font-medium text-gray-700 mb-2">Clicks</label>
                        <input type="number" name="clicks" id="clicks" min="0" value="{{ old('clicks') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('clicks') border-red-500 @enderror" placeholder="0">
                        @error('clicks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="conversions" class="block text-sm font-medium text-gray-700 mb-2">Conversions</label>
                        <input type="number" name="conversions" id="conversions" min="0" value="{{ old('conversions') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('conversions') border-red-500 @enderror" placeholder="0">
                        @error('conversions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Calculated Metrics Display -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Calculated Metrics (Auto-calculated)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">CTR:</span>
                            <span id="ctr-display" class="font-medium text-gray-900">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">CPC:</span>
                            <span id="cpc-display" class="font-medium text-gray-900">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">CPM:</span>
                            <span id="cpm-display" class="font-medium text-gray-900">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('ad-spend.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium transition-colors">
                    Create Ad Spend Record
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calculateMetrics() {
    const amountSpent = parseFloat(document.getElementById('amount_spent').value) || 0;
    const impressions = parseInt(document.getElementById('impressions').value) || 0;
    const clicks = parseInt(document.getElementById('clicks').value) || 0;
    
    // Calculate CTR (Click-Through Rate)
    const ctr = impressions > 0 && clicks > 0 ? (clicks / impressions * 100).toFixed(2) + '%' : '-';
    document.getElementById('ctr-display').textContent = ctr;
    
    // Calculate CPC (Cost Per Click)
    const cpc = clicks > 0 ? '$' + (amountSpent / clicks).toFixed(2) : '-';
    document.getElementById('cpc-display').textContent = cpc;
    
    // Calculate CPM (Cost Per Mille)
    const cpm = impressions > 0 ? '$' + (amountSpent / impressions * 1000).toFixed(2) : '-';
    document.getElementById('cpm-display').textContent = cpm;
}

// Add event listeners to recalculate metrics when values change
document.getElementById('amount_spent').addEventListener('input', calculateMetrics);
document.getElementById('impressions').addEventListener('input', calculateMetrics);
document.getElementById('clicks').addEventListener('input', calculateMetrics);

// Initial calculation
calculateMetrics();
</script>
@endsection
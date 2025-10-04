@extends('layouts.app')

@section('title', 'Edit Campaign')
@section('page-title', 'Edit Campaign')
@section('page-description', 'Update campaign details and settings')

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('campaigns.show', $campaign) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <i class="fas fa-eye mr-2"></i>View Campaign
    </a>
    <a href="{{ route('campaigns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Back to Campaigns
    </a>
</div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Campaign Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $campaign->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Enter campaign name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Platform *</label>
                        <input type="text" name="platform" id="platform" value="{{ old('platform', $campaign->platform) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('platform') border-red-500 @enderror"
                               placeholder="e.g., Google Ads, Facebook, LinkedIn">
                        @error('platform')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Campaign Type *</label>
                        <select name="type" id="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                            <option value="">Select campaign type</option>
                            <option value="search" {{ old('type', $campaign->type) === 'search' ? 'selected' : '' }}>Search</option>
                            <option value="display" {{ old('type', $campaign->type) === 'display' ? 'selected' : '' }}>Display</option>
                            <option value="social" {{ old('type', $campaign->type) === 'social' ? 'selected' : '' }}>Social Media</option>
                            <option value="email" {{ old('type', $campaign->type) === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="video" {{ old('type', $campaign->type) === 'video' ? 'selected' : '' }}>Video</option>
                            <option value="other" {{ old('type', $campaign->type) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="">Select status</option>
                            <option value="draft" {{ old('status', $campaign->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status', $campaign->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ old('status', $campaign->status) === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="completed" {{ old('status', $campaign->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tracking_code" class="block text-sm font-medium text-gray-700 mb-2">Tracking Code</label>
                        <input type="text" name="tracking_code" id="tracking_code" value="{{ old('tracking_code', $campaign->tracking_code) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tracking_code') border-red-500 @enderror"
                               placeholder="Unique tracking identifier">
                        @error('tracking_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Unique identifier for tracking campaign performance</p>
                    </div>
                </div>
            </div>

            <!-- Budget & Dates -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Budget & Schedule</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">Total Budget</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="budget" id="budget" value="{{ old('budget', $campaign->budget) }}" step="0.01" min="0"
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('budget') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="daily_budget" class="block text-sm font-medium text-gray-700 mb-2">Daily Budget</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="daily_budget" id="daily_budget" value="{{ old('daily_budget', $campaign->daily_budget) }}" step="0.01" min="0"
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('daily_budget') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('daily_budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3>
                <div class="space-y-6">
                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                        <input type="text" name="target_audience" id="target_audience" value="{{ old('target_audience', $campaign->target_audience) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('target_audience') border-red-500 @enderror"
                               placeholder="e.g., Small business owners, 25-45, interested in marketing">
                        @error('target_audience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Campaign objectives, key messages, and other relevant details...">{{ old('description', $campaign->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Campaign Info -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Created:</span> {{ $campaign->created_at->format('M j, Y g:i A') }}
                    </div>
                    <div>
                        <span class="font-medium">Last Updated:</span> {{ $campaign->updated_at->format('M j, Y g:i A') }}
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <div class="flex space-x-4">
                    <a href="{{ route('campaigns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Campaign
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-red-900 mb-3">Danger Zone</h3>
        <p class="text-sm text-red-700 mb-4">Once you delete a campaign, there is no going back. Please be certain.</p>
        <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}" class="inline" 
              onsubmit="return confirm('Are you sure you want to delete this campaign? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-trash mr-2"></i>Delete Campaign
            </button>
        </form>
    </div>
</div>
@endsection
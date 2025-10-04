@extends('layouts.app')

@section('title', 'Add New Campaign')
@section('page-title', 'Add New Campaign')
@section('page-description', 'Create a new marketing campaign to track leads and performance')

@section('header-actions')
<a href="{{ route('campaigns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
    <i class="fas fa-arrow-left mr-2"></i>Back to Campaigns
</a>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('campaigns.store') }}" class="p-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Campaign Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Enter campaign name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Platform *</label>
                        <input type="text" name="platform" id="platform" value="{{ old('platform') }}" required
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
                            <option value="search" {{ old('type') === 'search' ? 'selected' : '' }}>Search</option>
                            <option value="display" {{ old('type') === 'display' ? 'selected' : '' }}>Display</option>
                            <option value="social" {{ old('type') === 'social' ? 'selected' : '' }}>Social Media</option>
                            <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
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
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tracking_code" class="block text-sm font-medium text-gray-700 mb-2">Tracking Code</label>
                        <input type="text" name="tracking_code" id="tracking_code" value="{{ old('tracking_code') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tracking_code') border-red-500 @enderror"
                               placeholder="Leave blank to auto-generate">
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
                            <input type="number" name="budget" id="budget" value="{{ old('budget') }}" step="0.01" min="0"
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
                            <input type="number" name="daily_budget" id="daily_budget" value="{{ old('daily_budget') }}" step="0.01" min="0"
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('daily_budget') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('daily_budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
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
                        <input type="text" name="target_audience" id="target_audience" value="{{ old('target_audience') }}"
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
                                  placeholder="Campaign objectives, key messages, and other relevant details...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('campaigns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Campaign
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-3">Campaign Creation Tips</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
            <div>
                <h4 class="font-medium mb-2">Campaign Types:</h4>
                <ul class="space-y-1">
                    <li><strong>Search:</strong> Google Ads, Bing Ads search campaigns</li>
                    <li><strong>Display:</strong> Banner ads, remarketing campaigns</li>
                    <li><strong>Social:</strong> Facebook, LinkedIn, Twitter campaigns</li>
                    <li><strong>Email:</strong> Email marketing campaigns</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2">Best Practices:</h4>
                <ul class="space-y-1">
                    <li>• Use descriptive campaign names</li>
                    <li>• Set realistic budgets based on goals</li>
                    <li>• Define clear target audiences</li>
                    <li>• Track performance with unique codes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
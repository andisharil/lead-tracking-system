@extends('layouts.app')

@section('title', 'Create New Source')

@section('page-title', 'Create New Source')

@section('page-description', 'Add a new lead source to track performance')

@section('header-actions')
    <a href="{{ route('sources.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Back to Sources
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('sources.store') }}" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Source Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., Google Ads, Facebook, Website">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Source Type *</label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                            <option value="">Select a type</option>
                            <option value="website" {{ old('type') === 'website' ? 'selected' : '' }}>Website</option>
                            <option value="social_media" {{ old('type') === 'social_media' ? 'selected' : '' }}>Social Media</option>
                            <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email Marketing</option>
                            <option value="referral" {{ old('type') === 'referral' ? 'selected' : '' }}>Referral</option>
                            <option value="advertising" {{ old('type') === 'advertising' ? 'selected' : '' }}>Paid Advertising</option>
                            <option value="direct" {{ old('type') === 'direct' ? 'selected' : '' }}>Direct</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tracking_code" class="block text-sm font-medium text-gray-700 mb-2">Tracking Code</label>
                        <input type="text" id="tracking_code" name="tracking_code" value="{{ old('tracking_code') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tracking_code') border-red-500 @enderror"
                               placeholder="UTM code or tracking identifier">
                        @error('tracking_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Describe this lead source...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Cost & Budget Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Cost & Budget Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cost_per_lead" class="block text-sm font-medium text-gray-700 mb-2">Cost Per Lead ($)</label>
                        <input type="number" id="cost_per_lead" name="cost_per_lead" value="{{ old('cost_per_lead') }}" 
                               step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cost_per_lead') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('cost_per_lead')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Average cost to acquire one lead from this source</p>
                    </div>

                    <div>
                        <label for="monthly_budget" class="block text-sm font-medium text-gray-700 mb-2">Monthly Budget ($)</label>
                        <input type="number" id="monthly_budget" name="monthly_budget" value="{{ old('monthly_budget') }}" 
                               step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('monthly_budget') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('monthly_budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Monthly budget allocated for this source</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('sources.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Source
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-900 mb-2">Tips for Creating Sources</h4>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Use descriptive names that clearly identify the source (e.g., "Google Ads - Search Campaign")</li>
            <li>• Choose the appropriate type to help with reporting and analytics</li>
            <li>• Add tracking codes to monitor campaign performance</li>
            <li>• Set realistic cost per lead and budget estimates for ROI tracking</li>
            <li>• Keep descriptions concise but informative for team members</li>
        </ul>
    </div>
</div>
@endsection
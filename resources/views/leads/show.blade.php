@extends('layouts.app')

@section('title', 'Lead Details - ' . $lead->name)
@section('page-title', 'Lead Details')
@section('page-description', 'View detailed information about ' . $lead->name)

@section('header-actions')
    <div class="flex space-x-4">
        <a href="{{ route('leads.edit', $lead) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            Edit Lead
        </a>
        <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline" 
              onsubmit="return confirm('Are you sure you want to delete this lead?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Delete Lead
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Lead Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $lead->name }}</h2>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                @if($lead->status === 'successful') bg-green-100 text-green-800
                                @elseif($lead->status === 'lost') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                                        <p class="text-gray-900">{{ $lead->phone }}</p>
                                    </div>
                                    @if($lead->email)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Email</label>
                                            <p class="text-gray-900">{{ $lead->email }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Lead Source & Location -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Source</label>
                                        <p class="text-gray-900">{{ $lead->source->name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500">Location</label>
                                        <p class="text-gray-900">{{ $lead->location->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        @if($lead->notes)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $lead->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Lead Data (if available) -->
                        @if($lead->lead_data)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Data</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode(json_decode($lead->lead_data), JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Timeline -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mt-1"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Lead Created</p>
                                    <p class="text-sm text-gray-500">{{ $lead->created_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                            </div>

                            @if($lead->updated_at != $lead->created_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full mt-1"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                        <p class="text-sm text-gray-500">{{ $lead->updated_at->format('M d, Y \a\t H:i') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($lead->closed_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-3 h-3 {{ $lead->status === 'successful' ? 'bg-green-500' : 'bg-red-500' }} rounded-full mt-1"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Lead Closed</p>
                                        <p class="text-sm text-gray-500">{{ $lead->closed_at->format('M d, Y \a\t H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Lead ID</span>
                                <span class="text-sm font-medium text-gray-900">#{{ $lead->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Days Since Created</span>
                                <span class="text-sm font-medium text-gray-900">{{ $lead->created_at->diffInDays(now()) }} days</span>
                            </div>
                            @if($lead->closed_at)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Time to Close</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $lead->created_at->diffInDays($lead->closed_at) }} days</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Lead - {{ $lead->name }} - Lead Tracking CRM')
@section('page-title', 'Edit Lead')
@section('page-description', 'Update lead information')

@section('header-actions')
<div class="flex space-x-4">
    <a href="{{ route('leads.show', $lead) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
        View Lead
    </a>
</div>
@endsection

@section('content')
        <div class="max-w-4xl mx-auto">
            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('leads.update', $lead) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $lead->name) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                       placeholder="Enter full name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $lead->phone) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $lead->email) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                       placeholder="Enter email address">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select id="status" name="status" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                    <option value="">Select status</option>
                                    <option value="new" {{ old('status', $lead->status) == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="successful" {{ old('status', $lead->status) == 'successful' ? 'selected' : '' }}>Successful</option>
                                    <option value="lost" {{ old('status', $lead->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Lead Source & Location -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Source & Location</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Source -->
                            <div>
                                <label for="source_id" class="block text-sm font-medium text-gray-700 mb-2">Lead Source *</label>
                                <select id="source_id" name="source_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('source_id') border-red-500 @enderror">
                                    <option value="">Select source</option>
                                    @foreach($sources as $source)
                                        <option value="{{ $source->id }}" {{ old('source_id', $lead->source_id) == $source->id ? 'selected' : '' }}>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                <select id="location_id" name="location_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location_id') border-red-500 @enderror">
                                    <option value="">Select location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id', $lead->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                        <div class="space-y-6">
                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea id="notes" name="notes" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                                          placeholder="Add any additional notes about this lead...">{{ old('notes', $lead->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lead Data -->
                            <div>
                                <label for="lead_data" class="block text-sm font-medium text-gray-700 mb-2">Additional Data (JSON)</label>
                                <textarea id="lead_data" name="lead_data" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm @error('lead_data') border-red-500 @enderror"
                                          placeholder='Optional JSON data, e.g., {"campaign": "summer2024", "utm_source": "google"}'>{{ old('lead_data', $lead->lead_data) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Optional: Enter valid JSON data for additional lead information</p>
                                @error('lead_data')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead History</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <p class="text-gray-600">{{ $lead->created_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Last Updated:</span>
                                    <p class="text-gray-600">{{ $lead->updated_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                                @if($lead->closed_at)
                                    <div>
                                        <span class="font-medium text-gray-700">Closed:</span>
                                        <p class="text-gray-600">{{ $lead->closed_at->format('M d, Y \a\t H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('leads.show', $lead) }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                            Update Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
@endsection

@section('scripts')
<script>
    // JSON validation for lead_data field
    document.getElementById('lead_data').addEventListener('blur', function() {
        const value = this.value.trim();
        if (value && value !== '') {
            try {
                JSON.parse(value);
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } catch (e) {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-500');
            }
        } else {
            this.classList.remove('border-red-500', 'border-green-500');
        }
    });
</script>
@endsection
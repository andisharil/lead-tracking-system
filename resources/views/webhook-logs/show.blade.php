@extends('layouts.app')

@section('title', 'Webhook Log Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Webhook Log Details</h1>
            <p class="text-gray-600 mt-2">Inspect webhook payload, headers, and response</p>
        </div>
        <div class="flex space-x-3">
            @if($log->status === 'failed')
                <button onclick="retryWebhook({{ $log->id }})" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Retry Webhook
                </button>
            @endif
            <a href="{{ route('webhook-logs.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Logs
            </a>
        </div>
    </div>

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
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            @if($log->status === 'success')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Success
                                </span>
                            @elseif($log->status === 'failed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Failed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pending
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                            <p class="text-sm text-gray-900">{{ $log->source }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ strtoupper($log->method) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Code</label>
                            <p class="text-sm text-gray-900">{{ $log->status_code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Processing Time</label>
                            <p class="text-sm text-gray-900">{{ $log->processing_time_ms }}ms</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                            <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Endpoint</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md font-mono break-all">{{ $log->endpoint }}</p>
                    </div>
                    
                    @if($log->error_message)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Error Message</label>
                            <p class="text-sm text-red-600 bg-red-50 p-3 rounded-md">{{ $log->error_message }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Request Headers -->
            @if($log->headers)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Request Headers</h3>
                    </div>
                    <div class="p-6">
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto"><code>{{ json_encode($log->headers, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            @endif

            <!-- Request Payload -->
            @if($log->payload)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Request Payload</h3>
                    </div>
                    <div class="p-6">
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto"><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            @endif

            <!-- Response -->
            @if($log->response)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Response</h3>
                    </div>
                    <div class="p-6">
                        <pre class="bg-gray-50 p-4 rounded-md text-sm overflow-x-auto"><code>{{ json_encode($log->response, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($log->status === 'failed')
                        <button onclick="retryWebhook({{ $log->id }})" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Retry Webhook
                        </button>
                    @endif
                    <button onclick="copyToClipboard('payload')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Payload
                    </button>
                    <button onclick="copyToClipboard('curl')" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy as cURL
                    </button>
                </div>
            </div>

            <!-- Retry History -->
            @if($retryHistory->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Retry History</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($retryHistory as $retry)
                                <div class="border-l-4 {{ $retry->status === 'success' ? 'border-green-400 bg-green-50' : 'border-red-400 bg-red-50' }} p-4 rounded">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium {{ $retry->status === 'success' ? 'text-green-800' : 'text-red-800' }}">
                                            {{ ucfirst($retry->status) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($retry->attempted_at)->format('M d, H:i') }}
                                        </span>
                                    </div>
                                    @if($retry->status_code)
                                        <p class="text-xs text-gray-600 mb-1">Status Code: {{ $retry->status_code }}</p>
                                    @endif
                                    @if($retry->response)
                                        <p class="text-xs text-gray-600 truncate">{{ $retry->response }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Related Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook ID</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $log->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User Agent</label>
                        <p class="text-sm text-gray-900 break-all">{{ $log->headers['User-Agent'] ?? 'N/A' }}</p>
                    </div>
                    @if(isset($log->headers['X-Forwarded-For']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                            <p class="text-sm text-gray-900">{{ $log->headers['X-Forwarded-For'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Retry webhook function
function retryWebhook(id) {
    if (!confirm('Are you sure you want to retry this webhook?')) {
        return;
    }

    fetch(`/webhook-logs/${id}/retry`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Webhook retried successfully!');
            location.reload();
        } else {
            alert('Retry failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while retrying the webhook.');
    });
}

// Copy to clipboard function
function copyToClipboard(type) {
    let content = '';
    
    if (type === 'payload') {
        content = @json($log->payload);
        content = JSON.stringify(content, null, 2);
    } else if (type === 'curl') {
        const headers = @json($log->headers);
        const payload = @json($log->payload);
        
        content = `curl -X {{ strtoupper($log->method) }} '{{ $log->endpoint }}'`;
        
        if (headers) {
            Object.keys(headers).forEach(key => {
                content += ` \\
  -H '${key}: ${headers[key]}'`;
            });
        }
        
        if (payload && Object.keys(payload).length > 0) {
            content += ` \\
  -d '${JSON.stringify(payload)}'`;
        }
    }
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(content).then(() => {
            alert('Copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(content);
        });
    } else {
        fallbackCopyTextToClipboard(content);
    }
}

// Fallback copy function for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            alert('Copied to clipboard!');
        } else {
            alert('Failed to copy to clipboard.');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        alert('Failed to copy to clipboard.');
    }
    
    document.body.removeChild(textArea);
}
</script>
@endsection
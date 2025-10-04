@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">API Documentation</h1>
            <p class="text-gray-600">Complete guide to integrating with the Lead Tracking System API</p>
        </div>

        <!-- Quick Navigation -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">Quick Navigation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#authentication" class="text-blue-600 hover:text-blue-800 font-medium">Authentication</a>
                <a href="#endpoints" class="text-blue-600 hover:text-blue-800 font-medium">API Endpoints</a>
                <a href="#examples" class="text-blue-600 hover:text-blue-800 font-medium">Code Examples</a>
                <a href="#monitoring" class="text-blue-600 hover:text-blue-800 font-medium">Monitoring</a>
            </div>
        </div>

        <!-- Authentication Section -->
        <section id="authentication" class="mb-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Authentication</h2>
                <p class="text-gray-600 mb-6">All API requests require authentication using a webhook token. You can authenticate using either a header or query parameter.</p>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Header Authentication (Recommended)</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <code class="text-sm text-gray-800">X-Webhook-Token: your_webhook_token_here</code>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Query Parameter Authentication</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <code class="text-sm text-gray-800">?token=your_webhook_token_here</code>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Security Note</h4>
                            <p class="text-sm text-yellow-700 mt-1">Keep your webhook token secure. Configure it in your .env file as WEBHOOK_TOKEN.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- API Endpoints Section -->
        <section id="endpoints" class="mb-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">API Endpoints</h2>

                <!-- Create New Lead Endpoint -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <div class="flex items-center mb-4">
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded mr-3">POST</span>
                        <h3 class="text-xl font-semibold text-gray-900">/api/leads/new</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Create a new lead in the system.</p>

                    <h4 class="text-lg font-medium text-gray-800 mb-3">Request Body</h4>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <pre class="text-sm text-gray-800"><code>{
  "name": "John Doe",
  "phone": "+1234567890",
  "location": "New York",
  "source": "Facebook Ads"
}</code></pre>
                    </div>

                    <h4 class="text-lg font-medium text-gray-800 mb-3">Required Fields</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">name</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Full name of the lead</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">phone</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Phone number (used for deduplication)</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">location</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Must match an existing location in the system</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">source</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">Must match an existing source in the system</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="text-lg font-medium text-gray-800 mb-3 mt-6">Response Examples</h4>
                    <div class="space-y-4">
                        <div>
                            <h5 class="text-sm font-medium text-green-700 mb-2">Success (201 Created)</h5>
                            <div class="bg-green-50 rounded-lg p-4">
                                <pre class="text-sm text-green-800"><code>{
  "success": true,
  "message": "Lead created successfully",
  "lead_id": 123
}</code></pre>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-yellow-700 mb-2">Duplicate Lead (200 OK)</h5>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <pre class="text-sm text-yellow-800"><code>{
  "success": true,
  "message": "Duplicate lead detected (same phone within 30 minutes)",
  "lead_id": 122
}</code></pre>
                            </div>
                        </div>
                        <div>
                            <h5 class="text-sm font-medium text-red-700 mb-2">Validation Error (422 Unprocessable Entity)</h5>
                            <div class="bg-red-50 rounded-lg p-4">
                                <pre class="text-sm text-red-800"><code>{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."]
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Lead Status Endpoint -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded mr-3">PUT</span>
                        <h3 class="text-xl font-semibold text-gray-900">/api/leads/status</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Update the status of an existing lead.</p>

                    <h4 class="text-lg font-medium text-gray-800 mb-3">Request Body</h4>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <pre class="text-sm text-gray-800"><code>{
  "lead_id": 123,
  "status": "successful"
}</code></pre>
                    </div>

                    <h4 class="text-lg font-medium text-gray-800 mb-3">Valid Status Values</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">new</span>
                            <p class="text-sm text-gray-600 mt-2">Default status for new leads</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">successful</span>
                            <p class="text-sm text-gray-600 mt-2">Lead converted successfully</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">lost</span>
                            <p class="text-sm text-gray-600 mt-2">Lead was not converted</p>
                        </div>
                    </div>

                    <h4 class="text-lg font-medium text-gray-800 mb-3">Response Example</h4>
                    <div class="bg-green-50 rounded-lg p-4">
                        <pre class="text-sm text-green-800"><code>{
  "success": true,
  "message": "Lead status updated successfully"
}</code></pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- Code Examples Section -->
        <section id="examples" class="mb-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Integration Examples</h2>

                <!-- cURL Example -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">cURL</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-green-400"><code># Create a new lead
curl -X POST http://localhost:8000/api/leads/new \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Token: your_webhook_token_here" \
  -d '{
    "name": "John Doe",
    "phone": "+1234567890",
    "location": "New York",
    "source": "Facebook Ads"
  }'

# Update lead status
curl -X PUT http://localhost:8000/api/leads/status \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Token: your_webhook_token_here" \
  -d '{
    "lead_id": 123,
    "status": "successful"
  }'</code></pre>
                    </div>
                </div>

                <!-- JavaScript Example -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">JavaScript (Node.js)</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-blue-400"><code>const axios = require('axios');

// Create a new lead
const createLead = async (leadData) => {
  try {
    const response = await axios.post('{{ url('/api/leads/new') }}', leadData, {
      headers: {
        'Content-Type': 'application/json',
        'X-Webhook-Token': 'your_webhook_token_here'
      }
    });
    // Lead created successfully
  } catch (error) {
    // Handle error appropriately
  }
};

// Usage
createLead({
  name: 'John Doe',
  phone: '+1234567890',
  location: 'New York',
  source: 'Facebook Ads'
});</code></pre>
                    </div>
                </div>

                <!-- PHP Example -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">PHP</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-purple-400"><code>&lt;?php

$leadData = [
    'name' => 'John Doe',
    'phone' => '+1234567890',
    'location' => 'New York',
    'source' => 'Facebook Ads'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/leads/new');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($leadData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Webhook-Token: your_webhook_token_here'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    echo "Lead created successfully: " . $response;
} else {
    echo "Error creating lead: " . $response;
}

?></code></pre>
                    </div>
                </div>

                <!-- Pabbly Connect Configuration -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Pabbly Connect Configuration</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h4 class="font-medium text-blue-900 mb-3">Webhook Action Settings</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-blue-800">URL:</label>
                                <code class="ml-2 text-sm bg-white px-2 py-1 rounded border">http://localhost:8000/api/leads/new</code>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-blue-800">Method:</label>
                                <code class="ml-2 text-sm bg-white px-2 py-1 rounded border">POST</code>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-blue-800">Headers:</label>
                                <div class="ml-2 mt-1">
                                    <code class="text-sm bg-white px-2 py-1 rounded border block">Content-Type: application/json</code>
                                    <code class="text-sm bg-white px-2 py-1 rounded border block mt-1">X-Webhook-Token: your_webhook_token_here</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Monitoring Section -->
        <section id="monitoring" class="mb-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Monitoring & Debugging</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Webhook Logs</h3>
                        <p class="text-gray-600 mb-4">Monitor all webhook requests and responses in real-time.</p>
                        <a href="{{ route('webhook-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Webhook Logs
                        </a>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Error Handling</h3>
                        <p class="text-gray-600 mb-4">All webhook requests are logged with detailed error information for debugging.</p>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Success responses (200, 201)
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                Validation errors (422)
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Authentication & server errors (401, 500)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-800 mb-2">Features</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Automatic deduplication (same phone within 30 minutes)</li>
                        <li>• Comprehensive request/response logging</li>
                        <li>• Real-time webhook monitoring dashboard</li>
                        <li>• Detailed error messages and stack traces</li>
                        <li>• Retry functionality for failed webhooks</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Rate Limiting & Best Practices -->
        <section class="mb-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Best Practices</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Security</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                Always use HTTPS in production
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                Keep your webhook token secure and rotate it regularly
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                Use header authentication instead of query parameters
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Performance</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                Implement proper error handling and retries
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                Monitor webhook logs for performance issues
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                Validate data before sending to reduce errors
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
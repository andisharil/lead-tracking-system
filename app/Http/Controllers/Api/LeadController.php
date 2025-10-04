<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Location;
use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeadController extends Controller
{
    /**
     * Create a new lead from webhook (Pabbly Connect)
     * POST /api/leads/new
     */
    public function store(Request $request): JsonResponse
    {
        $start = microtime(true);
        $headers = $request->headers->all();
        $payload = $request->all();
        $endpoint = $request->path();
        $method = $request->method();

        // Create inbound webhook log (pending)
        $logId = DB::table('webhook_logs')->insertGetId([
            'source' => 'lead_webhook',
            'endpoint' => $endpoint,
            'method' => $method,
            'status' => 'pending',
            'status_code' => null,
            'processing_time_ms' => null,
            'error_message' => null,
            'payload' => json_encode($payload),
            'headers' => json_encode($headers),
            'response' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Webhook authentication
        $expectedToken = env('WEBHOOK_TOKEN');
        if ($expectedToken && $request->header('X-Webhook-Token') !== $expectedToken && $request->query('token') !== $expectedToken) {
            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'status_code' => 401,
                'error_message' => 'Unauthorized webhook request',
                'processing_time_ms' => $duration,
                'response' => json_encode(['error' => 'Unauthorized']),
                'updated_at' => now(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'location' => 'required|string',
                'source' => 'required|string'
            ]);

            // Deduplicate: same phone within last 30 minutes
            $recentDuplicate = Lead::where('phone', $validated['phone'])
                ->where('created_at', '>=', now()->subMinutes(30))
                ->first();
            if ($recentDuplicate) {
                $duration = (int) round((microtime(true) - $start) * 1000);
                DB::table('webhook_logs')->where('id', $logId)->update([
                    'status' => 'success',
                    'status_code' => 200,
                    'processing_time_ms' => $duration,
                    'response' => json_encode([
                        'message' => 'Duplicate detected, existing lead used',
                        'lead_id' => $recentDuplicate->id,
                    ]),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Duplicate detected, existing lead used',
                    'lead_id' => $recentDuplicate->id
                ], 200);
            }

            // Find or validate location
            $location = Location::where('name', $validated['location'])->first();
            if (!$location) {
                $duration = (int) round((microtime(true) - $start) * 1000);
                DB::table('webhook_logs')->where('id', $logId)->update([
                    'status' => 'failed',
                    'status_code' => 400,
                    'processing_time_ms' => $duration,
                    'error_message' => 'Location not found: ' . $validated['location'],
                    'response' => json_encode(['error' => 'Invalid location provided']),
                    'updated_at' => now(),
                ]);
                return response()->json([
                    'error' => 'Invalid location provided',
                    'message' => 'Location not found: ' . $validated['location']
                ], 400);
            }

            // Find or validate source
            $source = Source::where('name', $validated['source'])->first();
            if (!$source) {
                $duration = (int) round((microtime(true) - $start) * 1000);
                DB::table('webhook_logs')->where('id', $logId)->update([
                    'status' => 'failed',
                    'status_code' => 400,
                    'processing_time_ms' => $duration,
                    'error_message' => 'Source not found: ' . $validated['source'],
                    'response' => json_encode(['error' => 'Invalid source provided']),
                    'updated_at' => now(),
                ]);
                return response()->json([
                    'error' => 'Invalid source provided',
                    'message' => 'Source not found: ' . $validated['source']
                ], 400);
            }

            // Create the lead
            $lead = Lead::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'location_id' => $location->id,
                'source_id' => $source->id,
                'status' => 'new'
            ]);

            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'success',
                'status_code' => 201,
                'processing_time_ms' => $duration,
                'response' => json_encode([
                    'message' => 'Lead created successfully',
                    'lead_id' => $lead->id,
                ]),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'lead_id' => $lead->id
            ], 201);

        } catch (ValidationException $e) {
            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'status_code' => 422,
                'processing_time_ms' => $duration,
                'error_message' => json_encode($e->errors()),
                'response' => json_encode([
                    'error' => 'Validation failed',
                    'messages' => $e->errors(),
                ]),
                'updated_at' => now(),
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lead webhook processing failed: ' . $e->getMessage());
            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'status_code' => 500,
                'processing_time_ms' => $duration,
                'error_message' => $e->getMessage(),
                'response' => json_encode([
                    'error' => 'Internal server error',
                ]),
                'updated_at' => now(),
            ]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update lead status from webhook (Monday.com)
     * POST /api/leads/status
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $start = microtime(true);
        $headers = $request->headers->all();
        $payload = $request->all();
        $endpoint = $request->path();
        $method = $request->method();

        // Create inbound webhook log (pending)
        $logId = DB::table('webhook_logs')->insertGetId([
            'source' => 'lead_webhook',
            'endpoint' => $endpoint,
            'method' => $method,
            'status' => 'pending',
            'status_code' => null,
            'processing_time_ms' => null,
            'error_message' => null,
            'payload' => json_encode($payload),
            'headers' => json_encode($headers),
            'response' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Webhook authentication
        $expectedToken = env('WEBHOOK_TOKEN');
        if ($expectedToken && $request->header('X-Webhook-Token') !== $expectedToken && $request->query('token') !== $expectedToken) {
            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'status_code' => 401,
                'error_message' => 'Unauthorized webhook request',
                'processing_time_ms' => $duration,
                'response' => json_encode(['error' => 'Unauthorized']),
                'updated_at' => now(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'lead_id' => 'required|integer|exists:leads,id',
                'status' => 'required|string|in:new,successful,lost'
            ]);

            $lead = Lead::findOrFail($validated['lead_id']);
            $lead->status = $validated['status'];
            if (in_array($validated['status'], ['successful', 'lost'])) {
                $lead->closed_at = now();
            } else {
                $lead->closed_at = null;
            }
            $lead->save();

            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'success',
                'status_code' => 200,
                'processing_time_ms' => $duration,
                'response' => json_encode([
                    'message' => 'Lead status updated successfully',
                    'lead' => [
                        'id' => $lead->id,
                        'status' => $lead->status,
                        'closed_at' => $lead->closed_at,
                    ]
                ]),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully',
                'lead' => [
                    'id' => $lead->id,
                    'status' => $lead->status,
                    'closed_at' => $lead->closed_at
                ]
            ]);

        } catch (ValidationException $e) {
            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'status_code' => 422,
                'processing_time_ms' => $duration,
                'error_message' => json_encode($e->errors()),
                'response' => json_encode([
                    'error' => 'Validation failed',
                    'messages' => $e->errors(),
                ]),
                'updated_at' => now(),
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lead status webhook failed: ' . $e->getMessage());
            $duration = (int) round((microtime(true) - $start) * 1000);
            DB::table('webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'status_code' => 500,
                'processing_time_ms' => $duration,
                'error_message' => $e->getMessage(),
                'response' => json_encode([
                    'error' => 'Internal server error',
                ]),
                'updated_at' => now(),
            ]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

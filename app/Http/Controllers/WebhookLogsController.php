<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;

class WebhookLogsController extends Controller
{
    /**
     * Display webhook logs dashboard
     */
    public function index(Request $request)
    {
        $query = DB::table('webhook_logs')
            ->select('*')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('endpoint', 'like', '%' . $search . '%')
                  ->orWhere('source', 'like', '%' . $search . '%')
                  ->orWhere('error_message', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(20);

        // Get statistics
        $stats = $this->getWebhookStats();

        // Get recent activity
        $recentActivity = DB::table('webhook_logs')
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('webhook-logs.index', compact('logs', 'stats', 'recentActivity'));
    }

    /**
     * Show webhook log details
     */
    public function show($id)
    {
        $log = DB::table('webhook_logs')->where('id', $id)->first();

        if (!$log) {
            return redirect()->route('webhook-logs.index')
                ->with('error', 'Webhook log not found.');
        }

        // Decode JSON payload
        $log->payload = json_decode($log->payload, true);
        $log->headers = json_decode($log->headers, true);
        $log->response = json_decode($log->response, true);

        // Get retry history
        $retryHistory = DB::table('webhook_retries')
            ->where('webhook_log_id', $id)
            ->orderBy('attempted_at', 'desc')
            ->get();

        return view('webhook-logs.show', compact('log', 'retryHistory'));
    }

    /**
     * Retry failed webhook
     */
    public function retry($id)
    {
        try {
            $log = DB::table('webhook_logs')->where('id', $id)->first();

            if (!$log) {
                return response()->json(['error' => 'Webhook log not found'], 404);
            }

            if ($log->status === 'success') {
                return response()->json(['error' => 'Cannot retry successful webhook'], 400);
            }

            // Attempt to retry the webhook
            $payload = json_decode($log->payload, true);
            $headers = json_decode($log->headers, true) ?: [];

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($log->endpoint, $payload);

            $success = $response->successful();
            $statusCode = $response->status();
            $responseBody = $response->body();

            // Log retry attempt
            DB::table('webhook_retries')->insert([
                'webhook_log_id' => $id,
                'status' => $success ? 'success' : 'failed',
                'status_code' => $statusCode,
                'response' => $responseBody,
                'attempted_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update original log if successful
            if ($success) {
                DB::table('webhook_logs')
                    ->where('id', $id)
                    ->update([
                        'status' => 'success',
                        'status_code' => $statusCode,
                        'response' => json_encode([
                            'body' => $responseBody,
                            'status' => $statusCode,
                            'retried_at' => now()->toISOString()
                        ]),
                        'updated_at' => now()
                    ]);
            }

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Webhook retried successfully' : 'Webhook retry failed',
                'status_code' => $statusCode,
                'response' => $responseBody
            ]);

        } catch (Exception $e) {
            Log::error('Webhook retry failed: ' . $e->getMessage());

            // Log failed retry attempt
            DB::table('webhook_retries')->insert([
                'webhook_log_id' => $id,
                'status' => 'failed',
                'status_code' => 0,
                'response' => $e->getMessage(),
                'attempted_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook retry failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk retry failed webhooks
     */
    public function bulkRetry(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:webhook_logs,id'
        ]);

        $results = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($request->ids as $id) {
            try {
                $response = $this->retry($id);
                $data = json_decode($response->getContent(), true);
                
                if ($data['success']) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
                
                $results[] = [
                    'id' => $id,
                    'success' => $data['success'],
                    'message' => $data['message']
                ];
            } catch (Exception $e) {
                $failedCount++;
                $results[] = [
                    'id' => $id,
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk retry completed: {$successCount} successful, {$failedCount} failed",
            'results' => $results,
            'summary' => [
                'total' => count($request->ids),
                'successful' => $successCount,
                'failed' => $failedCount
            ]
        ]);
    }

    /**
     * Clear old webhook logs
     */
    public function clearOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = Carbon::now()->subDays($request->days);
        
        $deletedCount = DB::table('webhook_logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        // Also clear related retry records
        DB::table('webhook_retries')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('webhook_logs')
                    ->whereRaw('webhook_logs.id = webhook_retries.webhook_log_id');
            })
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deletedCount} webhook logs older than {$request->days} days",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Export webhook logs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = DB::table('webhook_logs')
            ->select('*')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        if ($format === 'json') {
            return response()->json($logs);
        }

        // CSV Export
        $filename = 'webhook_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID', 'Source', 'Endpoint', 'Method', 'Status', 'Status Code',
                'Processing Time', 'Error Message', 'Created At', 'Updated At'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->source,
                    $log->endpoint,
                    $log->method,
                    $log->status,
                    $log->status_code,
                    $log->processing_time_ms . 'ms',
                    $log->error_message,
                    $log->created_at,
                    $log->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get webhook statistics
     */
    private function getWebhookStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_webhooks' => DB::table('webhook_logs')->count(),
            'successful_webhooks' => DB::table('webhook_logs')->where('status', 'success')->count(),
            'failed_webhooks' => DB::table('webhook_logs')->where('status', 'failed')->count(),
            'pending_webhooks' => DB::table('webhook_logs')->where('status', 'pending')->count(),
            'today_webhooks' => DB::table('webhook_logs')->whereDate('created_at', $today)->count(),
            'yesterday_webhooks' => DB::table('webhook_logs')->whereDate('created_at', $yesterday)->count(),
            'week_webhooks' => DB::table('webhook_logs')->where('created_at', '>=', $thisWeek)->count(),
            'month_webhooks' => DB::table('webhook_logs')->where('created_at', '>=', $thisMonth)->count(),
            'avg_processing_time' => DB::table('webhook_logs')
                ->where('status', 'success')
                ->avg('processing_time_ms'),
            'success_rate' => $this->calculateSuccessRate(),
            'top_sources' => DB::table('webhook_logs')
                ->select('source', DB::raw('count(*) as count'))
                ->groupBy('source')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'recent_errors' => DB::table('webhook_logs')
                ->where('status', 'failed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'source', 'error_message', 'created_at'])
        ];
    }

    /**
     * Calculate success rate percentage
     */
    private function calculateSuccessRate()
    {
        $total = DB::table('webhook_logs')->count();
        
        if ($total === 0) {
            return 0;
        }

        $successful = DB::table('webhook_logs')->where('status', 'success')->count();
        
        return round(($successful / $total) * 100, 2);
    }
}
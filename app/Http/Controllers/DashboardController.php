<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Location;
use App\Models\Source;
use App\Models\AdSpend;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Default safe values if database is unavailable
        $totalLeads = 0;
        $successfulLeads = 0;
        $conversionRate = 0;
        $totalSpend = 0;
        $costPerLead = 0;
        $costPerConversion = 0;
        $leadsBySource = collect();
        $leadsByLocation = collect();
        $dailyLeads = collect();
        $sources = collect();
        $locations = collect();
        $recentLeads = collect();

        try {
            // Ensure database connection is available
            DB::connection()->getPdo();

            // Get metrics for current month
            $totalLeads = Lead::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $successfulLeads = Lead::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                  ->where('status', 'successful')->count();
            $conversionRate = $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0;
            
            // Get ad spend for current month
            $totalSpend = AdSpend::where('month', $currentMonth)->sum('amount_spent');
            $costPerLead = $totalLeads > 0 ? round($totalSpend / $totalLeads, 2) : 0;
            $costPerConversion = $successfulLeads > 0 ? round($totalSpend / $successfulLeads, 2) : 0;
            
            // Get chart data
            $leadsBySource = Lead::whereBetween('leads.created_at', [$startOfMonth, $endOfMonth])
                                ->join('sources', 'leads.source_id', '=', 'sources.id')
                                ->selectRaw('sources.name, COUNT(*) as count')
                                ->groupBy('sources.name')
                                ->get();
            
            $leadsByLocation = Lead::join('locations', 'leads.location_id', '=', 'locations.id')
                                  ->whereBetween('leads.created_at', [$startOfMonth, $endOfMonth])
                                  ->selectRaw('locations.name, COUNT(*) as count')
                                  ->groupBy('locations.name')
                                  ->get();
            
            // Daily leads trend for current month (DB-specific date casting)
            $driver = config('database.default');
            if ($driver === 'pgsql') {
                $dateExpr = 'CAST(leads.created_at AS DATE)';
            } elseif ($driver === 'sqlite') {
                $dateExpr = 'DATE(leads.created_at)';
            } else { // mysql & others
                $dateExpr = 'DATE(leads.created_at)';
            }
            $dailyLeads = Lead::whereBetween('leads.created_at', [$startOfMonth, $endOfMonth])
                             ->selectRaw($dateExpr . ' as date, COUNT(*) as count')
                             ->groupBy('date')
                             ->orderBy('date')
                             ->get();
            
            $sources = Source::all();
            $locations = Location::all();
            
            // Get recent leads for the table
            $recentLeads = Lead::with(['location', 'source'])
                              ->orderBy('created_at', 'desc')
                              ->limit(20)
                              ->get();
        } catch (\Throwable $e) {
            // Log and continue with safe defaults to avoid 500 error on serverless when DB is not reachable
            Log::warning('DashboardController index fallback due to database issue: ' . $e->getMessage());
        }
        
        return view('dashboard', compact(
            'totalLeads', 'successfulLeads', 'conversionRate', 'costPerLead', 'costPerConversion',
            'leadsBySource', 'leadsByLocation', 'dailyLeads', 'sources', 'locations', 'currentMonth', 'recentLeads'
        ));
    }

    public function exportCsv()
    {
        // Attempt to fetch leads; fallback to empty collection if DB is unavailable
        $leads = collect();
        try {
            DB::connection()->getPdo();
            $leads = Lead::with(['location', 'source'])->get();
        } catch (\Throwable $e) {
            Log::warning('DashboardController exportCsv fallback due to database issue: ' . $e->getMessage());
        }
        
        $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Phone', 'Location', 'Source', 'Status', 'Created Date', 'Closed Date']);
            
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->name,
                    $lead->phone,
                    optional($lead->location)->name,
                    optional($lead->source)->name,
                    $lead->status,
                    optional($lead->created_at)->format('Y-m-d H:i:s'),
                    optional($lead->closed_at)->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function storeAdSpend(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'amount_spent' => 'required|numeric|min:0',
        ]);

        try {
            DB::connection()->getPdo();
            AdSpend::updateOrCreate(
                ['month' => $request->month],
                ['amount_spent' => $request->amount_spent]
            );
        } catch (\Throwable $e) {
            Log::warning('DashboardController storeAdSpend failed due to database issue: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'We could not save the ad spend right now. Please try again later.');
        }

        return redirect()->route('dashboard')->with('success', 'Ad spend updated successfully!');
    }
}

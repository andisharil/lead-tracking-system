<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Source;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        
        // Base query with date filtering
        $baseQuery = Lead::whereBetween('created_at', [$startDate, $endDate]);
        if ($sourceId) {
            $baseQuery->where('source_id', $sourceId);
        }
        
        // Overall Performance Metrics
        $totalLeads = $baseQuery->count();
        $successfulLeads = $baseQuery->where('status', 'successful')->count();
        $conversionRate = $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0;
        $totalRevenue = $baseQuery->where('status', 'successful')->sum('value') ?? 0;
        $averageDealSize = $successfulLeads > 0 ? round($totalRevenue / $successfulLeads, 2) : 0;
        
        // ROI Analysis by Source
        $sourcePerformance = Source::select(
            'sources.id',
            'sources.name',
            DB::raw('COUNT(leads.id) as total_leads'),
            DB::raw("SUM(CASE WHEN leads.status = 'successful' THEN 1 ELSE 0 END) as successful_leads"),
            DB::raw("SUM(CASE WHEN leads.status = 'successful' THEN leads.value ELSE 0 END) as revenue"),
            DB::raw("ROUND(AVG(CASE WHEN leads.status = 'successful' THEN leads.value END), 2) as avg_deal_size"),
            DB::raw("ROUND(CASE WHEN COUNT(leads.id) > 0 THEN (SUM(CASE WHEN leads.status = 'successful' THEN 1 ELSE 0 END) / COUNT(leads.id)) * 100 ELSE 0 END, 2) as conversion_rate")
        )
        ->leftJoin('leads', function($join) use ($startDate, $endDate, $sourceId) {
            $join->on('sources.id', '=', 'leads.source_id')
                 ->whereBetween('leads.created_at', [$startDate, $endDate]);
            if ($sourceId) {
                $join->where('leads.source_id', $sourceId);
            }
        })
        ->groupBy('sources.id', 'sources.name')
        ->orderBy('revenue', 'desc')
        ->get();
        
        // Monthly Performance Trend
        $monthlyTrend = Lead::select(
            DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
            DB::raw('COUNT(*) as total_leads'),
            DB::raw("SUM(CASE WHEN status = 'successful' THEN 1 ELSE 0 END) as successful_leads"),
            DB::raw("SUM(CASE WHEN status = 'successful' THEN value ELSE 0 END) as revenue"),
            DB::raw("ROUND(CASE WHEN COUNT(*) > 0 THEN (SUM(CASE WHEN status = 'successful' THEN 1 ELSE 0 END) / COUNT(*)) * 100 ELSE 0 END, 2) as conversion_rate")
        )
        ->whereBetween('created_at', [Carbon::parse($startDate)->subMonths(11), $endDate])
        ->when($sourceId, function($query) use ($sourceId) {
            return $query->where('source_id', $sourceId);
        })
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Lead Quality Score (based on conversion rate and average deal size)
        $sourceQuality = $sourcePerformance->map(function($source) {
            $qualityScore = 0;
            if ($source->total_leads > 0) {
                $conversionWeight = ($source->conversion_rate / 100) * 60; // 60% weight
                $dealSizeWeight = min(($source->avg_deal_size / 5000) * 40, 40); // 40% weight, capped at $5000
                $qualityScore = round($conversionWeight + $dealSizeWeight, 1);
            }
            $source->quality_score = $qualityScore;
            return $source;
        })->sortByDesc('quality_score');
        
        // Cost Analysis (mock data for demonstration)
        $costAnalysis = $sourcePerformance->map(function($source) {
            // Mock cost data - in real implementation, this would come from a costs table
            $mockCost = $source->total_leads * rand(10, 50); // $10-50 per lead
            $roi = $source->revenue > 0 ? round((($source->revenue - $mockCost) / $mockCost) * 100, 2) : 0;
            $costPerLead = $source->total_leads > 0 ? round($mockCost / $source->total_leads, 2) : 0;
            $costPerAcquisition = $source->successful_leads > 0 ? round($mockCost / $source->successful_leads, 2) : 0;
            
            return [
                'source_name' => $source->name,
                'total_cost' => $mockCost,
                'revenue' => $source->revenue,
                'roi' => $roi,
                'cost_per_lead' => $costPerLead,
                'cost_per_acquisition' => $costPerAcquisition,
                'profit' => $source->revenue - $mockCost
            ];
        })->sortByDesc('roi');
        
        $sources = Source::all();
        
        return view('performance.index', compact(
            'totalLeads',
            'successfulLeads', 
            'conversionRate',
            'totalRevenue',
            'averageDealSize',
            'sourcePerformance',
            'sourceQuality',
            'monthlyTrend',
            'costAnalysis',
            'sources',
            'startDate',
            'endDate',
            'sourceId'
        ));
    }
    
    public function getPerformanceData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        
        $data = Lead::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_leads'),
            DB::raw("SUM(CASE WHEN status = 'successful' THEN 1 ELSE 0 END) as successful_leads"),
            DB::raw("SUM(CASE WHEN status = 'successful' THEN value ELSE 0 END) as revenue")
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->when($sourceId, function($query) use ($sourceId) {
            return $query->where('source_id', $sourceId);
        })
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return response()->json($data);
    }
}
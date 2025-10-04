<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Source;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $sourceId = $request->get('source_id');

        try {
            // Overall metrics
            $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])
                              ->when($sourceId, fn($q) => $q->where('source_id', $sourceId))
                              ->count();
            $successfulLeads = Lead::whereBetween('created_at', [$startDate, $endDate])
                                   ->when($sourceId, fn($q) => $q->where('source_id', $sourceId))
                                   ->where('status', 'successful')
                                   ->count();
            $totalRevenue = Lead::whereBetween('created_at', [$startDate, $endDate])
                                ->when($sourceId, fn($q) => $q->where('source_id', $sourceId))
                                ->where('status', 'successful')
                                ->sum('value') ?? 0;
            $conversionRate = $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0;
            $averageDealSize = $successfulLeads > 0 ? round($totalRevenue / $successfulLeads, 2) : 0;

            // ROI analysis by source
            $sourcePerformance = Source::select('sources.id', 'sources.name')
                ->leftJoin('leads', 'sources.id', '=', 'leads.source_id')
                ->whereBetween('leads.created_at', [$startDate, $endDate])
                ->groupBy('sources.id', 'sources.name')
                ->get()
                ->map(function ($row) {
                    $totalLeads = Lead::where('source_id', $row->id)->count();
                    $successfulLeads = Lead::where('source_id', $row->id)->where('status', 'successful')->count();
                    $revenue = Lead::where('source_id', $row->id)->where('status', 'successful')->sum('value') ?? 0;
                    return [
                        'name' => $row->name,
                        'total_leads' => $totalLeads,
                        'successful_leads' => $successfulLeads,
                        'conversion_rate' => $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0,
                        'revenue' => $revenue,
                        'roi' => 0,
                        'cost_per_lead' => 0,
                    ];
                });

            // Monthly trend
            $monthlyTrend = Lead::select(
                DB::raw('TO_CHAR(created_at, \"YYYY-MM\") as month'),
                DB::raw('COUNT(*) as total_leads'),
                DB::raw('SUM(CASE WHEN status = \"successful\" THEN 1 ELSE 0 END) as successful_leads'),
                DB::raw('SUM(CASE WHEN status = \"successful\" THEN value ELSE 0 END) as revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($sourceId, fn($q) => $q->where('source_id', $sourceId))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            // Source quality scores
            $sourceQuality = Source::select('sources.id', 'sources.name')
                ->leftJoin('leads', 'sources.id', '=', 'leads.source_id')
                ->groupBy('sources.id', 'sources.name')
                ->get()
                ->map(function ($row) {
                    $totalLeads = Lead::where('source_id', $row->id)->count();
                    $successfulLeads = Lead::where('source_id', $row->id)->where('status', 'successful')->count();
                    $revenue = Lead::where('source_id', $row->id)->where('status', 'successful')->sum('value') ?? 0;
                    $avgDealSize = $successfulLeads > 0 ? round($revenue / $successfulLeads, 2) : 0;
                    $qualityScore = $totalLeads > 0 ? round((($successfulLeads / max($totalLeads, 1)) * 0.6 + ($avgDealSize / max($avgDealSize, 1)) * 0.4) * 100, 2) : 0;
                    return [
                        'name' => $row->name,
                        'total_leads' => $totalLeads,
                        'successful_leads' => $successfulLeads,
                        'conversion_rate' => $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0,
                        'revenue' => $revenue,
                        'avg_deal_size' => $avgDealSize,
                        'quality_score' => $qualityScore,
                    ];
                });

            // Cost analysis (placeholders if cost data not available)
            $costAnalysis = $sourcePerformance->map(function ($row) {
                return [
                    'source_name' => $row['name'],
                    'total_cost' => 0,
                    'revenue' => $row['revenue'],
                    'roi' => 0,
                    'cost_per_lead' => 0,
                    'cost_per_acquisition' => 0,
                    'profit' => $row['revenue']
                ];
            });

            $sources = Source::select('id', 'name')->orderBy('name')->get();

            return view('performance.index', compact(
                'startDate', 'endDate', 'sourceId', 'sources',
                'totalLeads', 'successfulLeads', 'conversionRate', 'totalRevenue', 'averageDealSize',
                'sourcePerformance', 'monthlyTrend', 'sourceQuality', 'costAnalysis'
            ));
        } catch (\Throwable $e) {
            logger()->warning('Performance index failed, showing safe defaults', ['error' => $e->getMessage()]);
            session()->flash('error', 'We are currently unable to load performance data. Please try again later.');

            $sources = collect([]);
            $totalLeads = 0;
            $successfulLeads = 0;
            $conversionRate = 0;
            $totalRevenue = 0;
            $averageDealSize = 0;
            $sourcePerformance = collect([]);
            $monthlyTrend = collect([]);
            $sourceQuality = collect([]);
            $costAnalysis = collect([]);

            return view('performance.index', compact(
                'startDate', 'endDate', 'sourceId', 'sources',
                'totalLeads', 'successfulLeads', 'conversionRate', 'totalRevenue', 'averageDealSize',
                'sourcePerformance', 'monthlyTrend', 'sourceQuality', 'costAnalysis'
            ));
        }
    }
}
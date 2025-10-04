<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FunnelController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
    
        try {
            // Build query with date filters
            $query = Lead::whereBetween('created_at', [$startDate, $endDate]);
            if ($sourceId) {
                $query->where('source_id', $sourceId);
            }
    
            // Get funnel data - leads by status
            $funnelData = $query->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->keyBy('status');
    
            // Define funnel stages in order
            $stages = [
                'new' => 'New Leads',
                'successful' => 'Successful',
                'lost' => 'Lost'
            ];
    
            // Prepare funnel chart data
            $funnelChartData = [];
            $totalLeads = $query->count();
            foreach ($stages as $status => $label) {
                $count = $funnelData->get($status)->count ?? 0;
                $percentage = $totalLeads > 0 ? round(($count / $totalLeads) * 100, 2) : 0;
                $funnelChartData[] = [
                    'stage' => $label,
                    'count' => $count,
                    'percentage' => $percentage,
                    'status' => $status
                ];
            }
    
            // Calculate conversion rates between stages
            $newLeads = $funnelData->get('new')->count ?? 0;
            $successfulLeads = $funnelData->get('successful')->count ?? 0;
            $lostLeads = $funnelData->get('lost')->count ?? 0;
            $conversionRates = [
                'new_to_successful' => $newLeads > 0 ? round(($successfulLeads / ($newLeads + $successfulLeads + $lostLeads)) * 100, 2) : 0,
                'overall_success_rate' => $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0,
                'drop_off_rate' => $totalLeads > 0 ? round(($lostLeads / $totalLeads) * 100, 2) : 0
            ];
    
            // Get funnel data by source for comparison
            $funnelBySource = Lead::select(
                    'sources.name as source_name',
                    'leads.status',
                    DB::raw('COUNT(*) as count')
                )
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->whereBetween('leads.created_at', [$startDate, $endDate])
                ->groupBy('sources.name', 'leads.status')
                ->get()
                ->groupBy('source_name');
    
            // Get time-based funnel progression
            $timeProgression = Lead::selectRaw(
                    'DATE(created_at) as date, 
                     status, 
                     COUNT(*) as count'
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date', 'status')
                ->orderBy('date')
                ->get()
                ->groupBy('date');
    
            // Get sources for filter dropdown
            $sources = Source::orderBy('name')->get();
    
            return view('funnel.index', compact(
                'funnelChartData',
                'conversionRates',
                'funnelBySource',
                'timeProgression',
                'sources',
                'startDate',
                'endDate',
                'sourceId',
                'totalLeads'
            ));
        } catch (\Throwable $e) {
            logger()->warning('Funnel Overview index failed, showing safe defaults', ['error' => $e->getMessage()]);
            session()->flash('error', 'We are currently unable to load the funnel overview. Please try again later.');
    
            $funnelChartData = [];
            $conversionRates = [
                'new_to_successful' => 0,
                'overall_success_rate' => 0,
                'drop_off_rate' => 0
            ];
            $funnelBySource = [];
            $timeProgression = [];
            $sources = collect([]);
            $totalLeads = 0;
    
            return view('funnel.index', compact(
                'funnelChartData',
                'conversionRates',
                'funnelBySource',
                'timeProgression',
                'sources',
                'startDate',
                'endDate',
                'sourceId',
                'totalLeads'
            ));
        }
    }

    public function getTimelineData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');

        $query = Lead::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }

        $timelineData = $query->selectRaw(
                'DATE(created_at) as date,
                 SUM(CASE WHEN status = "new" THEN 1 ELSE 0 END) as new_count,
                 SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful_count,
                 SUM(CASE WHEN status = "lost" THEN 1 ELSE 0 END) as lost_count'
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($timelineData);
    }
}
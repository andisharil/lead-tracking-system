<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Source;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConversionFunnelController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        $campaignId = $request->get('campaign_id');
        
        // Get funnel data
        $funnelData = $this->getFunnelData($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get conversion rates
        $conversionRates = $this->getConversionRates($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get drop-off analysis
        $dropOffAnalysis = $this->getDropOffAnalysis($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get stage performance over time
        $stagePerformance = $this->getStagePerformance($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get source funnel comparison
        $sourceFunnelComparison = $this->getSourceFunnelComparison($dateFrom, $dateTo);
        
        // Get campaign funnel comparison
        $campaignFunnelComparison = $this->getCampaignFunnelComparison($dateFrom, $dateTo);
        
        // Get funnel velocity (time between stages)
        $funnelVelocity = $this->getFunnelVelocity($dateFrom, $dateTo, $sourceId, $campaignId);
        
        $sources = Source::all();
        $campaigns = Campaign::all();
        
        return view('conversion-funnel.index', compact(
            'funnelData', 'conversionRates', 'dropOffAnalysis', 'stagePerformance',
            'sourceFunnelComparison', 'campaignFunnelComparison', 'funnelVelocity',
            'sources', 'campaigns', 'dateFrom', 'dateTo', 'sourceId', 'campaignId'
        ));
    }
    
    private function getFunnelData($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $leads = $query->get();
        
        // Define funnel stages in order
        $stages = [
            'new' => 'New Leads',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'proposal' => 'Proposal Sent',
            'negotiation' => 'In Negotiation',
            'closed' => 'Closed Won',
            'lost' => 'Closed Lost'
        ];
        
        $funnelData = [];
        $totalLeads = $leads->count();
        
        foreach ($stages as $status => $label) {
            $count = $leads->where('status', $status)->count();
            $percentage = $totalLeads > 0 ? ($count / $totalLeads) * 100 : 0;
            
            $funnelData[] = [
                'stage' => $status,
                'label' => $label,
                'count' => $count,
                'percentage' => $percentage,
                'total_value' => $leads->where('status', $status)->sum('value'),
                'avg_value' => $count > 0 ? $leads->where('status', $status)->avg('value') : 0,
            ];
        }
        
        return $funnelData;
    }
    
    private function getConversionRates($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $leads = $query->get();
        $totalLeads = $leads->count();
        
        if ($totalLeads === 0) {
            return [];
        }
        
        // Calculate conversion rates between stages
        $stages = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed'];
        $conversionRates = [];
        
        for ($i = 0; $i < count($stages) - 1; $i++) {
            $currentStage = $stages[$i];
            $nextStage = $stages[$i + 1];
            
            // Get leads that reached current stage or beyond
            $currentStageLeads = $this->getLeadsAtStageOrBeyond($leads, $currentStage);
            $nextStageLeads = $this->getLeadsAtStageOrBeyond($leads, $nextStage);
            
            $conversionRate = $currentStageLeads > 0 ? ($nextStageLeads / $currentStageLeads) * 100 : 0;
            
            $conversionRates[] = [
                'from_stage' => $currentStage,
                'to_stage' => $nextStage,
                'from_count' => $currentStageLeads,
                'to_count' => $nextStageLeads,
                'conversion_rate' => $conversionRate,
            ];
        }
        
        // Overall conversion rate (new to closed)
        $closedLeads = $leads->where('status', 'closed')->count();
        $overallConversionRate = $totalLeads > 0 ? ($closedLeads / $totalLeads) * 100 : 0;
        
        return [
            'stage_conversions' => $conversionRates,
            'overall_conversion_rate' => $overallConversionRate,
            'total_leads' => $totalLeads,
            'closed_leads' => $closedLeads,
        ];
    }
    
    private function getLeadsAtStageOrBeyond($leads, $targetStage)
    {
        $stageOrder = ['new' => 1, 'contacted' => 2, 'qualified' => 3, 'proposal' => 4, 'negotiation' => 5, 'closed' => 6, 'lost' => 6];
        $targetOrder = $stageOrder[$targetStage] ?? 0;
        
        return $leads->filter(function($lead) use ($stageOrder, $targetOrder) {
            $leadOrder = $stageOrder[$lead->status] ?? 0;
            return $leadOrder >= $targetOrder;
        })->count();
    }
    
    private function getDropOffAnalysis($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $leads = $query->get();
        $stages = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed'];
        $dropOffs = [];
        
        for ($i = 0; $i < count($stages) - 1; $i++) {
            $currentStage = $stages[$i];
            $nextStage = $stages[$i + 1];
            
            $currentCount = $this->getLeadsAtStageOrBeyond($leads, $currentStage);
            $nextCount = $this->getLeadsAtStageOrBeyond($leads, $nextStage);
            
            $dropOffCount = $currentCount - $nextCount;
            $dropOffRate = $currentCount > 0 ? ($dropOffCount / $currentCount) * 100 : 0;
            
            $dropOffs[] = [
                'stage' => $currentStage,
                'next_stage' => $nextStage,
                'current_count' => $currentCount,
                'next_count' => $nextCount,
                'drop_off_count' => $dropOffCount,
                'drop_off_rate' => $dropOffRate,
            ];
        }
        
        // Find the stage with highest drop-off
        $highestDropOff = collect($dropOffs)->sortByDesc('drop_off_rate')->first();
        
        return [
            'stage_dropoffs' => $dropOffs,
            'highest_dropoff' => $highestDropOff,
        ];
    }
    
    private function getStagePerformance($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $leads = $query->get();
        
        // Group by week and status
        $weeklyPerformance = $leads->groupBy(function($lead) {
            return $lead->created_at->format('Y-W');
        })->map(function($weekLeads) {
            return $weekLeads->groupBy('status')->map->count();
        });
        
        // Prepare data for chart
        $weeks = $weeklyPerformance->keys()->sort();
        $stages = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed'];
        
        $chartData = [];
        foreach ($stages as $stage) {
            $stageData = [];
            foreach ($weeks as $week) {
                $stageData[] = $weeklyPerformance->get($week, collect())->get($stage, 0);
            }
            $chartData[$stage] = $stageData;
        }
        
        return [
            'weeks' => $weeks->values()->toArray(),
            'stage_data' => $chartData,
        ];
    }
    
    private function getSourceFunnelComparison($dateFrom, $dateTo)
    {
        $sources = Source::with(['leads' => function($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }])->get();
        
        $comparison = [];
        
        foreach ($sources as $source) {
            $leads = $source->leads;
            $totalLeads = $leads->count();
            
            if ($totalLeads > 0) {
                $closedLeads = $leads->where('status', 'closed')->count();
                $conversionRate = ($closedLeads / $totalLeads) * 100;
                
                $comparison[] = [
                    'source_name' => $source->name,
                    'source_type' => $source->type,
                    'total_leads' => $totalLeads,
                    'closed_leads' => $closedLeads,
                    'conversion_rate' => $conversionRate,
                    'total_value' => $leads->where('status', 'closed')->sum('value'),
                    'avg_deal_size' => $closedLeads > 0 ? $leads->where('status', 'closed')->avg('value') : 0,
                ];
            }
        }
        
        return collect($comparison)->sortByDesc('conversion_rate')->take(10);
    }
    
    private function getCampaignFunnelComparison($dateFrom, $dateTo)
    {
        $campaigns = Campaign::with(['leads' => function($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }])->get();
        
        $comparison = [];
        
        foreach ($campaigns as $campaign) {
            $leads = $campaign->leads;
            $totalLeads = $leads->count();
            
            if ($totalLeads > 0) {
                $closedLeads = $leads->where('status', 'closed')->count();
                $conversionRate = ($closedLeads / $totalLeads) * 100;
                
                $comparison[] = [
                    'campaign_name' => $campaign->name,
                    'campaign_type' => $campaign->type,
                    'campaign_status' => $campaign->status,
                    'total_leads' => $totalLeads,
                    'closed_leads' => $closedLeads,
                    'conversion_rate' => $conversionRate,
                    'total_value' => $leads->where('status', 'closed')->sum('value'),
                    'avg_deal_size' => $closedLeads > 0 ? $leads->where('status', 'closed')->avg('value') : 0,
                ];
            }
        }
        
        return collect($comparison)->sortByDesc('conversion_rate')->take(10);
    }
    
    private function getFunnelVelocity($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        // This would require tracking stage change timestamps
        // For now, we'll return mock data showing average days in each stage
        // In a real implementation, you'd need a lead_stage_history table
        
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('status', 'closed');
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $closedLeads = $query->get();
        
        // Calculate average time from creation to close
        $avgTimeToClose = 0;
        if ($closedLeads->count() > 0) {
            $totalDays = $closedLeads->sum(function($lead) {
                return $lead->created_at->diffInDays($lead->updated_at);
            });
            $avgTimeToClose = $totalDays / $closedLeads->count();
        }
        
        // Mock stage velocity data (in a real app, this would come from stage history)
        $stageVelocity = [
            'new_to_contacted' => ['avg_days' => 1.2, 'median_days' => 1],
            'contacted_to_qualified' => ['avg_days' => 3.5, 'median_days' => 2],
            'qualified_to_proposal' => ['avg_days' => 5.8, 'median_days' => 4],
            'proposal_to_negotiation' => ['avg_days' => 7.2, 'median_days' => 6],
            'negotiation_to_closed' => ['avg_days' => 4.1, 'median_days' => 3],
        ];
        
        return [
            'avg_time_to_close' => $avgTimeToClose,
            'total_closed_leads' => $closedLeads->count(),
            'stage_velocity' => $stageVelocity,
        ];
    }
    
    public function getStageDetails(Request $request)
    {
        $stage = $request->get('stage');
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        $campaignId = $request->get('campaign_id');
        
        $query = Lead::with(['source', 'campaign'])
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('status', $stage);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $leads = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'leads' => $leads->items(),
            'pagination' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'total' => $leads->total(),
            ]
        ]);
    }
}
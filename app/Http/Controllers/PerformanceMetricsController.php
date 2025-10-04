<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Source;
use App\Models\Campaign;
use App\Models\AdSpend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceMetricsController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        $campaignId = $request->get('campaign_id');
        $metric = $request->get('metric', 'roi');
        
        // Get ROI analysis
        $roiAnalysis = $this->getROIAnalysis($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get conversion rate tracking
        $conversionTracking = $this->getConversionTracking($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get source effectiveness
        $sourceEffectiveness = $this->getSourceEffectiveness($dateFrom, $dateTo);
        
        // Get campaign effectiveness
        $campaignEffectiveness = $this->getCampaignEffectiveness($dateFrom, $dateTo);
        
        // Get performance trends
        $performanceTrends = $this->getPerformanceTrends($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get cost analysis
        $costAnalysis = $this->getCostAnalysis($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get lead quality metrics
        $leadQualityMetrics = $this->getLeadQualityMetrics($dateFrom, $dateTo, $sourceId, $campaignId);
        
        // Get performance benchmarks
        $benchmarks = $this->getPerformanceBenchmarks($dateFrom, $dateTo);
        
        $sources = Source::all();
        $campaigns = Campaign::all();
        
        return view('performance-metrics.index', compact(
            'roiAnalysis', 'conversionTracking', 'sourceEffectiveness', 'campaignEffectiveness',
            'performanceTrends', 'costAnalysis', 'leadQualityMetrics', 'benchmarks',
            'sources', 'campaigns', 'dateFrom', 'dateTo', 'sourceId', 'campaignId', 'metric'
        ));
    }
    
    private function getROIAnalysis($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $leadsQuery = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        $spendQuery = AdSpend::whereBetween('spend_date', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $leadsQuery->where('source_id', $sourceId);
            $spendQuery->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $leadsQuery->where('campaign_id', $campaignId);
            $spendQuery->where('campaign_id', $campaignId);
        }
        
        $leads = $leadsQuery->get();
        $totalSpend = $spendQuery->sum('amount_spent');
        
        // Calculate revenue from closed leads
        $totalRevenue = $leads->where('status', 'closed')->sum('value');
        $totalLeads = $leads->count();
        $closedLeads = $leads->where('status', 'closed')->count();
        
        // Calculate ROI
        $roi = $totalSpend > 0 ? (($totalRevenue - $totalSpend) / $totalSpend) * 100 : 0;
        $roas = $totalSpend > 0 ? ($totalRevenue / $totalSpend) : 0;
        
        // Calculate cost metrics
        $costPerLead = $totalLeads > 0 ? $totalSpend / $totalLeads : 0;
        $costPerAcquisition = $closedLeads > 0 ? $totalSpend / $closedLeads : 0;
        
        // Calculate lifetime value
        $avgDealSize = $closedLeads > 0 ? $totalRevenue / $closedLeads : 0;
        $conversionRate = $totalLeads > 0 ? ($closedLeads / $totalLeads) * 100 : 0;
        
        // Get previous period for comparison
        $previousPeriodStart = Carbon::parse($dateFrom)->subDays(Carbon::parse($dateTo)->diffInDays(Carbon::parse($dateFrom)));
        $previousPeriodEnd = Carbon::parse($dateFrom)->subDay();
        
        $previousLeads = Lead::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd]);
        $previousSpend = AdSpend::whereBetween('spend_date', [$previousPeriodStart, $previousPeriodEnd]);
        
        if ($sourceId) {
            $previousLeads->where('source_id', $sourceId);
            $previousSpend->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $previousLeads->where('campaign_id', $campaignId);
            $previousSpend->where('campaign_id', $campaignId);
        }
        
        $prevTotalSpend = $previousSpend->sum('amount_spent');
        $prevTotalRevenue = $previousLeads->where('status', 'closed')->sum('value');
        $prevROI = $prevTotalSpend > 0 ? (($prevTotalRevenue - $prevTotalSpend) / $prevTotalSpend) * 100 : 0;
        
        $roiChange = $prevROI != 0 ? (($roi - $prevROI) / abs($prevROI)) * 100 : 0;
        
        return [
            'total_spend' => $totalSpend,
            'total_revenue' => $totalRevenue,
            'roi' => $roi,
            'roas' => $roas,
            'cost_per_lead' => $costPerLead,
            'cost_per_acquisition' => $costPerAcquisition,
            'avg_deal_size' => $avgDealSize,
            'conversion_rate' => $conversionRate,
            'total_leads' => $totalLeads,
            'closed_leads' => $closedLeads,
            'roi_change' => $roiChange,
            'profit_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalSpend) / $totalRevenue) * 100 : 0,
        ];
    }
    
    private function getConversionTracking($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
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
        
        // Calculate conversion rates by stage
        $stages = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed', 'lost'];
        $stageConversions = [];
        
        foreach ($stages as $stage) {
            $stageCount = $leads->where('status', $stage)->count();
            $percentage = $totalLeads > 0 ? ($stageCount / $totalLeads) * 100 : 0;
            
            $stageConversions[$stage] = [
                'count' => $stageCount,
                'percentage' => $percentage,
                'value' => $leads->where('status', $stage)->sum('value'),
            ];
        }
        
        // Calculate time-based conversions (daily)
        $dailyConversions = $leads->groupBy(function($lead) {
            return $lead->created_at->format('Y-m-d');
        })->map(function($dayLeads) {
            $total = $dayLeads->count();
            $closed = $dayLeads->where('status', 'closed')->count();
            return [
                'date' => $dayLeads->first()->created_at->format('Y-m-d'),
                'total_leads' => $total,
                'closed_leads' => $closed,
                'conversion_rate' => $total > 0 ? ($closed / $total) * 100 : 0,
                'revenue' => $dayLeads->where('status', 'closed')->sum('value'),
            ];
        })->values();
        
        // Calculate conversion velocity (average days to close)
        $closedLeads = $leads->where('status', 'closed');
        $avgTimeToClose = 0;
        
        if ($closedLeads->count() > 0) {
            $totalDays = $closedLeads->sum(function($lead) {
                return $lead->created_at->diffInDays($lead->updated_at);
            });
            $avgTimeToClose = $totalDays / $closedLeads->count();
        }
        
        return [
            'stage_conversions' => $stageConversions,
            'daily_conversions' => $dailyConversions,
            'avg_time_to_close' => $avgTimeToClose,
            'overall_conversion_rate' => $totalLeads > 0 ? ($stageConversions['closed']['count'] / $totalLeads) * 100 : 0,
            'win_rate' => $totalLeads > 0 ? ($stageConversions['closed']['count'] / ($stageConversions['closed']['count'] + $stageConversions['lost']['count'])) * 100 : 0,
        ];
    }
    
    private function getSourceEffectiveness($dateFrom, $dateTo)
    {
        $sources = Source::with(['leads' => function($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }, 'adSpends' => function($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('spend_date', [$dateFrom, $dateTo]);
        }])->get();
        
        $effectiveness = [];
        
        foreach ($sources as $source) {
            $leads = $source->leads;
            $totalLeads = $leads->count();
            $closedLeads = $leads->where('status', 'closed')->count();
            $totalRevenue = $leads->where('status', 'closed')->sum('value');
            $totalSpend = $source->adSpends->sum('amount_spent');
            
            if ($totalLeads > 0) {
                $conversionRate = ($closedLeads / $totalLeads) * 100;
                $costPerLead = $totalSpend > 0 ? $totalSpend / $totalLeads : 0;
                $costPerAcquisition = $closedLeads > 0 ? $totalSpend / $closedLeads : 0;
                $roi = $totalSpend > 0 ? (($totalRevenue - $totalSpend) / $totalSpend) * 100 : 0;
                $avgDealSize = $closedLeads > 0 ? $totalRevenue / $closedLeads : 0;
                
                // Calculate lead quality score (based on conversion rate, deal size, and time to close)
                $qualityScore = ($conversionRate * 0.4) + (min($avgDealSize / 1000, 100) * 0.3) + (max(0, 100 - $costPerLead / 10) * 0.3);
                
                $effectiveness[] = [
                    'source_id' => $source->id,
                    'source_name' => $source->name,
                    'source_type' => $source->type,
                    'total_leads' => $totalLeads,
                    'closed_leads' => $closedLeads,
                    'conversion_rate' => $conversionRate,
                    'total_revenue' => $totalRevenue,
                    'total_spend' => $totalSpend,
                    'roi' => $roi,
                    'cost_per_lead' => $costPerLead,
                    'cost_per_acquisition' => $costPerAcquisition,
                    'avg_deal_size' => $avgDealSize,
                    'quality_score' => $qualityScore,
                    'profit' => $totalRevenue - $totalSpend,
                ];
            }
        }
        
        return collect($effectiveness)->sortByDesc('quality_score');
    }
    
    private function getCampaignEffectiveness($dateFrom, $dateTo)
    {
        $campaigns = Campaign::with(['leads' => function($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }, 'adSpends' => function($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('spend_date', [$dateFrom, $dateTo]);
        }])->get();
        
        $effectiveness = [];
        
        foreach ($campaigns as $campaign) {
            $leads = $campaign->leads;
            $totalLeads = $leads->count();
            $closedLeads = $leads->where('status', 'closed')->count();
            $totalRevenue = $leads->where('status', 'closed')->sum('value');
            $totalSpend = $campaign->adSpends->sum('amount_spent');
            
            if ($totalLeads > 0) {
                $conversionRate = ($closedLeads / $totalLeads) * 100;
                $costPerLead = $totalSpend > 0 ? $totalSpend / $totalLeads : 0;
                $costPerAcquisition = $closedLeads > 0 ? $totalSpend / $closedLeads : 0;
                $roi = $totalSpend > 0 ? (($totalRevenue - $totalSpend) / $totalSpend) * 100 : 0;
                $avgDealSize = $closedLeads > 0 ? $totalRevenue / $closedLeads : 0;
                
                // Calculate campaign performance score
                $performanceScore = ($conversionRate * 0.3) + (min($roi / 10, 100) * 0.4) + (min($avgDealSize / 1000, 100) * 0.3);
                
                $effectiveness[] = [
                    'campaign_id' => $campaign->id,
                    'campaign_name' => $campaign->name,
                    'campaign_type' => $campaign->type,
                    'campaign_status' => $campaign->status,
                    'total_leads' => $totalLeads,
                    'closed_leads' => $closedLeads,
                    'conversion_rate' => $conversionRate,
                    'total_revenue' => $totalRevenue,
                    'total_spend' => $totalSpend,
                    'roi' => $roi,
                    'cost_per_lead' => $costPerLead,
                    'cost_per_acquisition' => $costPerAcquisition,
                    'avg_deal_size' => $avgDealSize,
                    'performance_score' => $performanceScore,
                    'profit' => $totalRevenue - $totalSpend,
                ];
            }
        }
        
        return collect($effectiveness)->sortByDesc('performance_score');
    }
    
    private function getPerformanceTrends($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $leadsQuery = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        $spendQuery = AdSpend::whereBetween('spend_date', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $leadsQuery->where('source_id', $sourceId);
            $spendQuery->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $leadsQuery->where('campaign_id', $campaignId);
            $spendQuery->where('campaign_id', $campaignId);
        }
        
        $leads = $leadsQuery->get();
        $spends = $spendQuery->get();
        
        // Group by week
        $weeklyTrends = [];
        $weeks = [];
        
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);
        
        while ($startDate <= $endDate) {
            $weekStart = $startDate->copy()->startOfWeek();
            $weekEnd = $startDate->copy()->endOfWeek();
            $weekKey = $weekStart->format('Y-W');
            
            if (!in_array($weekKey, $weeks)) {
                $weeks[] = $weekKey;
                
                $weekLeads = $leads->filter(function($lead) use ($weekStart, $weekEnd) {
                    return $lead->created_at->between($weekStart, $weekEnd);
                });
                
                $weekSpends = $spends->filter(function($spend) use ($weekStart, $weekEnd) {
                    return Carbon::parse($spend->spend_date)->between($weekStart, $weekEnd);
                });
                
                $totalLeads = $weekLeads->count();
                $closedLeads = $weekLeads->where('status', 'closed')->count();
                $revenue = $weekLeads->where('status', 'closed')->sum('value');
                $spend = $weekSpends->sum('amount_spent');
                
                $weeklyTrends[] = [
                    'week' => $weekKey,
                    'week_label' => $weekStart->format('M j') . ' - ' . $weekEnd->format('M j'),
                    'total_leads' => $totalLeads,
                    'closed_leads' => $closedLeads,
                    'conversion_rate' => $totalLeads > 0 ? ($closedLeads / $totalLeads) * 100 : 0,
                    'revenue' => $revenue,
                    'spend' => $spend,
                    'roi' => $spend > 0 ? (($revenue - $spend) / $spend) * 100 : 0,
                    'cost_per_lead' => $totalLeads > 0 ? $spend / $totalLeads : 0,
                ];
            }
            
            $startDate->addWeek();
        }
        
        return $weeklyTrends;
    }
    
    private function getCostAnalysis($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $spendQuery = AdSpend::with(['source', 'campaign'])
                            ->whereBetween('spend_date', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $spendQuery->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $spendQuery->where('campaign_id', $campaignId);
        }
        
        $spends = $spendQuery->get();
        
        // Cost breakdown by platform
        $platformCosts = $spends->groupBy('platform')->map(function($platformSpends) {
            return [
                'total_spend' => $platformSpends->sum('amount_spent'),
                'avg_spend' => $platformSpends->avg('amount_spent'),
                'count' => $platformSpends->count(),
            ];
        });
        
        // Cost breakdown by ad type
        $adTypeCosts = $spends->groupBy('ad_type')->map(function($typeSpends) {
            return [
                'total_spend' => $typeSpends->sum('amount_spent'),
                'avg_spend' => $typeSpends->avg('amount_spent'),
                'count' => $typeSpends->count(),
            ];
        });
        
        // Daily spend trend
        $dailySpend = $spends->groupBy(function($spend) {
            return Carbon::parse($spend->spend_date)->format('Y-m-d');
        })->map(function($daySpends, $date) {
            return [
                'date' => $date,
                'total_spend' => $daySpends->sum('amount_spent'),
                'impressions' => $daySpends->sum('impressions'),
                'clicks' => $daySpends->sum('clicks'),
                'conversions' => $daySpends->sum('conversions'),
            ];
        })->values();
        
        return [
            'total_spend' => $spends->sum('amount_spent'),
            'avg_daily_spend' => $dailySpend->avg('total_spend'),
            'platform_breakdown' => $platformCosts,
            'ad_type_breakdown' => $adTypeCosts,
            'daily_spend_trend' => $dailySpend,
            'spend_efficiency' => $spends->sum('conversions') > 0 ? $spends->sum('amount_spent') / $spends->sum('conversions') : 0,
        ];
    }
    
    private function getLeadQualityMetrics($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $query = Lead::with(['source', 'campaign'])
                    ->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $leads = $query->get();
        
        // Quality metrics by source
        $sourceQuality = $leads->groupBy('source_id')->map(function($sourceLeads) {
            $total = $sourceLeads->count();
            $qualified = $sourceLeads->whereIn('status', ['qualified', 'proposal', 'negotiation', 'closed'])->count();
            $closed = $sourceLeads->where('status', 'closed')->count();
            
            return [
                'source_name' => $sourceLeads->first()->source->name ?? 'Unknown',
                'total_leads' => $total,
                'qualified_leads' => $qualified,
                'closed_leads' => $closed,
                'qualification_rate' => $total > 0 ? ($qualified / $total) * 100 : 0,
                'close_rate' => $total > 0 ? ($closed / $total) * 100 : 0,
                'avg_value' => $sourceLeads->where('status', 'closed')->avg('value') ?? 0,
                'quality_score' => $total > 0 ? (($qualified * 0.3) + ($closed * 0.7)) / $total * 100 : 0,
            ];
        });
        
        // Lead scoring distribution
        $scoreDistribution = [
            'high_quality' => $leads->filter(function($lead) {
                return in_array($lead->status, ['proposal', 'negotiation', 'closed']) && $lead->value > 1000;
            })->count(),
            'medium_quality' => $leads->filter(function($lead) {
                return in_array($lead->status, ['qualified', 'proposal']) && $lead->value <= 1000;
            })->count(),
            'low_quality' => $leads->filter(function($lead) {
                return in_array($lead->status, ['new', 'contacted', 'lost']);
            })->count(),
        ];
        
        return [
            'source_quality' => $sourceQuality,
            'score_distribution' => $scoreDistribution,
            'avg_lead_value' => $leads->avg('value') ?? 0,
            'total_pipeline_value' => $leads->whereNotIn('status', ['lost'])->sum('value'),
        ];
    }
    
    private function getPerformanceBenchmarks($dateFrom, $dateTo)
    {
        // Industry benchmarks (these would typically come from external data)
        $industryBenchmarks = [
            'conversion_rate' => 2.5, // 2.5% industry average
            'cost_per_lead' => 50,    // $50 industry average
            'roi' => 300,             // 300% industry average
            'avg_deal_size' => 1500,  // $1500 industry average
        ];
        
        // Calculate current performance
        $leads = Lead::whereBetween('created_at', [$dateFrom, $dateTo])->get();
        $spends = AdSpend::whereBetween('spend_date', [$dateFrom, $dateTo])->get();
        
        $totalLeads = $leads->count();
        $closedLeads = $leads->where('status', 'closed')->count();
        $totalRevenue = $leads->where('status', 'closed')->sum('value');
        $totalSpend = $spends->sum('amount_spent');
        
        $currentMetrics = [
            'conversion_rate' => $totalLeads > 0 ? ($closedLeads / $totalLeads) * 100 : 0,
            'cost_per_lead' => $totalLeads > 0 ? $totalSpend / $totalLeads : 0,
            'roi' => $totalSpend > 0 ? (($totalRevenue - $totalSpend) / $totalSpend) * 100 : 0,
            'avg_deal_size' => $closedLeads > 0 ? $totalRevenue / $closedLeads : 0,
        ];
        
        // Compare with benchmarks
        $comparisons = [];
        foreach ($industryBenchmarks as $metric => $benchmark) {
            $current = $currentMetrics[$metric];
            $difference = $current - $benchmark;
            $percentageDiff = $benchmark > 0 ? ($difference / $benchmark) * 100 : 0;
            
            $comparisons[$metric] = [
                'current' => $current,
                'benchmark' => $benchmark,
                'difference' => $difference,
                'percentage_diff' => $percentageDiff,
                'status' => $percentageDiff > 10 ? 'above' : ($percentageDiff < -10 ? 'below' : 'on_par'),
            ];
        }
        
        return $comparisons;
    }
    
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        $roiAnalysis = $this->getROIAnalysis($dateFrom, $dateTo);
        $sourceEffectiveness = $this->getSourceEffectiveness($dateFrom, $dateTo);
        
        if ($format === 'csv') {
            return $this->exportCSV($roiAnalysis, $sourceEffectiveness, $dateFrom, $dateTo);
        }
        
        return response()->json(['error' => 'Unsupported format'], 400);
    }
    
    private function exportCSV($roiAnalysis, $sourceEffectiveness, $dateFrom, $dateTo)
    {
        $filename = "performance_metrics_{$dateFrom}_to_{$dateTo}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($roiAnalysis, $sourceEffectiveness) {
            $file = fopen('php://output', 'w');
            
            // ROI Analysis section
            fputcsv($file, ['ROI Analysis']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Spend', '$' . number_format($roiAnalysis['total_spend'], 2)]);
            fputcsv($file, ['Total Revenue', '$' . number_format($roiAnalysis['total_revenue'], 2)]);
            fputcsv($file, ['ROI', number_format($roiAnalysis['roi'], 2) . '%']);
            fputcsv($file, ['ROAS', number_format($roiAnalysis['roas'], 2)]);
            fputcsv($file, ['Cost Per Lead', '$' . number_format($roiAnalysis['cost_per_lead'], 2)]);
            fputcsv($file, ['Cost Per Acquisition', '$' . number_format($roiAnalysis['cost_per_acquisition'], 2)]);
            fputcsv($file, ['Conversion Rate', number_format($roiAnalysis['conversion_rate'], 2) . '%']);
            fputcsv($file, []);
            
            // Source Effectiveness section
            fputcsv($file, ['Source Effectiveness']);
            fputcsv($file, ['Source', 'Total Leads', 'Closed Leads', 'Conversion Rate', 'Revenue', 'Spend', 'ROI', 'Quality Score']);
            
            foreach ($sourceEffectiveness as $source) {
                fputcsv($file, [
                    $source['source_name'],
                    $source['total_leads'],
                    $source['closed_leads'],
                    number_format($source['conversion_rate'], 2) . '%',
                    '$' . number_format($source['total_revenue'], 2),
                    '$' . number_format($source['total_spend'], 2),
                    number_format($source['roi'], 2) . '%',
                    number_format($source['quality_score'], 2),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
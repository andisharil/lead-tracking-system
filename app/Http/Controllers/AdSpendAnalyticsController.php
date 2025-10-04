<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Source;
use App\Models\Campaign;
use App\Models\AdSpend;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdSpendAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $source = $request->get('source');
        $campaign = $request->get('campaign');
        $platform = $request->get('platform');
        
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        
        if ($request->get('start_date') && $request->get('end_date')) {
            $startDate = Carbon::parse($request->get('start_date'));
            $endDate = Carbon::parse($request->get('end_date'));
        }
        
        // Get overview metrics
        $overview = $this->getOverviewMetrics($startDate, $endDate, $source, $campaign, $platform);
        
        // Get spend breakdown by source
        $sourceBreakdown = $this->getSourceBreakdown($startDate, $endDate, $source, $campaign, $platform);
        
        // Get spend breakdown by campaign
        $campaignBreakdown = $this->getCampaignBreakdown($startDate, $endDate, $source, $campaign, $platform);
        
        // Get daily spend trend
        $dailySpendTrend = $this->getDailySpendTrend($startDate, $endDate, $source, $campaign, $platform);
        
        // Get platform performance
        $platformPerformance = $this->getPlatformPerformance($startDate, $endDate, $source, $campaign, $platform);
        
        // Get ROI analysis
        $roiAnalysis = $this->getROIAnalysis($startDate, $endDate, $source, $campaign, $platform);
        
        // Get budget utilization
        $budgetUtilization = $this->getBudgetUtilization($startDate, $endDate, $source, $campaign, $platform);
        
        // Get filter options
        $sources = Source::where('status', 'active')->get();
        $campaigns = Campaign::where('status', 'active')->get();
        // Pull platform options from ad_spend table since campaigns table has no platform column
        $platforms = AdSpend::query()
            ->select('platform')
            ->whereNotNull('platform')
            ->distinct()
            ->pluck('platform');
        
        return view('ad-spend-analytics.index', compact(
            'overview',
            'sourceBreakdown',
            'campaignBreakdown',
            'dailySpendTrend',
            'platformPerformance',
            'roiAnalysis',
            'budgetUtilization',
            'sources',
            'campaigns',
            'platforms',
            'dateRange',
            'source',
            'campaign',
            'platform'
        ));
    }
    
    public function export(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $source = $request->get('source');
        $campaign = $request->get('campaign');
        $platform = $request->get('platform');
        
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        
        if ($request->get('start_date') && $request->get('end_date')) {
            $startDate = Carbon::parse($request->get('start_date'));
            $endDate = Carbon::parse($request->get('end_date'));
        }
        
        // Get all data for export
        $data = [
            'overview' => $this->getOverviewMetrics($startDate, $endDate, $source, $campaign, $platform),
            'source_breakdown' => $this->getSourceBreakdown($startDate, $endDate, $source, $campaign, $platform),
            'campaign_breakdown' => $this->getCampaignBreakdown($startDate, $endDate, $source, $campaign, $platform),
            'platform_performance' => $this->getPlatformPerformance($startDate, $endDate, $source, $campaign, $platform),
            'roi_analysis' => $this->getROIAnalysis($startDate, $endDate, $source, $campaign, $platform),
        ];
        
        return response()->json($data);
    }
    
    private function getOverviewMetrics($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $query = Lead::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($source) {
            $query->where('source_id', $source);
        }
        
        if ($campaign) {
            $query->where('campaign_id', $campaign);
        }
        
        if ($platform) {
            // Filter leads by campaigns that have ad spend entries on the selected platform
            $campaignIds = AdSpend::query()
                ->where('platform', $platform)
                ->whereNotNull('campaign_id')
                ->distinct()
                ->pluck('campaign_id');
            if ($campaignIds->isNotEmpty()) {
                $query->whereIn('campaign_id', $campaignIds);
            } else {
                // No matching campaigns; ensure empty result
                $query->whereRaw('1=0');
            }
        }
        
        $leads = $query->get();
        $totalSpend = $this->calculateTotalSpend($startDate, $endDate, $source, $campaign, $platform);
        $totalRevenue = $leads->where('status', 'converted')->sum('value');
        $totalLeads = $leads->count();
        $conversions = $leads->where('status', 'converted')->count();
        
        $costPerLead = $totalLeads > 0 ? $totalSpend / $totalLeads : 0;
        $costPerConversion = $conversions > 0 ? $totalSpend / $conversions : 0;
        $roi = $totalSpend > 0 ? (($totalRevenue - $totalSpend) / $totalSpend) * 100 : 0;
        $roas = $totalSpend > 0 ? $totalRevenue / $totalSpend : 0;
        
        return [
            'total_spend' => $totalSpend,
            'total_revenue' => $totalRevenue,
            'total_leads' => $totalLeads,
            'conversions' => $conversions,
            'cost_per_lead' => $costPerLead,
            'cost_per_conversion' => $costPerConversion,
            'roi' => $roi,
            'roas' => $roas,
            'profit' => $totalRevenue - $totalSpend
        ];
    }
    
    private function getSourceBreakdown($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $query = Source::with(['leads' => function($q) use ($startDate, $endDate, $campaign, $platform) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($campaign) {
                $q->where('campaign_id', $campaign);
            }
            
            if ($platform) {
                $campaignIds = AdSpend::query()
                    ->where('platform', $platform)
                    ->whereNotNull('campaign_id')
                    ->distinct()
                    ->pluck('campaign_id');
                if ($campaignIds->isNotEmpty()) {
                    $q->whereIn('campaign_id', $campaignIds);
                } else {
                    $q->whereRaw('1=0');
                }
            }
        }]);
        
        if ($source) {
            $query->where('id', $source);
        }
        
        $sources = $query->get();
        
        return $sources->map(function($source) use ($startDate, $endDate) {
            $leads = $source->leads;
            $spend = $this->calculateSourceSpend($source->id, $startDate, $endDate);
            $revenue = $leads->where('status', 'converted')->sum('value');
            $conversions = $leads->where('status', 'converted')->count();
            $totalLeads = $leads->count();
            
            $costPerLead = $totalLeads > 0 ? $spend / $totalLeads : 0;
            $costPerConversion = $conversions > 0 ? $spend / $conversions : 0;
            $roi = $spend > 0 ? (($revenue - $spend) / $spend) * 100 : 0;
            $conversionRate = $totalLeads > 0 ? ($conversions / $totalLeads) * 100 : 0;
            
            return [
                'id' => $source->id,
                'name' => $source->name,
                'type' => $source->type,
                'spend' => $spend,
                'revenue' => $revenue,
                'leads' => $totalLeads,
                'conversions' => $conversions,
                'cost_per_lead' => $costPerLead,
                'cost_per_conversion' => $costPerConversion,
                'roi' => $roi,
                'conversion_rate' => $conversionRate,
                'profit' => $revenue - $spend
            ];
        })->sortByDesc('spend');
    }
    
    private function getCampaignBreakdown($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $query = Campaign::with([
            'leads' => function($q) use ($startDate, $endDate, $source) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
                
                if ($source) {
                    $q->where('source_id', $source);
                }
            },
            'adSpends' => function($q) use ($startDate, $endDate, $platform) {
                $q->whereBetween('spend_date', [$startDate, $endDate]);
                if ($platform) {
                    $q->where('platform', $platform);
                }
            }
        ]);
        
        if ($campaign) {
            $query->where('id', $campaign);
        }
        
        if ($platform) {
            $query->whereHas('adSpends', function($q) use ($platform, $startDate, $endDate) {
                $q->where('platform', $platform)
                  ->whereBetween('spend_date', [$startDate, $endDate]);
            });
        }
        
        $campaigns = $query->get();
        
        return $campaigns->map(function($campaign) use ($startDate, $endDate) {
            $leads = $campaign->leads;
            $spend = $campaign->adSpends->sum('amount_spent');
            $revenue = $leads->where('status', 'converted')->sum('value');
            $conversions = $leads->where('status', 'converted')->count();
            $totalLeads = $leads->count();
            
            $costPerLead = $totalLeads > 0 ? $spend / $totalLeads : 0;
            $costPerConversion = $conversions > 0 ? $spend / $conversions : 0;
            $roi = $spend > 0 ? (($revenue - $spend) / $spend) * 100 : 0;
            $conversionRate = $totalLeads > 0 ? ($conversions / $totalLeads) * 100 : 0;
            $budgetUtilization = $campaign->budget > 0 ? ($spend / $campaign->budget) * 100 : 0;
            
            // Determine primary platform for the campaign within the date range (most frequent in adSpends)
            $primaryPlatform = optional($campaign->adSpends->groupBy('platform')->sortByDesc(function($rows){return $rows->count();})->keys()->first());
            if (is_array($primaryPlatform)) { // safety if optional returns array-like
                $primaryPlatform = $campaign->adSpends->groupBy('platform')->sortByDesc(function($rows){return $rows->count();})->keys()->first();
            }
            
            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'platform' => $primaryPlatform ?? null,
                'type' => $campaign->type,
                'budget' => $campaign->budget,
                'spend' => $spend,
                'revenue' => $revenue,
                'leads' => $totalLeads,
                'conversions' => $conversions,
                'cost_per_lead' => $costPerLead,
                'cost_per_conversion' => $costPerConversion,
                'roi' => $roi,
                'conversion_rate' => $conversionRate,
                'budget_utilization' => $budgetUtilization,
                'profit' => $revenue - $spend
            ];
        })->sortByDesc('spend');
    }
    
    private function getDailySpendTrend($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $days = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
            
            $dailySpend = $this->calculateTotalSpend($dayStart, $dayEnd, $source, $campaign, $platform);
            
            $query = Lead::whereBetween('created_at', [$dayStart, $dayEnd]);
            
            if ($source) {
                $query->where('source_id', $source);
            }
            
            if ($campaign) {
                $query->where('campaign_id', $campaign);
            }
            
            if ($platform) {
                $campaignIds = AdSpend::query()
                    ->where('platform', $platform)
                    ->whereNotNull('campaign_id')
                    ->distinct()
                    ->pluck('campaign_id');
                if ($campaignIds->isNotEmpty()) {
                    $query->whereIn('campaign_id', $campaignIds);
                } else {
                    $query->whereRaw('1=0');
                }
            }
            
            $leads = $query->get();
            $dailyRevenue = $leads->where('status', 'converted')->sum('value');
            $dailyLeads = $leads->count();
            $dailyConversions = $leads->where('status', 'converted')->count();
            
            $days[] = [
                'date' => $currentDate->format('Y-m-d'),
                'spend' => $dailySpend,
                'revenue' => $dailyRevenue,
                'leads' => $dailyLeads,
                'conversions' => $dailyConversions,
                'profit' => $dailyRevenue - $dailySpend
            ];
            
            $currentDate->addDay();
        }
        
        return $days;
    }
    
    private function getPlatformPerformance($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $query = Campaign::with([
            'leads' => function($q) use ($startDate, $endDate, $source) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
                
                if ($source) {
                    $q->where('source_id', $source);
                }
            },
            'adSpends' => function($q) use ($startDate, $endDate, $platform) {
                $q->whereBetween('spend_date', [$startDate, $endDate]);
                if ($platform) {
                    $q->where('platform', $platform);
                }
            }
        ]);
        
        if ($campaign) {
            $query->where('id', $campaign);
        }
        
        if ($platform) {
            $query->whereHas('adSpends', function($q) use ($platform, $startDate, $endDate) {
                $q->where('platform', $platform)
                  ->whereBetween('spend_date', [$startDate, $endDate]);
            });
        }
        
        $campaigns = $query->get();
        
        // Aggregate by primary platform per campaign (most frequent platform in adSpends within the range)
        $platformData = $campaigns->map(function($campaign) use ($startDate, $endDate) {
            $allLeads = $campaign->leads;
            $totalSpend = $campaign->adSpends->sum('amount_spent');
            $revenue = $allLeads->where('status', 'converted')->sum('value');
            $conversions = $allLeads->where('status', 'converted')->count();
            $totalLeads = $allLeads->count();
            $totalBudget = $campaign->budget ?? 0;
            
            $primaryPlatform = $campaign->adSpends->groupBy('platform')
                ->sortByDesc(function($rows){ return $rows->count(); })
                ->keys()
                ->first();
            
            $costPerLead = $totalLeads > 0 ? $totalSpend / $totalLeads : 0;
            $costPerConversion = $conversions > 0 ? $totalSpend / $conversions : 0;
            $roi = $totalSpend > 0 ? (($revenue - $totalSpend) / $totalSpend) * 100 : 0;
            $conversionRate = $totalLeads > 0 ? ($conversions / $totalLeads) * 100 : 0;
            $budgetUtilization = $totalBudget > 0 ? ($totalSpend / $totalBudget) * 100 : 0;
            
            return [
                'platform' => $primaryPlatform,
                'campaigns' => 1,
                'budget' => $totalBudget,
                'spend' => $totalSpend,
                'revenue' => $revenue,
                'leads' => $totalLeads,
                'conversions' => $conversions,
                'cost_per_lead' => $costPerLead,
                'cost_per_conversion' => $costPerConversion,
                'roi' => $roi,
                'conversion_rate' => $conversionRate,
                'budget_utilization' => $budgetUtilization,
                'profit' => $revenue - $totalSpend
            ];
        })
        ->filter(function($row){ return !empty($row['platform']); })
        ->groupBy('platform')
        ->map(function($rows){
            return [
                'platform' => $rows->first()['platform'],
                'campaigns' => $rows->sum('campaigns'),
                'budget' => $rows->sum('budget'),
                'spend' => $rows->sum('spend'),
                'revenue' => $rows->sum('revenue'),
                'leads' => $rows->sum('leads'),
                'conversions' => $rows->sum('conversions'),
                'cost_per_lead' => ($rows->sum('leads') > 0) ? ($rows->sum('spend') / $rows->sum('leads')) : 0,
                'cost_per_conversion' => ($rows->sum('conversions') > 0) ? ($rows->sum('spend') / $rows->sum('conversions')) : 0,
                'roi' => ($rows->sum('spend') > 0) ? ((($rows->sum('revenue') - $rows->sum('spend')) / $rows->sum('spend')) * 100) : 0,
                'conversion_rate' => ($rows->sum('leads') > 0) ? (($rows->sum('conversions') / $rows->sum('leads')) * 100) : 0,
                'budget_utilization' => ($rows->sum('budget') > 0) ? (($rows->sum('spend') / $rows->sum('budget')) * 100) : 0,
                'profit' => $rows->sum('revenue') - $rows->sum('spend'),
            ];
        })
        ->sortByDesc('spend');
        
        return $platformData;
    }
    
    private function getROIAnalysis($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $sourceBreakdown = $this->getSourceBreakdown($startDate, $endDate, $source, $campaign, $platform);
        $campaignBreakdown = $this->getCampaignBreakdown($startDate, $endDate, $source, $campaign, $platform);
        
        // Top performing sources by ROI
        $topSources = $sourceBreakdown->sortByDesc('roi')->take(5);
        
        // Top performing campaigns by ROI
        $topCampaigns = $campaignBreakdown->sortByDesc('roi')->take(5);
        
        // Worst performing sources by ROI
        $worstSources = $sourceBreakdown->sortBy('roi')->take(5);
        
        // Worst performing campaigns by ROI
        $worstCampaigns = $campaignBreakdown->sortBy('roi')->take(5);
        
        return [
            'top_sources' => $topSources,
            'top_campaigns' => $topCampaigns,
            'worst_sources' => $worstSources,
            'worst_campaigns' => $worstCampaigns
        ];
    }
    
    private function getBudgetUtilization($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        $query = Campaign::query();
        
        if ($campaign) {
            $query->where('id', $campaign);
        }
        
        if ($platform) {
            $query->whereHas('adSpends', function($q) use ($platform, $startDate, $endDate) {
                $q->where('platform', $platform)
                  ->whereBetween('spend_date', [$startDate, $endDate]);
            });
        }
        
        $campaigns = $query->with(['adSpends' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('spend_date', [$startDate, $endDate]);
        }])->get();
        
        return $campaigns->map(function($campaign) use ($startDate, $endDate, $source) {
            $spend = $campaign->adSpends->sum('amount_spent');
            $utilization = $campaign->budget > 0 ? ($spend / $campaign->budget) * 100 : 0;
            $remaining = ($campaign->budget ?? 0) - $spend;
            
            $query = Lead::where('campaign_id', $campaign->id)
                        ->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($source) {
                $query->where('source_id', $source);
            }
            
            $leads = $query->get();
            $revenue = $leads->where('status', 'converted')->sum('value');
            
            return [
                'campaign_id' => $campaign->id,
                'campaign_name' => $campaign->name,
                'platform' => optional($campaign->adSpends->groupBy('platform')->keys()->first()),
                'budget' => $campaign->budget,
                'spend' => $spend,
                'remaining' => $remaining,
                'utilization' => $utilization,
                'revenue' => $revenue,
                'efficiency' => $spend > 0 ? $revenue / $spend : 0
            ];
        })->sortByDesc('utilization');
    }
    
    private function calculateTotalSpend($startDate, $endDate, $source = null, $campaign = null, $platform = null)
    {
        // Use actual ad spend table rather than estimating by leads
        $query = AdSpend::whereBetween('spend_date', [$startDate, $endDate]);
        
        if ($source) {
            $query->where('source_id', $source);
        }
        
        if ($campaign) {
            $query->where('campaign_id', $campaign);
        }
        
        if ($platform) {
            $query->where('platform', $platform);
        }
        
        return (float) $query->sum('amount_spent');
    }
    
    private function calculateSourceSpend($sourceId, $startDate, $endDate)
    {
        $source = Source::find($sourceId);
        // Prefer actual ad spend records when available
        $spendFromTable = AdSpend::where('source_id', $sourceId)
            ->whereBetween('spend_date', [$startDate, $endDate])
            ->sum('amount_spent');
        
        if ($spendFromTable > 0) {
            return (float) $spendFromTable;
        }
        
        // Fallback to estimate by cost per lead if no spend records
        $leadsCount = Lead::where('source_id', $sourceId)
                         ->whereBetween('created_at', [$startDate, $endDate])
                         ->count();
        
        $costPerLead = $source->cost_per_lead ?: 25;
        
        return $leadsCount * $costPerLead;
    }
    
    private function calculateCampaignSpend($campaignId, $startDate, $endDate)
    {
        // Prefer actual ad spend records
        $spendFromTable = AdSpend::where('campaign_id', $campaignId)
            ->whereBetween('spend_date', [$startDate, $endDate])
            ->sum('amount_spent');
        
        if ($spendFromTable > 0) {
            return (float) $spendFromTable;
        }
        
        // Fallback to estimate based on budget spread if no spend records
        $campaign = Campaign::find($campaignId);
        $days = $startDate->diffInDays($endDate) + 1;
        $dailyBudget = ($campaign && $campaign->budget) ? ($campaign->budget / 30) : 0; // Assume monthly budget
        
        return min($dailyBudget * $days, $campaign->budget ?? 0);
    }
}
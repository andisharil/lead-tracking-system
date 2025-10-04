<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Source;
use App\Models\Lead;
use App\Models\AdSpend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns.
     */
    public function index(Request $request)
    {
        $query = Campaign::with(['source', 'leads', 'adSpends'])
            ->withCount([
                'leads',
                'leads as conversions' => function ($q) {
                    $q->where('status', 'successful');
                },
            ]);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('source', function ($sourceQuery) use ($search) {
                      $sourceQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Source filter
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }
        
        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'status', 'type', 'budget', 'spent', 'start_date', 'end_date', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $campaigns = $query->paginate(15)->withQueryString();
        
        // Calculate summary metrics for overview
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::active()->count();
        $totalLeads = Lead::whereNotNull('campaign_id')->count();
        $successfulLeads = Lead::whereNotNull('campaign_id')->where('status', 'successful')->count();
        $avgConversionRate = $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0;
        
        // Bundle into overview metrics object for the view
        $overviewMetrics = (object) [
            'total_campaigns' => $totalCampaigns,
            'active_campaigns' => $activeCampaigns,
            'total_leads' => $totalLeads,
            'avg_conversion_rate' => $avgConversionRate,
        ];
        
        // Preserve filter inputs for the view
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        
        // Get sources for potential filters (not currently used in the view but kept for compatibility)
        $sources = Source::orderBy('name')->get();
        
        return view('campaigns.index', compact(
            'campaigns',
            'overviewMetrics',
            'search',
            'status',
            'sortBy',
            'sortOrder',
            'sources'
        ));
    }
    
    /**
     * Show the form for creating a new campaign.
     */
    public function create()
    {
        $sources = Source::where('status', 'active')->orderBy('name')->get();
        return view('campaigns.create', compact('sources'));
    }
    
    /**
     * Store a newly created campaign in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,paused,completed,draft',
            'type' => 'required|in:email,social,ppc,display,content,other',
            'source_id' => 'required|exists:sources,id',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'utm_source' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            'utm_campaign' => 'nullable|string|max:255',
            'utm_term' => 'nullable|string|max:255',
            'utm_content' => 'nullable|string|max:255',
            'targeting' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);
        
        Campaign::create($validated);
        
        return redirect()->route('campaigns.index')
                        ->with('success', 'Campaign created successfully!');
    }
    
    /**
     * Display the specified campaign.
     */
    public function show(Campaign $campaign)
    {
        $campaign->load(['source', 'leads\location', 'adSpends']);
        
        // Calculate performance metrics
        $totalLeads = $campaign->leads()->count();
        $successfulLeads = $campaign->leads()->where('status', 'successful')->count();
        $conversionRate = $campaign->clicks > 0 ? ($totalLeads / $campaign->clicks) * 100 : 0;
        $totalRevenue = $campaign->leads()->where('status', 'successful')->sum('value') ?? 0;
        $averageDealSize = $successfulLeads > 0 ? $totalRevenue / $successfulLeads : 0;
        
        // Get recent leads
        $recentLeads = $campaign->leads()
                               ->with('location')
                               ->orderBy('created_at', 'desc')
                               ->limit(10)
                               ->get();
        
        // Get performance data for chart (last 12 months)
        $performanceData = $this->getCampaignPerformanceData($campaign->id);
        
        // Calculate additional metrics
        $ctr = $campaign->impressions > 0 ? ($campaign->clicks / $campaign->impressions) * 100 : 0;
        $costPerLead = $totalLeads > 0 ? $campaign->spent / $totalLeads : 0;
        $roi = $campaign->spent > 0 ? (($totalRevenue - $campaign->spent) / $campaign->spent) * 100 : 0;
        
        return view('campaigns.show', compact(
            'campaign',
            'totalLeads',
            'successfulLeads',
            'conversionRate',
            'totalRevenue',
            'averageDealSize',
            'recentLeads',
            'performanceData',
            'ctr',
            'costPerLead',
            'roi'
        ));
    }
    
    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(Campaign $campaign)
    {
        $sources = Source::orderBy('name')->get();
        return view('campaigns.edit', compact('campaign', 'sources'));
    }
    
    /**
     * Update the specified campaign in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,paused,completed,draft',
            'type' => 'required|in:email,social,ppc,display,content,other',
            'source_id' => 'required|exists:sources,id',
            'budget' => 'nullable|numeric|min:0',
            'spent' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'utm_source' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            'utm_campaign' => 'nullable|string|max:255',
            'utm_term' => 'nullable|string|max:255',
            'utm_content' => 'nullable|string|max:255',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'targeting' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);
        
        // Calculate CTR, CPC, CPM if impressions and clicks are provided
        if (isset($validated['impressions']) && isset($validated['clicks'])) {
            if ($validated['impressions'] > 0) {
                $validated['ctr'] = ($validated['clicks'] / $validated['impressions']) * 100;
            }
            
            if ($validated['clicks'] > 0 && isset($validated['spent'])) {
                $validated['cpc'] = $validated['spent'] / $validated['clicks'];
            }
            
            if ($validated['impressions'] > 0 && isset($validated['spent'])) {
                $validated['cpm'] = ($validated['spent'] / $validated['impressions']) * 1000;
            }
        }
        
        $campaign->update($validated);
        
        return redirect()->route('campaigns.show', $campaign)
                        ->with('success', 'Campaign updated successfully!');
    }
    
    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(Campaign $campaign)
    {
        // Check if campaign has associated leads
        if ($campaign->leads()->count() > 0) {
            return redirect()->route('campaigns.index')
                           ->with('error', 'Cannot delete campaign with associated leads. Please reassign or delete leads first.');
        }
        
        $campaign->delete();
        
        return redirect()->route('campaigns.index')
                        ->with('success', 'Campaign deleted successfully!');
    }
    
    /**
     * Toggle campaign status between active and paused.
     */
    public function toggleStatus(Campaign $campaign)
    {
        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);
        
        return redirect()->back()
                        ->with('success', "Campaign status changed to {$newStatus}!");
    }
    
    /**
     * Get performance data for a specific campaign.
     */
    public function getPerformanceData(Campaign $campaign)
    {
        $data = $this->getCampaignPerformanceData($campaign->id);
        return response()->json($data);
    }
    
    /**
     * Get campaign analytics dashboard data.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Campaign performance by type
        $performanceByType = Campaign::select('type')
            ->selectRaw('COUNT(*) as campaign_count')
            ->selectRaw('SUM(budget) as total_budget')
            ->selectRaw('SUM(spent) as total_spent')
            ->selectRaw('SUM(impressions) as total_impressions')
            ->selectRaw('SUM(clicks) as total_clicks')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('type')
            ->get();
        
        // Top performing campaigns
        $topCampaigns = Campaign::with('source')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get()
            ->map(function ($campaign) {
                $totalLeads = $campaign->leads()->count();
                $revenue = $campaign->leads()->where('status', 'successful')->sum('value') ?? 0;
                $roi = $campaign->spent > 0 ? (($revenue - $campaign->spent) / $campaign->spent) * 100 : 0;
                
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'source' => $campaign->source->name,
                    'type' => $campaign->type,
                    'budget' => $campaign->budget,
                    'spent' => $campaign->spent,
                    'leads' => $totalLeads,
                    'revenue' => $revenue,
                    'roi' => $roi,
                    'conversion_rate' => $campaign->conversion_rate,
                ];
            })
            ->sortByDesc('roi')
            ->take(10);
        
        // Monthly trends
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            
            $campaigns = Campaign::whereYear('created_at', $month->year)
                                ->whereMonth('created_at', $month->month)
                                ->get();
            
            $totalSpent = $campaigns->sum('spent');
            $totalLeads = Lead::whereNotNull('campaign_id')
                             ->whereYear('created_at', $month->year)
                             ->whereMonth('created_at', $month->month)
                             ->count();
            $totalRevenue = Lead::whereNotNull('campaign_id')
                               ->where('status', 'successful')
                               ->whereYear('created_at', $month->year)
                               ->whereMonth('created_at', $month->month)
                               ->sum('value') ?? 0;
            
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'campaigns' => $campaigns->count(),
                'spent' => $totalSpent,
                'leads' => $totalLeads,
                'revenue' => $totalRevenue,
                'roi' => $totalSpent > 0 ? (($totalRevenue - $totalSpent) / $totalSpent) * 100 : 0,
            ];
        }
        
        return view('campaigns.analytics', compact(
            'performanceByType',
            'topCampaigns',
            'monthlyTrends',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Private method to get campaign performance data.
     */
    private function getCampaignPerformanceData($campaignId)
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $leads = Lead::where('campaign_id', $campaignId)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->get();
            
            $totalLeads = $leads->count();
            $successfulLeads = $leads->where('status', 'successful')->count();
            $revenue = $leads->where('status', 'successful')->sum('value') ?? 0;
            
            $adSpend = AdSpend::where('campaign_id', $campaignId)
                            ->where('month', $monthKey)
                            ->sum('amount_spent') ?? 0;
            
            $data[] = [
                'month' => $monthKey,
                'total_leads' => $totalLeads,
                'successful_leads' => $successfulLeads,
                'revenue' => $revenue,
                'spent' => $adSpend,
                'conversion_rate' => $totalLeads > 0 ? ($successfulLeads / $totalLeads) * 100 : 0,
                'roi' => $adSpend > 0 ? (($revenue - $adSpend) / $adSpend) * 100 : 0,
            ];
        }
        
        return $data;
    }
}
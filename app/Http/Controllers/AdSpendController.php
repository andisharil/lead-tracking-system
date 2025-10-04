<?php

namespace App\Http\Controllers;

use App\Models\AdSpend;
use App\Models\Source;
use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdSpendController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = AdSpend::with(['source', 'campaign']);
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('source', function($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })->orWhereHas('campaign', function($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Filter by source
            if ($request->filled('source_id')) {
                $query->where('source_id', $request->source_id);
            }
            
            // Filter by campaign
            if ($request->filled('campaign_id')) {
                $query->where('campaign_id', $request->campaign_id);
            }
            
            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('spend_date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('spend_date', '<=', $request->end_date);
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'spend_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $adSpends = $query->paginate(15)->withQueryString();
            
            // Calculate summary metrics
            $totalSpent = AdSpend::sum('amount_spent');
            $totalLeads = Lead::count();
            $totalRevenue = Lead::where('status', 'closed')->sum('value');
            $avgCostPerLead = $totalLeads > 0 ? $totalSpent / $totalLeads : 0;
            $roi = $totalSpent > 0 ? (($totalRevenue - $totalSpent) / $totalSpent) * 100 : 0;
            
            // Get filter options
            $sources = Source::orderBy('name')->get();
            $campaigns = Campaign::orderBy('name')->get();
            
            return view('ad-spend.index', compact(
                'adSpends', 'sources', 'campaigns', 'totalSpent', 'totalLeads', 
                'totalRevenue', 'avgCostPerLead', 'roi'
            ));
        } catch (\Throwable $e) {
            logger()->warning('Ad spend index failed, showing safe defaults', ['error' => $e->getMessage()]);
            session()->flash('error', 'We are currently unable to load ad spend records. Please try again later.');
        
            $adSpends = new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 15, 1, [
                'path' => url()->current(),
                'query' => $request->query(),
            ]);
        
            $totalSpent = 0;
            $totalLeads = 0;
            $totalRevenue = 0;
            $avgCostPerLead = 0;
            $roi = 0;
        
            $sources = collect([]);
            $campaigns = collect([]);
            $sortBy = $request->get('sort_by', 'spend_date');
            $sortOrder = $request->get('sort_order', 'desc');
        
            return view('ad-spend.index', compact(
                'adSpends', 'sources', 'campaigns', 'totalSpent', 'totalLeads',
                'totalRevenue', 'avgCostPerLead', 'roi'
            ));
        }
    }
    
    public function create()
    {
        $sources = Source::orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();
        
        return view('ad-spend.create', compact('sources', 'campaigns'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_id' => 'required|exists:sources,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'amount_spent' => 'required|numeric|min:0',
            'spend_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'platform' => 'nullable|string|max:100',
            'ad_type' => 'nullable|string|max:100',
            'target_audience' => 'nullable|string|max:255',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0'
        ]);
        
        AdSpend::create($validated);
        
        return redirect()->route('ad-spend.index')
            ->with('success', 'Ad spend record created successfully.');
    }
    
    public function show(AdSpend $adSpend)
    {
        $adSpend->load(['source', 'campaign']);
        
        // Get related leads for this spend record
        $relatedLeads = Lead::where('source_id', $adSpend->source_id)
            ->when($adSpend->campaign_id, function($query) use ($adSpend) {
                return $query->where('campaign_id', $adSpend->campaign_id);
            })
            ->whereDate('created_at', $adSpend->spend_date)
            ->with(['location'])
            ->latest()
            ->take(10)
            ->get();
        
        // Calculate performance metrics
        $ctr = ($adSpend->impressions > 0 && $adSpend->clicks > 0) 
            ? ($adSpend->clicks / $adSpend->impressions) * 100 : 0;
        $cpc = ($adSpend->clicks > 0) ? $adSpend->amount_spent / $adSpend->clicks : 0;
        $cpm = ($adSpend->impressions > 0) ? ($adSpend->amount_spent / $adSpend->impressions) * 1000 : 0;
        $conversionRate = ($adSpend->clicks > 0 && $adSpend->conversions > 0) 
            ? ($adSpend->conversions / $adSpend->clicks) * 100 : 0;
        $costPerConversion = ($adSpend->conversions > 0) 
            ? $adSpend->amount_spent / $adSpend->conversions : 0;
        
        return view('ad-spend.show', compact(
            'adSpend', 'relatedLeads', 'ctr', 'cpc', 'cpm', 
            'conversionRate', 'costPerConversion'
        ));
    }
    
    public function edit(AdSpend $adSpend)
    {
        $sources = Source::orderBy('name')->get();
        $campaigns = Campaign::orderBy('name')->get();
        
        return view('ad-spend.edit', compact('adSpend', 'sources', 'campaigns'));
    }
    
    public function update(Request $request, AdSpend $adSpend)
    {
        $validated = $request->validate([
            'source_id' => 'required|exists:sources,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'amount_spent' => 'required|numeric|min:0',
            'spend_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'platform' => 'nullable|string|max:100',
            'ad_type' => 'nullable|string|max:100',
            'target_audience' => 'nullable|string|max:255',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0'
        ]);
        
        $adSpend->update($validated);
        
        return redirect()->route('ad-spend.index')
            ->with('success', 'Ad spend record updated successfully.');
    }
    
    public function destroy(AdSpend $adSpend)
    {
        $adSpend->delete();
        
        return redirect()->route('ad-spend.index')
            ->with('success', 'Ad spend record deleted successfully.');
    }
    
    public function analytics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        
        // Handle export
        if ($request->get('export') === 'csv') {
            return $this->exportAnalytics($dateFrom, $dateTo, $sourceId);
        }
        
        $query = AdSpend::with(['source', 'campaign'])
            ->whereBetween('spend_date', [$dateFrom, $dateTo]);
            
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        $adSpends = $query->get();
        
        // Calculate current period analytics
        $totalSpent = $adSpends->sum('amount_spent');
        $totalImpressions = $adSpends->sum('impressions');
        $totalClicks = $adSpends->sum('clicks');
        $totalConversions = $adSpends->sum('conversions');
        
        // Calculate previous period for comparison
        $daysDiff = \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo));
        $prevDateFrom = \Carbon\Carbon::parse($dateFrom)->subDays($daysDiff + 1)->format('Y-m-d');
        $prevDateTo = \Carbon\Carbon::parse($dateFrom)->subDay()->format('Y-m-d');
        
        $prevQuery = AdSpend::whereBetween('spend_date', [$prevDateFrom, $prevDateTo]);
        if ($sourceId) {
            $prevQuery->where('source_id', $sourceId);
        }
        $prevAdSpends = $prevQuery->get();
        
        $prevTotalSpent = $prevAdSpends->sum('amount_spent');
        $prevTotalImpressions = $prevAdSpends->sum('impressions');
        $prevTotalClicks = $prevAdSpends->sum('clicks');
        $prevTotalConversions = $prevAdSpends->sum('conversions');
        
        // Calculate percentage changes
        $spendChange = $prevTotalSpent > 0 ? (($totalSpent - $prevTotalSpent) / $prevTotalSpent) * 100 : null;
        $impressionsChange = $prevTotalImpressions > 0 ? (($totalImpressions - $prevTotalImpressions) / $prevTotalImpressions) * 100 : null;
        $clicksChange = $prevTotalClicks > 0 ? (($totalClicks - $prevTotalClicks) / $prevTotalClicks) * 100 : null;
        $conversionsChange = $prevTotalConversions > 0 ? (($totalConversions - $prevTotalConversions) / $prevTotalConversions) * 100 : null;
        
        // Calculate ROI based on leads generated
        $leads = Lead::whereBetween('created_at', [$dateFrom, $dateTo])
            ->when($sourceId, function($q) use ($sourceId) {
                return $q->where('source_id', $sourceId);
            })
            ->get();
            
        $totalRevenue = $leads->sum('value');
        $overallRoi = $totalSpent > 0 ? (($totalRevenue - $totalSpent) / $totalSpent) * 100 : 0;
        
        // Performance by source
        $sourcePerformance = AdSpend::with('source')
            ->whereBetween('spend_date', [$dateFrom, $dateTo])
            ->when($sourceId, function($q) use ($sourceId) {
                return $q->where('source_id', $sourceId);
            })
            ->get()
            ->groupBy('source_id')
            ->map(function ($spends) {
                $totalSpent = $spends->sum('amount_spent');
                $totalImpressions = $spends->sum('impressions');
                $totalClicks = $spends->sum('clicks');
                $totalConversions = $spends->sum('conversions');
                
                return [
                    'name' => $spends->first()->source->name ?? 'Unknown',
                    'type' => $spends->first()->source->type ?? 'Unknown',
                    'total_spent' => $totalSpent,
                    'total_impressions' => $totalImpressions,
                    'total_clicks' => $totalClicks,
                    'total_conversions' => $totalConversions,
                    'avg_cpc' => $totalClicks > 0 ? number_format($totalSpent / $totalClicks, 2) : '0.00',
                    'ctr' => $totalImpressions > 0 ? number_format(($totalClicks / $totalImpressions) * 100, 2) : '0.00',
                    'conversion_rate' => $totalClicks > 0 ? number_format(($totalConversions / $totalClicks) * 100, 2) : '0.00',
                ];
            })
            ->values();
        
        // Performance by platform
        $platformPerformance = $adSpends->groupBy('platform')
            ->map(function ($spends, $platform) {
                $totalSpent = $spends->sum('amount_spent');
                $totalImpressions = $spends->sum('impressions');
                $totalClicks = $spends->sum('clicks');
                $totalConversions = $spends->sum('conversions');
                
                return [
                    'platform' => $platform,
                    'total_spent' => $totalSpent,
                    'total_impressions' => $totalImpressions,
                    'total_clicks' => $totalClicks,
                    'total_conversions' => $totalConversions,
                    'avg_cpc' => $totalClicks > 0 ? number_format($totalSpent / $totalClicks, 2) : '0.00',
                    'ctr' => $totalImpressions > 0 ? number_format(($totalClicks / $totalImpressions) * 100, 2) : '0.00',
                    'conversion_rate' => $totalClicks > 0 ? number_format(($totalConversions / $totalClicks) * 100, 2) : '0.00',
                ];
            })
            ->values();
        
        // Chart data for different periods
        $chartData = $this->getChartData($dateFrom, $dateTo, $sourceId);
        
        $analytics = [
            'total_spent' => $totalSpent,
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'total_conversions' => $totalConversions,
            'total_revenue' => $totalRevenue,
            'overall_roi' => $overallRoi,
            'spend_change' => $spendChange,
            'impressions_change' => $impressionsChange,
            'clicks_change' => $clicksChange,
            'conversions_change' => $conversionsChange,
            'avg_cpc' => $totalClicks > 0 ? number_format($totalSpent / $totalClicks, 2) : '0.00',
            'avg_cpa' => $totalConversions > 0 ? number_format($totalSpent / $totalConversions, 2) : '0.00',
            'avg_ctr' => $totalImpressions > 0 ? number_format(($totalClicks / $totalImpressions) * 100, 2) : '0.00',
            'avg_conversion_rate' => $totalClicks > 0 ? number_format(($totalConversions / $totalClicks) * 100, 2) : '0.00',
            'source_performance' => $sourcePerformance,
            'platform_performance' => $platformPerformance,
            'chart_data' => $chartData,
        ];
        
        $sources = Source::all();
        
        return view('ad-spend.analytics', compact('analytics', 'sources'));
    }
    
    private function getChartData($dateFrom, $dateTo, $sourceId = null)
    {
        $query = AdSpend::whereBetween('spend_date', [$dateFrom, $dateTo]);
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        // Daily data
        $dailyData = $query->get()
            ->groupBy(function($item) {
                return $item->spend_date->format('Y-m-d');
            })
            ->map(function($spends) {
                return $spends->sum('amount_spent');
            });
        
        // Weekly data
        $weeklyData = $query->get()
            ->groupBy(function($item) {
                return $item->spend_date->format('Y-W');
            })
            ->map(function($spends) {
                return $spends->sum('amount_spent');
            });
        
        // Monthly data
        $monthlyData = $query->get()
            ->groupBy(function($item) {
                return $item->spend_date->format('Y-m');
            })
            ->map(function($spends) {
                return $spends->sum('amount_spent');
            });
        
        return [
            'daily' => [
                'labels' => $dailyData->keys()->toArray(),
                'spend' => $dailyData->values()->toArray(),
            ],
            'weekly' => [
                'labels' => $weeklyData->keys()->toArray(),
                'spend' => $weeklyData->values()->toArray(),
            ],
            'monthly' => [
                'labels' => $monthlyData->keys()->toArray(),
                'spend' => $monthlyData->values()->toArray(),
            ],
        ];
    }
    
    private function exportAnalytics($dateFrom, $dateTo, $sourceId = null)
    {
        $query = AdSpend::with(['source', 'campaign'])
            ->whereBetween('spend_date', [$dateFrom, $dateTo]);
            
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        $adSpends = $query->get();
        
        $filename = 'ad_spend_analytics_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($adSpends) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date', 'Source', 'Campaign', 'Platform', 'Ad Type',
                'Amount Spent', 'Impressions', 'Clicks', 'Conversions',
                'CTR (%)', 'CPC ($)', 'CPM ($)', 'Conversion Rate (%)'
            ]);
            
            foreach ($adSpends as $spend) {
                $ctr = $spend->impressions > 0 ? ($spend->clicks / $spend->impressions) * 100 : 0;
                $cpc = $spend->clicks > 0 ? $spend->amount_spent / $spend->clicks : 0;
                $cpm = $spend->impressions > 0 ? ($spend->amount_spent / $spend->impressions) * 1000 : 0;
                $conversionRate = $spend->clicks > 0 ? ($spend->conversions / $spend->clicks) * 100 : 0;
                
                fputcsv($file, [
                    $spend->spend_date->format('Y-m-d'),
                    $spend->source->name ?? 'N/A',
                    $spend->campaign->name ?? 'N/A',
                    $spend->platform ?? 'N/A',
                    $spend->ad_type ?? 'N/A',
                    number_format($spend->amount_spent, 2),
                    $spend->impressions,
                    $spend->clicks,
                    $spend->conversions,
                    number_format($ctr, 2),
                    number_format($cpc, 2),
                    number_format($cpm, 2),
                    number_format($conversionRate, 2),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function getSpendData(Request $request)
    {
        $type = $request->get('type', 'daily');
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        switch ($type) {
            case 'daily':
                $data = AdSpend::select(
                        DB::raw('DATE(spend_date) as period'),
                        DB::raw('SUM(amount_spent) as total_spent')
                    )
                    ->whereBetween('spend_date', [$startDate, $endDate])
                    ->groupBy(DB::raw('DATE(spend_date)'))
                    ->orderBy('period')
                    ->get();
                break;
                
            case 'weekly':
                $data = AdSpend::select(
                        DB::raw('YEARWEEK(spend_date) as period'),
                        DB::raw('SUM(amount_spent) as total_spent')
                    )
                    ->whereBetween('spend_date', [$startDate, $endDate])
                    ->groupBy(DB::raw('YEARWEEK(spend_date)'))
                    ->orderBy('period')
                    ->get();
                break;
                
            case 'monthly':
                $data = AdSpend::select(
                        DB::raw('DATE_FORMAT(spend_date, "%Y-%m") as period'),
                        DB::raw('SUM(amount_spent) as total_spent')
                    )
                    ->whereBetween('spend_date', [$startDate, $endDate])
                    ->groupBy(DB::raw('DATE_FORMAT(spend_date, "%Y-%m")'))
                    ->orderBy('period')
                    ->get();
                break;
                
            default:
                $data = collect();
        }
        
        return response()->json($data);
    }
}
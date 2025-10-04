<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Lead;
use App\Models\Source;
use App\Models\Campaign;
use Carbon\Carbon;
use PDF;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());
        $sourceId = $request->get('source_id');
        $campaignId = $request->get('campaign_id');
        $metric = $request->get('metric', 'leads');
        $status = $this->normalizeStatusFilter($request->get('status'));
        $chartType = $request->get('chart_type', 'line');

        try {
            if ($request->get('export') === 'pdf') {
                return $this->exportPdf($dateFrom, $dateTo, $sourceId, $campaignId, $status, $metric, $chartType);
            } elseif ($request->get('export') === 'csv') {
                return $this->exportCSV($dateFrom, $dateTo, $sourceId, $campaignId, $status, $metric);
            }

            $overview = $this->getOverviewMetrics($dateFrom, $dateTo, $sourceId, $campaignId, $status);
            $chartData = $this->getChartData($dateFrom, $dateTo, $sourceId, $campaignId, $status, $metric, $chartType);
            $conversionMetrics = $this->getConversionMetrics($dateFrom, $dateTo, $sourceId, $campaignId, $status);
            $sourcePerformance = $this->getSourcePerformance($dateFrom, $dateTo, $status);
            $campaignPerformance = $this->getCampaignPerformance($dateFrom, $dateTo, $sourceId, $status);
            $revenueAnalytics = $this->getRevenueAnalytics($dateFrom, $dateTo, $sourceId, $campaignId, $status);
            $trendAnalysis = $this->getTrendAnalysis($dateFrom, $dateTo, $sourceId, $campaignId, $status);

            $sources = Source::select('id', 'name')->orderBy('name')->get();
            $campaigns = Campaign::select('id', 'name')->orderBy('name')->get();

            return view('reports.index', compact(
                'dateFrom', 'dateTo', 'sourceId', 'campaignId', 'metric', 'status',
                'sources', 'campaigns', 'overview', 'chartType', 'chartData', 'conversionMetrics',
                'sourcePerformance', 'campaignPerformance', 'revenueAnalytics', 'trendAnalysis'
            ));
        } catch (\Throwable $e) {
            logger()->warning('Reports index failed, showing safe defaults', ['error' => $e->getMessage()]);
            session()->flash('error', 'We are currently unable to load reports. Please try again later.');

            $sources = collect([]);
            $campaigns = collect([]);

            $overview = [
                'total_leads' => 0,
                'leads_change' => 0,
                'total_revenue' => 0,
                'revenue_change' => 0,
                'conversion_rate' => 0,
                'conversion_change' => 0,
                'roi' => 0,
                'cost_per_lead' => 0,
            ];

            $chartData = [
                'labels' => [],
                'datasets' => [],
            ];

            $conversionMetrics = [
                'successful_leads' => 0,
                'lost_leads' => 0,
                'pending_leads' => 0,
                'avg_conversion_time' => 0,
                'stage_dropoffs' => [],
            ];

            $sourcePerformance = [];
            $campaignPerformance = [];

            $revenueAnalytics = [
                'total_revenue' => 0,
                'avg_deal_size' => 0,
                'largest_deal' => 0,
            ];

            $trendAnalysis = [
                'current' => [
                    'leads' => 0,
                    'revenue' => 0,
                    'conversion_rate' => 0,
                    'avg_deal_size' => 0,
                ],
                'previous' => [
                    'leads' => 0,
                    'revenue' => 0,
                    'conversion_rate' => 0,
                    'avg_deal_size' => 0,
                ],
                'trend' => [
                    'leads' => 0,
                    'revenue' => 0,
                    'conversion_rate' => 0,
                    'avg_deal_size' => 0,
                ],
            ];

            return view('reports.index', compact(
                'dateFrom', 'dateTo', 'sourceId', 'campaignId', 'metric', 'status',
                'sources', 'campaigns', 'overview', 'chartType', 'chartData', 'conversionMetrics',
                'sourcePerformance', 'campaignPerformance', 'revenueAnalytics', 'trendAnalysis'
            ));
        }
    }

    private function normalizeStatusFilter($status)
    {
        if (!$status || $status === 'all') return null;
        $valid = ['successful', 'lost', 'pending'];
        return in_array($status, $valid) ? $status : null;
    }

    private function getOverviewMetrics($dateFrom, $dateTo, $sourceId, $campaignId, $status)
    {
        // ... existing code ...
    }

    private function getChartData($dateFrom, $dateTo, $sourceId = null, $campaignId = null, $metric = 'leads', $status = null)
    {
        $statusDb = $status;
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        if ($statusDb) {
            $query->where('status', $statusDb);
        }
        
        $leads = $query->get();
        
        // Group by date
        $dailyData = $leads->groupBy(function($lead) {
            return $lead->created_at->format('Y-m-d');
        });
        
        $labels = [];
        $data = [];
        
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);
        
        while ($startDate <= $endDate) {
            $dateKey = $startDate->format('Y-m-d');
            $labels[] = $startDate->format('M d');
            
            $dayLeads = $dailyData->get($dateKey, collect());
            
            switch ($metric) {
                case 'leads':
                    $data[] = $dayLeads->count();
                    break;
                case 'revenue':
                    $data[] = $dayLeads->sum('value');
                    break;
                case 'conversions':
                    $data[] = $dayLeads->where('status', 'successful')->count();
                    break;
                case 'conversion_rate':
                    $totalLeads = $dayLeads->count();
                    $closedLeads = $dayLeads->where('status', 'successful')->count();
                    $data[] = $totalLeads > 0 ? ($closedLeads / $totalLeads) * 100 : 0;
                    break;
                default:
                    $data[] = $dayLeads->count();
            }
            
            $startDate->addDay();
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'metric' => $metric,
        ];
    }
    
    private function getConversionMetrics($dateFrom, $dateTo, $sourceId = null, $campaignId = null, $status = null)
    {
        $statusDb = $status;
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        if ($statusDb) {
            $query->where('status', $statusDb);
        }
        
        $leads = $query->get();
        
        $statusCounts = $leads->groupBy('status')->map->count();
        
        return [
            'new' => $statusCounts->get('new', 0),
            'contacted' => $statusCounts->get('contacted', 0),
            'qualified' => $statusCounts->get('qualified', 0),
            'proposal' => $statusCounts->get('proposal', 0),
            'negotiation' => $statusCounts->get('negotiation', 0),
            'closed' => $statusCounts->get('successful', 0),
            'successful' => $statusCounts->get('successful', 0),
            'lost' => $statusCounts->get('lost', 0),
        ];
    }
    
    private function getSourcePerformance($dateFrom, $dateTo)
    {
        return Source::select('sources.*')
            ->selectRaw('COUNT(leads.id) as total_leads')
            ->selectRaw("SUM(CASE WHEN leads.status = 'successful' THEN 1 ELSE 0 END) as closed_leads")
            ->selectRaw('SUM(leads.value) as total_revenue')
            ->selectRaw('COALESCE(SUM(ad_spend.amount_spent), 0) as total_spent')
            ->leftJoin('leads', function($join) use ($dateFrom, $dateTo) {
                $join->on('sources.id', '=', 'leads.source_id')
                     ->whereBetween('leads.created_at', [$dateFrom, $dateTo]);
            })
            ->leftJoin('ad_spend', function($join) use ($dateFrom, $dateTo) {
                $join->on('sources.id', '=', 'ad_spend.source_id')
                     ->whereBetween('ad_spend.spend_date', [$dateFrom, $dateTo]);
            })
            ->groupBy('sources.id')
            ->get()
            ->map(function($source) {
                $conversionRate = $source->total_leads > 0 ? ($source->closed_leads / $source->total_leads) * 100 : 0;
                $roi = $source->total_spent > 0 ? (($source->total_revenue - $source->total_spent) / $source->total_spent) * 100 : 0;
                $costPerLead = $source->total_leads > 0 ? $source->total_spent / $source->total_leads : 0;
                
                return [
                    'name' => $source->name,
                    'type' => $source->type,
                    'total_leads' => $source->total_leads,
                    'closed_leads' => $source->closed_leads,
                    'total_revenue' => $source->total_revenue,
                    'total_spent' => $source->total_spent,
                    'conversion_rate' => $conversionRate,
                    'roi' => $roi,
                    'cost_per_lead' => $costPerLead,
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);
    }
    
    private function getCampaignPerformance($dateFrom, $dateTo)
    {
        return Campaign::select('campaigns.id', 'campaigns.name', 'campaigns.status', 'campaigns.type', 'campaigns.budget')
            ->selectRaw('COUNT(leads.id) as total_leads')
            ->selectRaw("SUM(CASE WHEN leads.status = 'successful' THEN 1 ELSE 0 END) as closed_leads")
            ->selectRaw('SUM(leads.value) as total_revenue')
            ->selectRaw('COALESCE(SUM(ad_spend.amount_spent), 0) as total_spent')
            ->leftJoin('leads', function($join) use ($dateFrom, $dateTo) {
                $join->on('campaigns.id', '=', 'leads.campaign_id')
                     ->whereBetween('leads.created_at', [$dateFrom, $dateTo]);
            })
            ->leftJoin('ad_spend', function($join) use ($dateFrom, $dateTo) {
                $join->on('campaigns.id', '=', 'ad_spend.campaign_id')
                     ->whereBetween('ad_spend.spend_date', [$dateFrom, $dateTo]);
            })
            ->groupBy('campaigns.id')
            ->get()
            ->map(function($campaign) {
                $conversionRate = $campaign->total_leads > 0 ? ($campaign->closed_leads / $campaign->total_leads) * 100 : 0;
                $roi = $campaign->total_spent > 0 ? (($campaign->total_revenue - $campaign->total_spent) / $campaign->total_spent) * 100 : 0;
                $budgetUtilization = $campaign->budget > 0 ? ($campaign->total_spent / $campaign->budget) * 100 : 0;
                
                return [
                    'name' => $campaign->name,
                    'status' => $campaign->status,
                    'type' => $campaign->type,
                    'budget' => $campaign->budget,
                    'total_leads' => $campaign->total_leads,
                    'closed_leads' => $campaign->closed_leads,
                    'total_revenue' => $campaign->total_revenue,
                    'total_spent' => $campaign->total_spent,
                    'conversion_rate' => $conversionRate,
                    'roi' => $roi,
                    'budget_utilization' => $budgetUtilization,
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);
    }
    
    private function getRevenueAnalytics($dateFrom, $dateTo, $sourceId = null, $campaignId = null)
    {
        $query = Lead::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('status', 'successful');
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        
        $closedLeads = $query->get();
        
        // Monthly revenue trend
        $monthlyRevenue = $closedLeads->groupBy(function($lead) {
            return $lead->created_at->format('Y-m');
        })->map(function($leads) {
            return [
                'revenue' => $leads->sum('value'),
                'count' => $leads->count(),
                'avg_value' => $leads->avg('value'),
            ];
        });
        
        // Revenue by source
        $revenueBySource = $closedLeads->load('source')->groupBy('source.name')
            ->map(function($leads) {
                return $leads->sum('value');
            })
            ->sortDesc()
            ->take(5);
        
        return [
            'monthly_revenue' => $monthlyRevenue,
            'revenue_by_source' => $revenueBySource,
            'total_revenue' => $closedLeads->sum('value'),
            'avg_deal_size' => $closedLeads->avg('value'),
            'largest_deal' => $closedLeads->max('value'),
        ];
    }
    
    private function getTrendAnalysis($dateFrom, $dateTo, $sourceId = null, $campaignId = null, $status = null)
    {
        // Calculate current period
        $statusDb = $status;
        $currentQuery = Lead::whereBetween('created_at', [$dateFrom, $dateTo]);
        if ($sourceId) $currentQuery->where('source_id', $sourceId);
        if ($campaignId) $currentQuery->where('campaign_id', $campaignId);
        if ($statusDb) $currentQuery->where('status', $statusDb);
        $currentLeads = $currentQuery->get();
        
        // Calculate previous period
        $daysDiff = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo));
        $prevDateFrom = Carbon::parse($dateFrom)->subDays($daysDiff + 1)->format('Y-m-d');
        $prevDateTo = Carbon::parse($dateFrom)->subDay()->format('Y-m-d');
        
        $prevQuery = Lead::whereBetween('created_at', [$prevDateFrom, $prevDateTo]);
        if ($sourceId) $prevQuery->where('source_id', $sourceId);
        if ($campaignId) $prevQuery->where('campaign_id', $campaignId);
        if ($statusDb) $prevQuery->where('status', $statusDb);
        $prevLeads = $prevQuery->get();
        
        $currentMetrics = [
            'leads' => $currentLeads->count(),
            'revenue' => $currentLeads->sum('value'),
            'conversions' => $currentLeads->where('status', 'successful')->count(),
            'avg_value' => $currentLeads->avg('value') ?: 0,
        ];
        
        $prevMetrics = [
            'leads' => $prevLeads->count(),
            'revenue' => $prevLeads->sum('value'),
            'conversions' => $prevLeads->where('status', 'successful')->count(),
            'avg_value' => $prevLeads->avg('value') ?: 0,
        ];
        
        $trends = [];
        foreach ($currentMetrics as $key => $current) {
            $previous = $prevMetrics[$key];
            $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : null;
            $trends[$key] = [
                'current' => $current,
                'previous' => $previous,
                'change' => $change,
                'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            ];
        }
        
        return $trends;
    }
    
    private function exportReport($format, $dateFrom, $dateTo, $sourceId = null, $campaignId = null, $status = null)
    {
        $statusDb = $status;
        $query = Lead::with(['source', 'campaign'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }
        if ($statusDb) {
            $query->where('status', $statusDb);
        }
        
        $leads = $query->get();
        
        if ($format === 'csv') {
            return $this->exportCSV($leads, $dateFrom, $dateTo);
        } elseif ($format === 'pdf') {
            return redirect()->route('reports.export-pdf', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'source_id' => $sourceId,
                'campaign_id' => $campaignId,
                'status' => $status,
            ]);
        }
        
        return redirect()->back()->with('error', 'Invalid export format');
    }
    
    private function exportCSV($leads, $dateFrom, $dateTo)
    {
        $filename = 'leads_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date', 'Name', 'Email', 'Phone', 'Source', 'Campaign',
                'Status', 'Value', 'Location', 'Notes'
            ]);
            
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->created_at->format('Y-m-d H:i:s'),
                    $lead->name,
                    $lead->email,
                    $lead->phone,
                    $lead->source->name ?? 'N/A',
                    $lead->campaign->name ?? 'N/A',
                    $lead->status,
                    number_format($lead->value, 2),
                    $lead->location,
                    $lead->notes,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportPdfLegacy($leads, $dateFrom, $dateTo)
    {
        // Redirect to the dedicated export route with the current date range.
        // Note: Source/Campaign filters aren’t available in this legacy helper signature.
        return redirect()->route('reports.export-pdf', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $sourceId = $request->get('source_id');
        $campaignId = $request->get('campaign_id');
        $chartType = $request->get('chart_type', 'line');
        $metric = $request->get('metric', 'leads');
        $status = $request->get('status');
        $statusDb = $status;

        $overview = $this->getOverviewMetrics($dateFrom, $dateTo, $sourceId, $campaignId, $status);
        $chartData = $this->getChartData($dateFrom, $dateTo, $sourceId, $campaignId, $metric, $status);
        $conversionMetrics = $this->getConversionMetrics($dateFrom, $dateTo, $sourceId, $campaignId, $status);
        $sourcePerformance = $this->getSourcePerformance($dateFrom, $dateTo);
        $campaignPerformance = $this->getCampaignPerformance($dateFrom, $dateTo);

        $leads = Lead::with(['source', 'campaign'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->when($sourceId, fn($q) => $q->where('source_id', $sourceId))
            ->when($campaignId, fn($q) => $q->where('campaign_id', $campaignId))
            ->when($statusDb, fn($q) => $q->where('status', $statusDb))
            ->orderBy('created_at', 'desc')
            ->get();
        
        $selectedSourceName = $sourceId ? optional(Source::find($sourceId))->name : 'All Sources';
        $selectedCampaignName = $campaignId ? optional(Campaign::find($campaignId))->name : 'All Campaigns';
        $selectedStatusName = $statusDb ? ($statusDb === 'successful' ? 'Successful' : ucfirst($statusDb)) : 'All Status';

        return view('reports.pdf', compact(
            'overview', 'chartData', 'conversionMetrics', 'sourcePerformance',
            'campaignPerformance', 'leads', 'dateFrom', 'dateTo', 'sourceId',
            'campaignId', 'chartType', 'metric', 'selectedSourceName', 'selectedCampaignName', 'status', 'selectedStatusName'
        ));
    }
}
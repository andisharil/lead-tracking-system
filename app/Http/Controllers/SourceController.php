<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SourceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $sources = Source::select(
            'sources.id',
            'sources.name',
            'sources.description',
            'sources.type',
            'sources.status',
            'sources.created_at',
            DB::raw('COUNT(leads.id) as leads_count'),
            DB::raw('SUM(CASE WHEN leads.status = \'successful\' THEN 1 ELSE 0 END) as successful_leads'),
            DB::raw('SUM(CASE WHEN leads.status = \'successful\' THEN leads.value ELSE 0 END) as total_value'),
            DB::raw('ROUND((SUM(CASE WHEN leads.status = \'successful\' THEN 1 ELSE 0 END) / NULLIF(COUNT(leads.id), 0)) * 100, 2) as conversion_rate'),
            DB::raw('MAX(leads.created_at) as last_lead_date')
        )
        ->leftJoin('leads', 'sources.id', '=', 'leads.source_id')
        ->when($search, function($query) use ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('sources.name', 'like', "%{$search}%")
                  ->orWhere('sources.description', 'like', "%{$search}%")
                  ->orWhere('sources.contact_person', 'like', "%{$search}%");
            });
        })
        ->when($status, function($query) use ($status) {
            return $query->where('sources.status', $status);
        })
        ->groupBy('sources.id', 'sources.name', 'sources.description', 'sources.type', 'sources.status', 'sources.created_at')
        ->orderBy($sortBy === 'conversion_rate' ? 'conversion_rate' : "sources.{$sortBy}", $sortOrder)
        ->paginate(15);
        
        // Calculate performance metrics for header cards
        $totalSources = Source::count();
        $activeSources = Source::where('status', 'active')->count();
        $totalLeads = Lead::count();
        $successfulLeadsAll = Lead::where('status', 'successful')->count();
        $avgConversionRate = $totalLeads > 0 ? round(($successfulLeadsAll / $totalLeads) * 100, 2) : 0;
        
        return view('sources.index', compact(
            'sources',
            'totalSources',
            'activeSources',
            'totalLeads',
            'avgConversionRate',
            'search',
            'status',
            'sortBy',
            'sortOrder'
        ));
    }
    
    public function create()
    {
        return view('sources.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sources,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,paused',
            'cost_per_lead' => 'nullable|numeric|min:0|max:9999.99',
            'monthly_budget' => 'nullable|numeric|min:0|max:99999999.99',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'configuration' => 'nullable|array'
        ]);
        
        if ($validated['status'] === 'active') {
            $validated['last_active_at'] = now();
        }
        
        Source::create($validated);
        
        return redirect()->route('sources.index')
                        ->with('success', 'Source created successfully!');
    }
    
    public function show(Source $source)
    {
        // Get performance data for the last 12 months
        $performanceData = Lead::select(
            DB::raw('TO_CHAR(created_at, \'YYYY-MM\') as month'),
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('SUM(CASE WHEN status = \'successful\' THEN 1 ELSE 0 END) as successful_leads'),
            DB::raw('SUM(CASE WHEN status = \'successful\' THEN value ELSE 0 END) as revenue')
        )
        ->where('source_id', $source->id)
        ->whereBetween('created_at', [Carbon::now()->subMonths(11), Carbon::now()])
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Get recent leads
        $recentLeads = Lead::where('source_id', $source->id)
                          ->with('location')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
        
        // Calculate key metrics
        $totalLeads = Lead::where('source_id', $source->id)->count();
        $successfulLeads = Lead::where('source_id', $source->id)->where('status', 'successful')->count();
        $totalRevenue = Lead::where('source_id', $source->id)->where('status', 'successful')->sum('value') ?? 0;
        $conversionRate = $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0;
        $averageDealSize = $successfulLeads > 0 ? round($totalRevenue / $successfulLeads, 2) : 0;
        
        // Calculate ROI if cost data is available
        $estimatedCost = $source->cost_per_lead ? $source->cost_per_lead * $totalLeads : 0;
        $roi = $estimatedCost > 0 ? round((($totalRevenue - $estimatedCost) / $estimatedCost) * 100, 2) : 0;
        
        return view('sources.show', compact(
            'source',
            'performanceData',
            'recentLeads',
            'totalLeads',
            'successfulLeads',
            'totalRevenue',
            'conversionRate',
            'averageDealSize',
            'estimatedCost',
            'roi'
        ));
    }
    
    public function edit(Source $source)
    {
        return view('sources.edit', compact('source'));
    }
    
    public function update(Request $request, Source $source)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('sources')->ignore($source->id)],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,paused',
            'cost_per_lead' => 'nullable|numeric|min:0|max:9999.99',
            'monthly_budget' => 'nullable|numeric|min:0|max:99999999.99',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'configuration' => 'nullable|array'
        ]);
        
        // Update last_active_at if status changed to active
        if ($validated['status'] === 'active' && $source->status !== 'active') {
            $validated['last_active_at'] = now();
        }
        
        $source->update($validated);
        
        return redirect()->route('sources.index')
                        ->with('success', 'Source updated successfully!');
    }
    
    public function destroy(Source $source)
    {
        // Check if source has associated leads
        $leadsCount = $source->leads()->count();
        
        if ($leadsCount > 0) {
            return redirect()->route('sources.index')
                           ->with('error', "Cannot delete source '{$source->name}' because it has {$leadsCount} associated leads.");
        }
        
        $source->delete();
        
        return redirect()->route('sources.index')
                        ->with('success', 'Source deleted successfully!');
    }
    
    public function toggleStatus(Source $source)
    {
        $newStatus = $source->status === 'active' ? 'inactive' : 'active';
        
        $source->update([
            'status' => $newStatus,
            'last_active_at' => $newStatus === 'active' ? now() : $source->last_active_at
        ]);
        
        return redirect()->back()
                        ->with('success', "Source status updated to {$newStatus}!");
    }
    
    public function getPerformanceData(Source $source, Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);
        
        $data = Lead::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful_leads')
        )
        ->where('source_id', $source->id)
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return response()->json($data);
    }
}
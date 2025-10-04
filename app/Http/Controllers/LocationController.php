<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        try {
        $locations = Location::select(
            'locations.id',
            'locations.name',
            'locations.city',
            'locations.state',
            'locations.country',
            'locations.postal_code',
            'locations.status',
            'locations.created_at',
            DB::raw('COUNT(leads.id) as leads_count'),
            DB::raw('SUM(CASE WHEN leads.status = \'successful\' THEN 1 ELSE 0 END) as successful_leads'),
            DB::raw('SUM(CASE WHEN leads.status = \'successful\' THEN leads.value ELSE 0 END) as total_value'),
            DB::raw('ROUND((SUM(CASE WHEN leads.status = \'successful\' THEN 1 ELSE 0 END) / NULLIF(COUNT(leads.id), 0)) * 100, 2) as conversion_rate'),
            DB::raw('MAX(leads.created_at) as last_lead_date')
        )
        ->leftJoin('leads', 'locations.id', '=', 'leads.location_id')
        ->when($search, function($query) use ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('locations.name', 'like', "%{$search}%")
                  ->orWhere('locations.city', 'like', "%{$search}%")
                  ->orWhere('locations.state', 'like', "%{$search}%")
                  ->orWhere('locations.country', 'like', "%{$search}%");
            });
        })
        ->when($status, function($query) use ($status) {
            return $query->where('locations.status', $status);
        })
        ->groupBy('locations.id', 'locations.name', 'locations.city', 'locations.state', 'locations.country', 'locations.postal_code', 'locations.status', 'locations.created_at')
        ->orderBy($sortBy, $sortOrder)
        ->paginate(15)
        ->withQueryString();
        
        // Header cards
        $totalLocations = Location::count();
        $activeLocations = Location::where('status', 'active')->count();
        $totalLeads = Lead::count();
        $avgConversionRate = Lead::select(DB::raw("ROUND((SUM(CASE WHEN status = 'successful' THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2) as avg_conversion"))->value('avg_conversion');
        
        return view('locations.index', compact(
            'locations', 'totalLocations', 'activeLocations', 'totalLeads', 'avgConversionRate',
            'search', 'status', 'sortBy', 'sortOrder'
        ));
        } catch (\Throwable $e) {
            logger()->warning('Locations index failed, showing safe defaults', ['error' => $e->getMessage()]);
            session()->flash('error', 'We are currently unable to load locations. Please try again later.');
            
            $locations = new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 15, 1, [
                'path' => url()->current(),
                'query' => $request->query(),
            ]);
            $totalLocations = 0;
            $activeLocations = 0;
            $totalLeads = 0;
            $avgConversionRate = 0;
            
            return view('locations.index', compact(
                'locations', 'totalLocations', 'activeLocations', 'totalLeads', 'avgConversionRate',
                'search', 'status', 'sortBy', 'sortOrder'
            ));
        }
    }
    
    public function create()
    {
        return view('locations.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);
        
        Location::create($validated);
        
        return redirect()->route('locations.index')
                        ->with('success', 'Location created successfully!');
    }
    
    public function show(Location $location)
    {
        // Get performance data for the last 12 months
        $performanceData = Lead::select(
            DB::raw('TO_CHAR(created_at, \'YYYY-MM\') as month'),
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('SUM(CASE WHEN status = \'successful\' THEN 1 ELSE 0 END) as successful_leads'),
            DB::raw('SUM(CASE WHEN status = \'successful\' THEN value ELSE 0 END) as revenue')
        )
        ->where('location_id', $location->id)
        ->whereBetween('created_at', [Carbon::now()->subMonths(11), Carbon::now()])
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Get recent leads
        $recentLeads = Lead::where('location_id', $location->id)
                          ->with('source')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
        
        // Calculate key metrics
        $totalLeads = Lead::where('location_id', $location->id)->count();
        $successfulLeads = Lead::where('location_id', $location->id)->where('status', 'successful')->count();
        $totalRevenue = Lead::where('location_id', $location->id)->where('status', 'successful')->sum('value') ?? 0;
        $conversionRate = $totalLeads > 0 ? round(($successfulLeads / $totalLeads) * 100, 2) : 0;
        $averageDealSize = $successfulLeads > 0 ? round($totalRevenue / $successfulLeads, 2) : 0;
        
        return view('locations.show', compact(
            'location',
            'performanceData',
            'recentLeads',
            'totalLeads',
            'successfulLeads',
            'totalRevenue',
            'conversionRate',
            'averageDealSize'
        ));
    }
    
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }
    
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('locations')->ignore($location->id)],
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);
        
        $location->update($validated);
        
        return redirect()->route('locations.index')
                        ->with('success', 'Location updated successfully!');
    }
    
    public function destroy(Location $location)
    {
        // Check if location has associated leads
        $leadsCount = $location->leads()->count();
        
        if ($leadsCount > 0) {
            return redirect()->route('locations.index')
                           ->with('error', "Cannot delete location '{$location->name}' because it has {$leadsCount} associated leads.");
        }
        
        $location->delete();
        
        return redirect()->route('locations.index')
                        ->with('success', 'Location deleted successfully!');
    }
    
    public function toggleStatus(Location $location)
    {
        $newStatus = $location->status === 'active' ? 'inactive' : 'active';
        
        $location->update([
            'status' => $newStatus
        ]);
        
        return redirect()->back()
                        ->with('success', "Location status updated to {$newStatus}!");
    }
    
    public function getPerformanceData(Location $location, Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);
        
        $data = Lead::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful_leads')
        )
        ->where('location_id', $location->id)
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return response()->json($data);
    }
}
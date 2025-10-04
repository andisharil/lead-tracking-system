<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Location;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    public function index(Request $request): View
    {
        try {
            $query = Lead::with(['location', 'source']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filter by source
            if ($request->filled('source_id')) {
                $query->where('source_id', $request->get('source_id'));
            }

            // Filter by location
            if ($request->filled('location_id')) {
                $query->where('location_id', $request->get('location_id'));
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->get('date_to'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $leads = $query->paginate(15)->withQueryString();
            
            // Get filter options
            $sources = Source::orderBy('name')->get();
            $locations = Location::orderBy('name')->get();

            return view('leads.index', compact('leads', 'sources', 'locations'));
        } catch (\Throwable $e) {
            logger()->warning('Leads index failed, falling back to empty state', ['error' => $e->getMessage()]);
            session()->flash('error', 'We are currently unable to fetch leads data. Please try again later.');

            $leads = new LengthAwarePaginator(collect([]), 0, 15, 1, [
                'path' => url()->current(),
                'query' => $request->query(),
            ]);
            $sources = collect([]);
            $locations = collect([]);

            return view('leads.index', compact('leads', 'sources', 'locations'));
        }
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create(): View
    {
        try {
            $sources = Source::orderBy('name')->get();
            $locations = Location::orderBy('name')->get();
            
            return view('leads.create', compact('sources', 'locations'));
        } catch (\Throwable $e) {
            logger()->warning('Leads create failed due to data source issues', ['error' => $e->getMessage()]);
            return redirect()->route('leads.index')
                ->with('error', 'The database is temporarily unavailable. Please try again later.');
        }
    }

    /**
     * Store a newly created lead in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'source_id' => 'required|exists:sources,id',
            'status' => 'required|in:new,successful,lost',
            'notes' => 'nullable|string'
        ]);

        $lead = Lead::create($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead): View
    {
        $lead->load(['location', 'source']);
        
        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead): View
    {
        try {
            $sources = Source::orderBy('name')->get();
            $locations = Location::orderBy('name')->get();
            
            return view('leads.edit', compact('lead', 'sources', 'locations'));
        } catch (\Throwable $e) {
            logger()->warning('Leads edit failed due to data source issues', ['error' => $e->getMessage()]);
            return redirect()->route('leads.index')
                ->with('error', 'The database is temporarily unavailable. Please try again later.');
        }
    }

    /**
     * Update the specified lead in storage.
     */
    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'source_id' => 'required|exists:sources,id',
            'status' => 'required|in:new,successful,lost',
            'notes' => 'nullable|string'
        ]);

        // Set closed_at timestamp if status is successful or lost
        if (in_array($validated['status'], ['successful', 'lost']) && $lead->status === 'new') {
            $validated['closed_at'] = now();
        } elseif ($validated['status'] === 'new') {
            $validated['closed_at'] = null;
        }

        $lead->update($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified lead from storage.
     */
    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
}
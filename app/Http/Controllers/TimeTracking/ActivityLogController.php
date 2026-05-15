<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityLogRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Calculate end_offset_seconds if duration_seconds is provided
        if (isset($validated['duration_seconds']) && ! isset($validated['end_offset_seconds'])) {
            $validated['end_offset_seconds'] = ($validated['start_offset_seconds'] ?? 0) + $validated['duration_seconds'];
        }

        // Calculate duration_seconds if end_offset_seconds is provided
        if (isset($validated['end_offset_seconds']) && ! isset($validated['duration_seconds'])) {
            $validated['duration_seconds'] = $validated['end_offset_seconds'] - ($validated['start_offset_seconds'] ?? 0);
        }

        ActivityLog::create($validated);

        return back()->with('success', 'Activity added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityLog $activityLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityLogRequest $request, ActivityLog $activityLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityLog $activityLog)
    {
        //
    }
}

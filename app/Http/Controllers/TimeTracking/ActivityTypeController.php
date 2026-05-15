<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityTypeRequest;
use App\Http\Requests\UpdateActivityTypeRequest;
use App\Http\Resources\ActivityTypeResource;
use App\Models\ActivityType;
use Inertia\Inertia;

class ActivityTypeController extends HybridController
{
    /**
     * Display a listing of the resource.
     */
    public function index(?string $account = null)
    {
        $activityTypes = ActivityType::all();

        return Inertia::render('ActivityType/Index', [
            'activityTypes' => ActivityTypeResource::collection($activityTypes),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(?string $account = null)
    {
        return Inertia::render('ActivityType/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityTypeRequest $request, ?string $account = null)
    {
        $validated = $request->validated();
        $count = count($validated['name']);

        for ($i = 0; $i < $count; $i++) {
            ActivityType::create([
                'name' => $validated['name'][$i],
                'description' => $validated['description'][$i] ?? null,
                'color' => $validated['color'][$i] ?? null,
                'icon' => $validated['icon'][$i] ?? null,
            ]);
        }

        $message = $count === 1 ? 'Activity type created.' : "{$count} activity types created.";

        if ($this->shouldRedirectBack()) {
            return back()->with('success', $message);
        }

        return redirect()->route('activity-type.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(?string $account, ActivityType $activityType)
    {
        return Inertia::render('ActivityType/Show', [
            'activityType' => new ActivityTypeResource($activityType),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(?string $account, ActivityType $activityType)
    {
        return Inertia::render('ActivityType/Edit', [
            'activityType' => new ActivityTypeResource($activityType),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityTypeRequest $request, ?string $account, ActivityType $activityType)
    {
        $validated = $request->validated();
        $activityType->update($validated);

        return $this->renderDetached('ActivityType/Index', [
            'activityTypes' => ActivityTypeResource::collection(ActivityType::all()),
        ])->flash('success', 'Activity type updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(?string $account, ActivityType $activityType)
    {
        try {
            $activityType->delete();

            return redirect()->route('activity-type.index')
                ->flash('success', 'Activity type deleted.');
        } catch (\Exception $e) {
            return redirect()->route('activity-type.index')
                ->flash('error', 'Activity type cannot be deleted.');
        }
    }
}

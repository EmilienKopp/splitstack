<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonitoredActivityRequest;
use App\Http\Requests\UpdateMonitoredActivityRequest;
use App\Models\MonitoredActivity;

class MonitoredActivityController extends HybridController
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
    public function store(StoreMonitoredActivityRequest $request)
    {
        $validated = $request->validated();

        // reduce into a shape that can group by DailyLog
        $keyed = collect($validated['activities'])->reduce(function ($acc, $curr) use ($request) {
            $date = \Carbon\Carbon::parse($curr['timestamp'])->toDateString();
            $userId = $request->user()->id;
            $projectId = $curr['project_id'];
            $key = "$date-$userId-$projectId";
            if (! isset($acc[$key])) {
                $acc[$key] = [
                    'date' => $date,
                    'user_id' => $userId,
                    'project_id' => $projectId,
                    'activities' => [],
                ];
            }
            $acc[$key]['activities'][] = [
                'process' => $curr['process'],
                'window_title' => $curr['window_title'] ?? null,
                'timestamp' => $curr['timestamp'],
            ];

            return $acc;
        }, []);

        foreach ($keyed as $key => $data) {
            $dailyLog = \App\Models\DailyLog::firstOrCreate([
                'date' => $data['date'],
                'user_id' => $data['user_id'],
                'project_id' => $data['project_id'],
            ]);

            $dailyLog->monitoredActivities()->createMany($data['activities']);
        }

        return response()->json(['message' => 'Monitored activities stored successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(MonitoredActivity $monitoredActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonitoredActivity $monitoredActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMonitoredActivityRequest $request, MonitoredActivity $monitoredActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonitoredActivity $monitoredActivity)
    {
        //
    }
}

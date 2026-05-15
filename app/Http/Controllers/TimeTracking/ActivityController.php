<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\ClockEntry;
use App\Models\DailyLog;
use App\Models\Landlord\Tenant;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Repositories\ActivityLogRepositoryInterface;
use App\Repositories\ClockEntryRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Utils\InertiaHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityController extends HybridController
{
    public function __construct(
        public UserRepositoryInterface $userRepository,
        public ProjectRepositoryInterface $projectRepository,
        public ActivityLogRepositoryInterface $activityLogRepository,
        public ClockEntryRepositoryInterface $clockEntryRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ?string $account = null)
    {
        $taskCategories = TaskCategory::all();
        $projects = Auth::guard('tenant')->user()->involvedProjects;

        $date = $request->query('date') ?? Carbon::today()->format('Y-m-d');

        $dailyLogs = DailyLog::getMonthly($date);

        $activityTypes = ActivityType::all();

        return inertia('Activity/Index', compact('projects', 'dailyLogs', 'taskCategories', 'activityTypes', 'date'));
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
    public function store(StoreActivityRequest $request)
    {
        $this->activityLogRepository->sync($request->validated());

        return $this->renderDetached(
            'Activity/Daily/Show',
            $this->loadDataForShow($request, $request->input('date')))
            ->flash('success', 'Activity logged successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ?Tenant $account = null, $date = null)
    {
        return inertia('Activity/Daily/Show', $this->loadDataForShow($request, $date));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }

    public function deleteEntry(?string $account, ClockEntry $clockEntry)
    {
        try {
            $success = $this->clockEntryRepository->delete($clockEntry->id);

            if (! $success) {
                throw new \Exception('Could not delete the clock entry.');
            }
        } catch (\Exception $e) {
            InertiaHelper::fail('Could not delete the clock entry.');
        }

        return to_route('activities.show', parameters: [
            'date' => Carbon::parse($clockEntry->in)->format('Y-m-d'),
        ])->flash('success', 'Clock entry deleted successfully.');
    }

    private function loadDataForShow(Request $request, $date)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $user = Auth::guard('tenant')->user();
        $projects = $this->projectRepository->findForUser($user);

        $taskCategories = TaskCategory::all()->transform(function ($taskCategory) {
            $taskCategory->name = __($taskCategory->name);

            return $taskCategory;
        });

        $activities = $this->activityLogRepository->findByUserAndDate($user->id, $date);
        $activityTypes = ActivityType::all();

        $tasks = Task::whereIn('project_id', $projects->pluck('id'))->get();
        $dailyLogs = DailyLog::getDaily($date);
        $daysWithEntries = DailyLog::selectRaw('DISTINCT date, count(*) as entry_count')
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->get(['date', 'entry_count'])
            ->map(function ($item) {
                return [
                    ...$item->toArray(),
                    'title' => $item->entry_count.' '.Str::plural('entry', $item->entry_count).' on '.Carbon::parse($item->date)->format('F j, Y'),
                ];
            })
            ->toArray();

        return compact('activities', 'projects', 'dailyLogs', 'taskCategories', 'activityTypes', 'tasks', 'date', 'daysWithEntries');
    }
}

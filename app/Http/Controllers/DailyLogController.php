<?php

namespace App\Http\Controllers;

use App\Application\TimeTracking\Actions\ClockIn;
use App\Application\TimeTracking\Actions\ClockOut;
use App\Application\TimeTracking\Actions\Punch;
use App\Application\TimeTracking\DTOs\ClockInDTO;
use App\Application\TimeTracking\DTOs\ClockOutDTO;
use App\Domain\TimeTracking\Entities\ClockEntryEntity;
use App\Domain\TimeTracking\Entities\DailyLogEntity;
use App\Facades\Split;
use App\Http\Requests\TimeTracking\ClockEntryStoreRequest;
use Illuminate\Http\Request;

class DailyLogController extends Controller
{
    public function __construct(
        protected readonly ClockIn $clockInAction,
        protected readonly ClockOut $clockOutAction,
        protected readonly Punch $punchAction,
    ) {}

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

    public function store(Request $request)
    {
        //
    }

    public function clockIn(ClockEntryStoreRequest $request)
    {
        $data = ClockInDTO::fromValidatable($request);
        $entry = $this->clockInAction->execute($data);

        return Split::respond($entry, route: 'dashboard');
    }

    public function clockOut(ClockEntryStoreRequest $request)
    {
        $data = ClockOutDTO::fromValidatable($request);
        $entry = $this->clockOutAction->execute($data);

        return Split::respond($entry, route: 'dashboard');
    }

    public function punch(Request $_request, DailyLogEntity $dailyLog)
    {
        $updated = $this->punchAction->execute($dailyLog);

        return Split::respond($updated, route: 'dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClockEntryEntity $clockEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClockEntryEntity $clockEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClockEntryEntity $clockEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClockEntryEntity $clockEntry)
    {
        //
    }
}

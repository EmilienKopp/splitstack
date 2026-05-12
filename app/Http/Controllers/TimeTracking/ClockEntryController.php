<?php

namespace App\Http\Controllers\TimeTracking;

use App\Application\TimeTracking\Actions\ClockIn;
use App\Application\TimeTracking\DTOs\ClockInDTO;
use App\Domain\TimeTracking\Entities\ClockEntryEntity;
use App\Facades\Split;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeTracking\ClockEntryStoreRequest;
use Illuminate\Http\Request;

class ClockEntryController extends Controller
{
    public function __construct(
        protected readonly ClockIn $clockInAction,
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClockEntryStoreRequest $request)
    {
        $data = ClockInDTO::fromValidatable($request);
        $entry = $this->clockInAction->execute($data);

        return Split::respond($entry, component: 'Dashboard');
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

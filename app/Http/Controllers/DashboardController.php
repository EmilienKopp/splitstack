<?php

namespace App\Http\Controllers;

use App\Domain\TimeTracking\Contracts\ClockEntryRepository;
use App\Domain\TimeTracking\Contracts\ProjectRepository;
use App\Facades\Split;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly ClockEntryRepository $clockEntryRepository,
        protected readonly ProjectRepository $projectRepository,
    ) {}

    public function index()
    {
        $userId = request()->user()->getKey();

        return Split::respond([
            'projects' => $this->projectRepository->findForUser($userId),
            'todayEntries' => $this->clockEntryRepository->getTodayEntries($userId),
        ], component: 'Dashboard');
    }
}

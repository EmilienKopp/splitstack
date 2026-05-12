<?php

namespace App\Http\Controllers\TimeTracking;

use App\Application\TimeTracking\Actions\CreateProject;
use App\Application\TimeTracking\DTOs\CreateProjectDTO;
use App\Domain\TimeTracking\Contracts\ProjectRepository;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeTracking\CreateProjectRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private readonly CreateProject $createProject,
        private readonly ProjectRepository $projectRepository,
    ) {}

    public function index(): Response
    {
        $userId = request()->user()->getKey();

        return Inertia::render('projects/Index', [
            'projects' => $this->projectRepository->findForUser($userId),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('projects/Create', [
            'statusOptions' => collect(ProjectStatus::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->name]),
            'typeOptions' => collect(ProjectType::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->name]),
        ]);
    }

    public function store(CreateProjectRequest $request): RedirectResponse
    {
        $this->createProject->execute(CreateProjectDTO::fromValidatable($request));

        return redirect()->route('projects.index');
    }

    public function show(int|string $id): Response
    {
        $project = $this->projectRepository->find((int) $id);

        return Inertia::render('projects/Show', [
            'project' => $project,
        ]);
    }
}

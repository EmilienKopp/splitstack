<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepositoryRequest;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\RepositoryRepositoryInterface;
use Illuminate\Http\Request;

class RepositoryController extends HybridController
{
    public function __construct(public RepositoryRepositoryInterface $repositoryRepository, public ProjectRepositoryInterface $projectRepository) {}

    public function index(Request $request, ?string $account = null)
    {
        $repositories = $this->repositoryRepository->all();

        return $this->respond(
            $repositories,
            fn () => inertia()->render('Repositories/Index', ['repositories' => $repositories])
        );
    }

    public function show(Request $request, ?string $account, int $id)
    {
        $repository = $this->repositoryRepository->find($id);

        return $this->respond(
            $repository,
            fn () => inertia()->render('Repositories/Show', ['repository' => $repository])
        );
    }

    public function link(StoreRepositoryRequest $request, ?string $account = null)
    {
        $validated = $request->validated();
        $repo = $this->repositoryRepository->create($validated);

        return $this->respond(
            ['message' => 'Repository created successfully', 'record' => $repo],
            fn () => inertia()->render('Repositories/Index', ['repository' => $repo])
        );

    }
}

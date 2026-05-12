<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use App\Services\AIService;
use App\Services\GitHubService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    private $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generate(Request $request)
    {
        try {
            $content = $this->aiService->generateGitSummary(log: $request->input('original_log'), reportType: $request->input('report_type'));
            \Log::info('Git report generated successfully', [
                'user_id' => auth()->id(),
                'content' => $content,
            ]);

            return Inertia::render('Report/Create', [
                'content' => $content,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Report/Create', [
                'errors' => [
                    'content' => 'Failed to generate report: '.$e->getMessage(),
                ],
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = auth()->user()->reports()
            ->with('user')
            ->latest()
            ->paginate(10);

        return inertia()->render('Report/Index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gh = GitHubService::forUser(auth()->id());
        $repositories = $gh->getRepositories();

        $getGranches = function () use ($gh, $repositories) {
            $branches = collect();
            foreach ($repositories as $repo) {
                try {
                    $branches[$repo['full_name']] = $gh->getBranches($repo['full_name']);
                } catch (\Exception $e) {
                    $branches[$repo['full_name']] = collect(); // Fallback to empty collection
                }
            }

            return $branches;
        };

        return Inertia::render('Report/Create', [
            'repositories' => $repositories,
            'branches' => Inertia::defer($getGranches),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
            'report_type' => 'required|in:technical,financial,operational,task_based',
            'title' => 'required|string|min:5|max:255',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'content' => $validatedData['content'],
            'report_type' => $validatedData['report_type'],
            'title' => $validatedData['title'],
        ]);

        return Inertia::render('Report/Show', [
            'report' => $report,
            'message' => 'Report created successfully!',
        ]);
    }

    public function fetchCommits(Request $request)
    {
        $validated = $request->validate([
            'repository' => 'required|string',
            'branch' => 'required|string',
            'since' => 'nullable|date',
            'until' => 'nullable|date',
        ]);

        $gh = GitHubService::forUser(auth()->id());
        $logRequest = new \App\DTOs\GitLogRequest(
            repository: $validated['repository'],
            branch: $validated['branch'],
            since: isset($validated['since']) ? \Carbon\Carbon::parse($validated['since']) : null,
            until: isset($validated['until']) ? \Carbon\Carbon::parse($validated['until']) : null,
            author: auth()->user()->gitHubConnection?->username ?? null,
        );
        $logs = $gh->getGitLogs($logRequest);

        return Inertia::render('Report/Create', [
            'logs' => $logs,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(?string $account, Report $report)
    {
        return Inertia::render('Report/Show', [
            'report' => $report,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(?string $account, Report $report)
    {
        return Inertia::render('Report/Edit', [
            'report' => $report,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(?string $account, UpdateReportRequest $request, Report $report)
    {
        $validatedData = $request->validated();
        $report->update([
            'content' => $validatedData['content'],
            'title' => $validatedData['title'],
        ]);

        return Inertia::render('Report/Show', [
            'report' => $report,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }

    public function bustCache()
    {
        GitHubService::forUser(auth()->id())->clearCache();

        return response()->json(['message' => 'Report cache busted successfully.']);
    }
}

<?php

namespace App\Console\Commands\Git;

use App\DTOs\GitLogRequest;
use App\Services\GitService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunGitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:run
                            {action : The git action to perform (logs, status, branches, diff, info)}
                            {--path= : Repository path (defaults to current project)}
                            {--branch= : Branch name (defaults to current branch)}
                            {--since= : Start date for logs (e.g., "7 days ago", "2024-01-01")}
                            {--until= : End date for logs (e.g., "today", "2024-12-31")}
                            {--author= : Filter commits by author email}
                            {--sha= : Commit SHA for diff action}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run local git commands using GitService';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $path = $this->option('path') ?: base_path();

        try {
            $service = GitService::forRepository($path);

            return match ($action) {
                'logs' => $this->handleLogs($service),
                'status' => $this->handleStatus($service),
                'branches' => $this->handleBranches($service),
                'diff' => $this->handleDiff($service),
                'info' => $this->handleInfo($service),
                default => $this->handleUnknownAction($action),
            };
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    private function handleLogs(GitService $service): int
    {
        $branch = $this->option('branch') ?: $service->getCurrentBranch();
        $since = $this->option('since') ? Carbon::parse($this->option('since')) : null;
        $until = $this->option('until') ? Carbon::parse($this->option('until')) : null;
        $author = $this->option('author');

        $request = new GitLogRequest(
            repository: $service->getRepositoryName(),
            branch: $branch,
            since: $since,
            until: $until,
            author: $author,
            includeDiff: false
        );

        $response = $service->getGitLogs($request);

        if ($this->option('json')) {
            $this->line(json_encode([
                'repository' => $response->repository,
                'branch' => $response->branch,
                'total_count' => $response->totalCount,
                'commits' => $response->commits->map(fn ($commit) => [
                    'sha' => $commit->sha,
                    'message' => $commit->message,
                    'author' => $commit->author,
                    'email' => $commit->authorEmail,
                    'date' => $commit->date->toISOString(),
                ])->toArray(),
            ], JSON_PRETTY_PRINT));
        } else {
            $this->info("Git logs for {$response->repository} on branch {$response->branch}");
            $this->info("Total commits: {$response->totalCount}");
            $this->newLine();

            foreach ($response->commits as $commit) {
                $this->line("<fg=yellow>{$commit->sha}</>");
                $this->line("<fg=cyan>{$commit->author}</> <{$commit->authorEmail}>");
                $this->line("<fg=gray>{$commit->date->format('Y-m-d H:i:s')}</>");
                $this->line($commit->message);
                $this->newLine();
            }
        }

        return Command::SUCCESS;
    }

    private function handleStatus(GitService $service): int
    {
        $isClean = $service->isClean();
        $changes = $service->getUncommittedChanges();

        if ($this->option('json')) {
            $this->line(json_encode([
                'is_clean' => $isClean,
                'changes' => $changes->toArray(),
            ], JSON_PRETTY_PRINT));
        } else {
            if ($isClean) {
                $this->info('✓ Working directory is clean');
            } else {
                $this->warn('✗ Working directory has uncommitted changes');
                $this->newLine();
                $this->table(['Status', 'File'], $changes->map(fn ($change) => [
                    $change['status'],
                    $change['filename'],
                ])->toArray());
            }
        }

        return Command::SUCCESS;
    }

    private function handleBranches(GitService $service): int
    {
        $branches = $service->getBranches();
        $current = $service->getCurrentBranch();

        if ($this->option('json')) {
            $this->line(json_encode([
                'current' => $current,
                'branches' => $branches->toArray(),
            ], JSON_PRETTY_PRINT));
        } else {
            $this->info("Branches for {$service->getRepositoryName()}");
            $this->info("Current: {$current}");
            $this->newLine();

            $this->table(['Branch', 'Commit SHA'], $branches->map(fn ($branch) => [
                $branch['name'] === $current ? "* {$branch['name']}" : "  {$branch['name']}",
                substr($branch['commit_sha'], 0, 7),
            ])->toArray());
        }

        return Command::SUCCESS;
    }

    private function handleDiff(GitService $service): int
    {
        $sha = $this->option('sha');

        if (! $sha) {
            $this->error('--sha option is required for diff action');

            return Command::FAILURE;
        }

        $diff = $service->getCommitDiff($sha);

        if ($this->option('json')) {
            $this->line(json_encode([
                'sha' => $sha,
                'files' => $diff->map(fn ($file) => [
                    'filename' => $file->filename,
                    'status' => $file->status,
                    'additions' => $file->additions,
                    'deletions' => $file->deletions,
                    'changes' => $file->changes,
                ])->toArray(),
            ], JSON_PRETTY_PRINT));
        } else {
            $this->info("Diff for commit {$sha}");
            $this->newLine();

            if ($diff->isEmpty()) {
                $this->warn('No file changes found');
            } else {
                $this->table(
                    ['Status', 'File', 'Changes'],
                    $diff->map(fn ($file) => [
                        $file->status,
                        $file->filename,
                        "<fg=green>+{$file->additions}</> <fg=red>-{$file->deletions}</>",
                    ])->toArray()
                );
            }
        }

        return Command::SUCCESS;
    }

    private function handleInfo(GitService $service): int
    {
        $name = $service->getRepositoryName();
        $branch = $service->getCurrentBranch();
        $remote = $service->getRemoteUrl();
        $commitCount = $service->getCommitCount();
        $isClean = $service->isClean();

        if ($this->option('json')) {
            $this->line(json_encode([
                'name' => $name,
                'current_branch' => $branch,
                'remote_url' => $remote,
                'total_commits' => $commitCount,
                'is_clean' => $isClean,
            ], JSON_PRETTY_PRINT));
        } else {
            $this->info('Repository Information');
            $this->newLine();
            $this->line("Name:           {$name}");
            $this->line("Current Branch: {$branch}");
            $this->line('Remote URL:     '.($remote ?: 'None configured'));
            $this->line("Total Commits:  {$commitCount}");
            $this->line('Status:         '.($isClean ? '<fg=green>Clean</>' : '<fg=yellow>Uncommitted changes</>'));
        }

        return Command::SUCCESS;
    }

    private function handleUnknownAction(string $action): int
    {
        $this->error("Unknown action: {$action}");
        $this->info('Available actions: logs, status, branches, diff, info');

        return Command::FAILURE;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Landlord\KnownIssue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KnownIssueWebhookController extends Controller
{
    /**
     * Handle incoming webhook from n8n with Jira issues and GitHub PR data.
     */
    public function store(Request $request)
    {

        try {
            $payload = $request->all();

            // Separate GitHub data from Jira issues
            $githubData = $this->extractGitHubData($payload);
            $issues = $this->extractJiraIssues($payload);

            \Log::debug('Received webhook', [
                'issue_count' => count($issues),
                'has_github_data' => ! empty($githubData),
            ]);

            $created = 0;
            $updated = 0;

            // Extract deployed Jira keys from GitHub data
            $deployedJiraKeys = $this->extractDeployedJiraKeys($githubData);

            foreach ($issues as $issueData) {
                $this->processIssue($issueData, $deployedJiraKeys, $githubData, $created, $updated);
            }

            return response()->json([
                'success' => true,
                'message' => "Processed {$created} new issues and updated {$updated} existing issues",
                'created' => $created,
                'updated' => $updated,
                'deployed_keys' => $deployedJiraKeys,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to process webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract GitHub PR and commits data from the payload.
     */
    private function extractGitHubData(array $payload): array
    {
        $githubData = [
            'pr' => null,
            'commits' => [],
        ];

        // Handle new structure where payload is an array with one object containing issues, commits, pr
        if (isset($payload[0])) {
            $data = $payload[0];

            if (isset($data['pr'])) {
                $githubData['pr'] = $data['pr'];
            }

            if (isset($data['commits']) && is_array($data['commits'])) {
                $githubData['commits'] = $data['commits'];
            }
        }

        return $githubData;
    }

    /**
     * Extract Jira issues from the payload.
     */
    private function extractJiraIssues(array $payload): array
    {
        // Handle new structure where payload is an array with one object containing issues
        if (isset($payload[0]['issues']) && is_array($payload[0]['issues'])) {
            \Log::debug("Payload 0");
            return $payload[0]['issues'];
        }

        if(isset($payload['issues'])) {
            return $payload['issues'];
        }
        
        // Fallback to old structure (array of issue objects)
        $issues = [];
        foreach ($payload as $item) {
            if (isset($item['key']) && isset($item['fields'])) {
                $issues[] = $item;
            }
        }

        return $issues;
    }

    /**
     * Extract Jira issue keys from GitHub PR branch name and commit messages.
     */
    private function extractDeployedJiraKeys(array $githubData): array
    {
        $jiraKeys = [];

        // Extract from PR branch name (head.ref)
        if (isset($githubData['pr']['head']['ref'])) {
            $branchName = $githubData['pr']['head']['ref'];
            $this->extractJiraKeysFromText($branchName, $jiraKeys);
        }

        // Extract from commit messages
        foreach ($githubData['commits'] as $commit) {
            $message = $commit['commit']['message'] ?? '';
            $this->extractJiraKeysFromText($message, $jiraKeys);
        }

        return array_unique($jiraKeys);
    }

    /**
     * Extract Jira issue keys from text using regex pattern.
     */
    private function extractJiraKeysFromText(string $text, array &$jiraKeys): void
    {
        // Match Jira issue keys (e.g., QAD-123, PROJ-456)
        preg_match_all('/\b([A-Z]{2,10}-\d+)\b/', $text, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $key) {
                $jiraKeys[] = $key;
            }
        }
    }

    /**
     * Process a single Jira issue.
     */
    private function processIssue(array $issueData, array $deployedJiraKeys, array $githubData, int &$created, int &$updated): void
    {
        $jiraKey = $issueData['key'] ?? null;

        if (! $jiraKey) {
            Log::warning('Skipping issue without key', ['data' => $issueData]);

            return;
        }

        $fields = $issueData['fields'] ?? [];

        // Extract status information
        $status = $fields['status']['name'] ?? 'Unknown';
        $statusCategory = $fields['status']['statusCategory']['key'] ?? null;
        $statusCategoryName = $fields['status']['statusCategory']['name'] ?? null;
        $statusColor = $fields['status']['statusCategory']['colorName'] ?? null;

        // Extract other fields
        $summary = $fields['summary'] ?? 'No summary';
        $description = $fields['description'] ?? null;
        $priority = $fields['priority']['name'] ?? null;
        $priorityIconUrl = $fields['priority']['iconUrl'] ?? null;
        $issueType = $fields['issuetype']['name'] ?? null;
        $issueTypeIconUrl = $fields['issuetype']['iconUrl'] ?? null;

        // Parse dates
        $firstReportedAt = isset($fields['created'])
            ? Carbon::parse($fields['created'])
            : now();

        $lastUpdatedAt = isset($fields['updated'])
            ? Carbon::parse($fields['updated'])
            : now();

        // Check if this issue is deployed (in GitHub PR/commits and status is Done)
        $isDeployed = $this->isIssueDeployed($jiraKey, $statusCategory, $deployedJiraKeys);

        // Prepare deployment metadata if deployed
        $deploymentMetadata = null;
        $deployedAt = null;

        if ($isDeployed) {
            $deploymentMetadata = [
                'pr' => $githubData['pr'] ? [
                    'number' => $githubData['pr']['number'] ?? null,
                    'title' => $githubData['pr']['title'] ?? null,
                    'url' => $githubData['pr']['html_url'] ?? null,
                    'branch' => $githubData['pr']['head']['ref'] ?? null,
                    'merged_at' => $githubData['pr']['merged_at'] ?? null,
                ] : null,
                'commits' => array_map(function ($commit) {
                    return [
                        'sha' => $commit['sha'] ?? null,
                        'message' => $commit['commit']['message'] ?? null,
                        'url' => $commit['html_url'] ?? null,
                        'date' => $commit['commit']['author']['date'] ?? null,
                    ];
                }, $githubData['commits']),
            ];

            // Use PR merged_at if available, otherwise use now
            $deployedAt = isset($githubData['pr']['merged_at'])
                ? Carbon::parse($githubData['pr']['merged_at'])
                : now();
        }

        // Find or create the issue
        $issue = KnownIssue::where('jira_key', $jiraKey)->first();

        if ($issue) {
            // Update existing issue
            $statusChanged = $issue->hasStatusChanged($status);

            $issue->update([
                'jira_id' => $issueData['id'] ?? $issue->jira_id,
                'summary' => $summary,
                'description' => $description,
                'priority' => $priority,
                'priority_icon_url' => $priorityIconUrl,
                'issue_type' => $issueType,
                'issue_type_icon_url' => $issueTypeIconUrl,
                'last_updated_at' => $lastUpdatedAt,
                'metadata' => $issueData,
                'is_deployed' => $isDeployed,
                'deployment_metadata' => $deploymentMetadata,
                'deployed_at' => $deployedAt,
            ]);

            // Update status if changed
            if ($statusChanged) {
                $issue->updateStatus($status, $statusCategory, $statusCategoryName);
                $issue->update(['status_color' => $statusColor]);
            }

            $updated++;
        } else {
            // Create new issue
            $issue = KnownIssue::create([
                'jira_id' => $issueData['id'] ?? null,
                'jira_key' => $jiraKey,
                'summary' => $summary,
                'description' => $description,
                'status' => $status,
                'status_category' => $statusCategory,
                'status_category_name' => $statusCategoryName,
                'status_color' => $statusColor,
                'priority' => $priority,
                'priority_icon_url' => $priorityIconUrl,
                'issue_type' => $issueType,
                'issue_type_icon_url' => $issueTypeIconUrl,
                'first_reported_at' => $firstReportedAt,
                'last_updated_at' => $lastUpdatedAt,
                'current_status_since' => $firstReportedAt,
                'metadata' => $issueData,
                'is_deployed' => $isDeployed,
                'deployment_metadata' => $deploymentMetadata,
                'deployed_at' => $deployedAt,
            ]);

            $created++;
        }

        Log::info('Processed Jira issue', [
            'key' => $jiraKey,
            'action' => $issue->wasRecentlyCreated ? 'created' : 'updated',
            'is_deployed' => $isDeployed,
        ]);
    }

    /**
     * Determine if an issue is deployed based on status and GitHub data.
     */
    private function isIssueDeployed(string $jiraKey, ?string $statusCategory, array $deployedJiraKeys): bool
    {
        // Issue must be in "done" status AND appear in GitHub PR/commits
        return $statusCategory === 'done' && in_array($jiraKey, $deployedJiraKeys);
    }
}

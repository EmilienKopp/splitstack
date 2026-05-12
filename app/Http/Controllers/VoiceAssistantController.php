<?php

namespace App\Http\Controllers;

use App\DTOs\N8nConfig;
use App\DTOs\N8nCredentials;
use App\Jobs\StoreVoiceCommandJob;
use App\Models\Landlord\Tenant;
use App\Services\AI\AIPromptRegistry;
use App\Services\AIService;
use App\Services\N8NService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VoiceAssistantController extends Controller
{
    public function __construct(
        private N8NService $n8nService,
        private AIService $aiService
    ) {}

    /**
     * Display the voice assistant settings page
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $tenant = Tenant::current();

        $tenantConfig = $tenant?->n8n_config;
        $userConfig = $user->n8n_config;

        $effectiveConfig = $tenantConfig?->mergeWith($userConfig);

        return Inertia::render('VoiceAssistant/Settings', [
            'config' => $effectiveConfig?->jsonSerialize(),
            'isActivated' => $effectiveConfig?->isValid() && $effectiveConfig->workflowId !== null,
            'canActivate' => $tenantConfig !== null, // Tenant must have base config
        ]);
    }

    /**
     * Activate AI Voice Assistant for the current user
     * Creates a PAT, MCP credentials in n8n, and the workflow
     */
    public function activate(Request $request)
    {
        $user = $request->user();
        $tenant = Tenant::current();

        \Log::debug('Activating voice assistant', [
            'user_id' => $user->id,
            'tenant_id' => $tenant?->id,
        ]);

        if (! $tenant) {
            return back()->with('error', 'Tenant context not found.');
        }

        try {
            DB::beginTransaction();

            // 1. Get or create tenant-level base configuration
            $tenantConfig = $tenant->n8n_config ?? new N8nConfig;
            if (! $tenantConfig->mcpEndpointUrl) {
                $tenantConfig->mcpEndpointUrl = $this->aiService->getMcpEndpointUrl();
                $tenant->n8n_config = $tenantConfig;
                $tenant->save();
            }

            // 2. Create Personal Access Token for MCP authentication
            $this->clearAssistantPersonalAccessTokens($request);
            $tokenName = 'AI Assistant - '.now()->format('Y-m-d H:i:s');
            $token = $user->createToken($tokenName, [
                'mcp:use',
                'mcp:tools',
                'mcp:resources',
                'mcp:prompts',
            ], now()->addYear());

            Log::info('Voice assistant PAT created', [
                'user_id' => $user->id,
                'token_name' => $tokenName,
                'tenant_id' => $tenant->id,
            ]);

            // 3. Create or reuse MCP credentials in n8n
            $mcpCredentialName = "MCP Bearer - {$user->email}";

            // Check if user already has MCP credentials configured
            $userConfig = $user->n8n_config;
            $mcpCredentials = $userConfig?->mcpCredentials;

            \Log::debug('MCP Credentials check', [
                'user_id' => $user->id,
                'token_plain_text' => $token->plainTextToken,
            ]);

            if (! $mcpCredentials) {
                // Create new MCP credentials in n8n
                try {
                    $mcpCredResult = $this->n8nService->createMcpCredentials(
                        $mcpCredentialName,
                        [
                            'token' => $token->plainTextToken, // Just the token, n8n adds "Bearer " prefix
                        ]
                    );

                    $mcpCredentials = new N8nCredentials(
                        id: $mcpCredResult['id'],
                        name: $mcpCredentialName
                    );

                    Log::info('N8n MCP credentials created', [
                        'user_id' => $user->id,
                        'credential_id' => $mcpCredentials->id,
                        'credential_name' => $mcpCredentials->name,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create N8n MCP credentials', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);

                    // If n8n API fails, we can still use tenant-level credentials
                    $mcpCredentials = $tenantConfig->mcpCredentials;

                    if (! $mcpCredentials) {
                        throw new \Exception('Unable to create MCP credentials and no tenant fallback available.');
                    }
                }
            }

            // 4. Prepare user configuration (override AI credentials if provided)
            if (! $userConfig) {
                $userConfig = new N8nConfig(
                    mcpCredentials: $mcpCredentials,
                );
            } else {
                $userConfig->mcpCredentials = $mcpCredentials;
            }

            // Merge with tenant config for workflow generation
            $effectiveConfig = $tenantConfig->mergeWith($userConfig);

            // 5. Generate and create workflow
            $workflowData = $this->n8nService->generateAgentWorkflow(
                mcpCredentials: $effectiveConfig->getMcpCredentialsArray(),
                mcpEndpointUrl: $effectiveConfig->mcpEndpointUrl,
                aiCredentials: $effectiveConfig->getAiCredentialsArray(),
                appUrl: config('app.url')
            );

            // Extract webhook ID for URL construction
            $webhookId = $workflowData['webhookId'] ?? null;
            $workflowTemplate = $workflowData;
            unset($workflowTemplate['webhookId']); // Remove webhookId before sending to n8n

            // Add workflow name
            $workflowTemplate['name'] = "AI Assistant - {$user->email} - {$tenant->name}";

            try {
                $createdWorkflow = $this->n8nService->createWorkflow($workflowTemplate);

                Log::info('N8n workflow created', [
                    'user_id' => $user->id,
                    'workflow_id' => $createdWorkflow['id'] ?? 'unknown',
                    'workflow_name' => $workflowTemplate['name'],
                ]);

                // 6. Update user config with workflow details
                $userConfig->workflowId = $createdWorkflow['id'] ?? null;

                // Construct webhook URL from n8n base URL and webhook ID
                $n8nBaseUrl = config('services.n8n.base_url');
                $userConfig->webhookUrl = $webhookId ? "{$n8nBaseUrl}/webhook/{$webhookId}" : null;

                if (! $userConfig->workflowId) {
                    throw new \Exception('Workflow created but ID not returned from n8n.');
                }
            } catch (\Exception $e) {
                Log::error('Failed to create N8n workflow', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw new \Exception('Failed to create workflow in n8n: '.$e->getMessage());
            }

            // 7. Save user configuration
            $user->n8n_config = $userConfig;
            $user->save();

            DB::commit();

            try {
                $this->n8nService->activateWorkflow($userConfig->workflowId);
            } catch (\Exception $e) {
                Log::error('Failed to activate N8n workflow', [
                    'user_id' => $user->id,
                    'workflow_id' => $userConfig->workflowId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw new \Exception('Failed to activate workflow in n8n: '.$e->getMessage());
            }

            Log::info('Voice assistant activated successfully', [
                'user_id' => $user->id,
                'workflow_id' => $userConfig->workflowId,
                'webhook_url' => $userConfig->webhookUrl,
            ]);

            return back()->with('success', 'AI Voice Assistant activated successfully!')
                ->with('webhookUrl', $userConfig->webhookUrl);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Voice assistant activation failed', [
                'user_id' => $user->id,
                'tenant_id' => $tenant?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to activate AI Voice Assistant: '.$e->getMessage());
        }
    }

    /**
     * Deactivate AI Voice Assistant for the current user
     * Optionally deletes the workflow and revokes tokens
     */
    public function deactivate(Request $request)
    {
        $user = $request->user();
        $userConfig = $user->n8n_config;

        if (! $userConfig) {
            return back()->with('info', 'Voice assistant is not activated.');
        }

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'delete_workflow' => 'boolean',
                'revoke_tokens' => 'boolean',
            ]);

            // Delete workflow in n8n if requested
            if ($validated['delete_workflow'] ?? false) {
                if ($userConfig->workflowId) {
                    try {
                        $this->n8nService->deleteWorkflow($userConfig->workflowId);
                        Log::info('N8n workflow deleted', [
                            'user_id' => $user->id,
                            'workflow_id' => $userConfig->workflowId,
                        ]);

                        $this->n8nService->deleteMcpCredentials($userConfig->mcpCredentials?->id);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete N8n workflow', [
                            'user_id' => $user->id,
                            'workflow_id' => $userConfig->workflowId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Revoke AI Assistant tokens if requested
            if ($validated['revoke_tokens'] ?? false) {
                $this->clearAssistantPersonalAccessTokens($request);
            }

            // Clear user configuration
            $user->n8n_config = null;
            $user->save();

            // Clear Tenant config
            Tenant::current()->n8n_config = [];
            Tenant::current()->save();

            DB::commit();

            return back()->with('success', 'AI Voice Assistant deactivated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Voice assistant deactivation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to deactivate AI Voice Assistant: '.$e->getMessage());
        }
    }

    /**
     * Update user-specific preferences for the voice assistant
     */
    public function updatePreferences(Request $request)
    {
        $user = $request->user();
        $userConfig = $user->n8n_config;

        if (! $userConfig) {
            return back()->with('error', 'Voice assistant is not activated.');
        }

        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.model' => 'sometimes|string',
            'preferences.timeout' => 'sometimes|integer|min:5|max:300',
            'preferences.max_tokens' => 'sometimes|integer|min:100|max:100000',
            'preferences.temperature' => 'sometimes|numeric|min:0|max:2',
        ]);

        try {
            foreach ($validated['preferences'] as $key => $value) {
                $userConfig->setPreference($key, $value);
            }

            $user->n8n_config = $userConfig;
            $user->save();

            Log::info('Voice assistant preferences updated', [
                'user_id' => $user->id,
                'preferences' => $validated['preferences'],
            ]);

            return back()->with('success', 'Preferences updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update voice assistant preferences', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update preferences: '.$e->getMessage());
        }
    }

    /**
     * Transcribe audio and get assistant response using user's n8n workflow
     */
    public function transcribeToAssistant(Request $request)
    {
        try {
            $request->validate([
                'audio' => 'required|file|mimes:webm,wav,mp3,ogg,m4a|max:10240', // 10MB max
            ]);

            $audioFile = $request->file('audio');

            if (! $audioFile) {
                return back()->with('data', [
                    'audioError' => 'No audio file provided',
                ]);
            }

            // Get the user's n8n configuration
            $user = $request->user();
            $effectiveConfig = $this->n8nService->getMergedConfig();

            if (! $effectiveConfig || ! $effectiveConfig->webhookUrl) {
                return back()->with('data', [
                    'audioError' => 'Voice assistant is not activated or webhook URL is missing.',
                ]);
            }

            // Get the transcript from the AI service
            $transcript = $this->aiService->transcribeAudio($audioFile);

            $userId = $user->id;

            // Save the voice command to database asynchronously with tenant context
            StoreVoiceCommandJob::dispatch(
                userId: $userId,
                transcript: $transcript,
            );

            // Send to n8n workflow using the user's webhook URL
            $response = $this->aiService->textToAssistant($transcript, $effectiveConfig->webhookUrl);

            Log::debug('Assistant response', [
                'response' => $response,
            ]);

            return back()->with('data', [
                'error' => null,
                'transcript' => $transcript,
                'assistantResponse' => $response,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let Inertia handle validation errors
        } catch (\Exception $e) {
            Log::error('Audio instructions failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('data', [
                'audioError' => 'Failed to transcribe audio: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Send transcript to the user's n8n assistant workflow
     */
    private function sendToAssistantWorkflow(string $webhookUrl, string $transcript, $user)
    {
        try {
            $client = new \GuzzleHttp\Client;
            $tenantId = hash('sha256', Tenant::current()?->id.env('APP_KEY'));

            // Get the AI Assistant token (hashed) for user identification in Server Auth node
            $aiToken = $user->tokens()
                ->where('name', 'like', 'AI Assistant%')
                ->latest()
                ->first();

            $response = $client->post($webhookUrl, [
                'json' => [
                    'timestamp' => now()->toIso8601String(),
                    'token' => $aiToken?->token ?? '', // Hashed token for user lookup in decrypt-tenant
                    'system_prompt' => AIPromptRegistry::getVoiceAssistantSystemPrompt($transcript),
                    'user_input' => $transcript,
                    'tenant_id' => $tenantId,
                ],
                'timeout' => 30,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error('n8n webhook error:', [
                    'status' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents(),
                ]);
                throw new \Exception('Webhook execution failed: '.$response->getBody()->getContents());
            }

            [$data] = json_decode($response->getBody()->getContents(), true);
            Log::info('n8n webhook response:', $data ?? []);

            return $data['output'] ?? [];
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error('n8n webhook request failed:', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to connect to webhook service: '.$e->getMessage());
        } catch (\Exception $e) {
            Log::error('Webhook execution failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function clearAssistantPersonalAccessTokens(Request $request)
    {
        $user = $request->user();

        $revokedCount = $user->tokens()
            ->where('name', 'like', 'AI Assistant%')
            ->delete();

        return $revokedCount;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMcpTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class McpTokenController extends Controller
{
    /**
     * Display the user's MCP tokens.
     */
    public function index()
    {
        $tokens = Auth::user()->tokens()
            ->where('name', 'like', 'MCP:%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at?->format('Y-m-d H:i:s'),
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $token->expires_at?->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($tokens);
    }

    /**
     * Create a new MCP access token.
     */
    public function store(CreateMcpTokenRequest $request)
    {
        $tokenName = 'MCP: '.$request->validated('name');

        // Create token with MCP-specific abilities and rate limiting
        $token = $request->user()->createToken($tokenName, [
            'mcp:use',
            'mcp:tools',
            'mcp:resources',
            'mcp:prompts',
        ], $request->validated('expires_at') ? now()->parse($request->validated('expires_at')) : null);

        $tenant = \App\Models\Landlord\Tenant::current();

        return Inertia::render('Profile/Edit', [
            'newMcpToken' => [
                'token' => $token->plainTextToken,
                'token_name' => $tokenName,
                'tenant_host' => $tenant?->host,
                'formatted_token' => $tenant ? "tenant:{$tenant->host}:{$token->plainTextToken}" : $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at?->format('Y-m-d H:i:s'),
                'created_at' => $token->accessToken->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Delete an MCP access token.
     */
    public function destroy(Request $request, int $tokenId)
    {
        $token = $request->user()->tokens()
            ->where('id', $tokenId)
            ->where('name', 'like', 'MCP:%')
            ->first();

        if (! $token) {
            return Inertia::render('Profile/Edit', [
                'error' => 'Token not found.',
            ]);
        }

        $tokenName = $token->name;

        // Log token deletion for security audit
        \Log::info('MCP token deleted', [
            'user_id' => $request->user()->id,
            'token_name' => $tokenName,
            'token_id' => $tokenId,
            'tenant_id' => \App\Models\Landlord\Tenant::current()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $token->delete();

        return Inertia::render('Profile/Edit', [
            'message' => "Token '{$tokenName}' has been deleted successfully.",
        ]);
    }

    /**
     * Get MCP connection information for the current tenant.
     */
    public function connectionInfo()
    {
        $tenant = \App\Models\Landlord\Tenant::current();

        if (! $tenant) {
            return Inertia::render('Profile/Edit', [
                'error' => 'No tenant context found.',
            ]);
        }

        // Determine the correct URL based on environment
        $baseUrl = config('app.url');

        if (app()->environment('staging')) {
            $mcpUrl = "{$baseUrl}/mcp/qadran";
        } elseif (app()->isProduction()) {
            $mcpUrl = 'https://qadran.io/mcp/qadran';
        } else {
            $mcpUrl = 'http://localhost:8000/mcp/qadran';
        }

        return Inertia::render('Profile/Edit', [
            'connectionInfo' => [
                'tenant_host' => $tenant->host,
                'tenant_name' => $tenant->name,
                'mcp_url' => $mcpUrl,
                'auth_format' => 'tenant:'.$tenant->host.':YOUR_TOKEN',
                'example_config' => [
                    'curl' => [
                        'command' => 'curl',
                        'args' => [
                            '-X', 'POST',
                            $mcpUrl,
                            '-H', 'Content-Type: application/json',
                            '-H', 'Authorization: Bearer tenant:'.$tenant->host.':YOUR_TOKEN',
                        ],
                    ],
                    'vscode' => [
                        'mcpServers' => [
                            'qadran' => [
                                'command' => 'curl',
                                'args' => [
                                    '-X', 'POST',
                                    $mcpUrl,
                                    '-H', 'Content-Type: application/json',
                                    '-H', 'Authorization: Bearer tenant:'.$tenant->host.':YOUR_TOKEN',
                                    '-d', '@-',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}

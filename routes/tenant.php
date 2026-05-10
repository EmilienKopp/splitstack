<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\ClockEntryController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GitHubOAuthController;
use App\Http\Controllers\McpTokenController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuickActionController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VoiceAssistantController;
use App\Models\Landlord\Tenant;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Inertia\Inertia;

$APP_HOST = Uri::of(config('app.url'))->host();

// // Set default guard for tenant routes
// config(['auth.defaults.guard' => 'tenant']);

// // Root route - shows landing page if no tenant, otherwise redirects based on auth
// Route::get('/', function () {
//     $tenant = Tenant::current();

//     return Inertia::render('Landing', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'authenticated' => Auth::guard()->check(),
//         'tenant' => $tenant,
//     ]);
// })->name('tenant.root');

// Route::get('/landing', function () {
//     $tenant = Tenant::current();

//     return Inertia::render('Landing', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'authenticated' => Auth::guard()->check(),
//         'tenant' => $tenant,
//     ]);
// })->name('tenant.landing');

// // Welcome/Login page for tenant subdomains
// Route::get('/welcome', function () {
//     return Inertia::render('TenantWelcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'domain' => Tenant::current()?->domain,
//     ]);
// })->name('tenant.welcome');

// // Public privacy policy page (tenant-scoped, no auth required)
// Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])
//     ->name('privacy-policy.index');

Route::middleware(['auth'])->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // GitHub OAuth routes for account linking
    // Note: Primary login route is in routes/auth.php
    // Route::get('/settings/github/connect', [GitHubOAuthController::class, 'redirect'])
    //     ->name('github.connect');
    // // Callback route moved to routes/auth.php to handle both login and linking
    // Route::post('/settings/github/confirm-replace', [GitHubOAuthController::class, 'confirmReplace'])
    //     ->name('github.confirm-replace');
    // Route::delete('/settings/github/disconnect', [GitHubOAuthController::class, 'disconnect'])
    //     ->name('github.disconnect');

    // // API endpoint for status
    // Route::get('/api/github/status', [GitHubOAuthController::class, 'status'])
    //     ->name('github.status');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route::get('/terminal', function () {
    //     return Inertia::render('Terminal');
    // })->name('terminal');

    // // MCP Token Management Routes
    // Route::prefix('mcp-tokens')->name('mcp-tokens.')->middleware(['throttle:10,1'])->group(function () {
    //     Route::get('/', [McpTokenController::class, 'index'])->name('index');
    //     Route::post('/', [McpTokenController::class, 'store'])
    //         ->middleware('throttle:5,1') // More restrictive for token creation
    //         ->name('store');
    //     Route::delete('/{tokenId}', [McpTokenController::class, 'destroy'])->name('destroy');
    //     Route::get('/connection-info', [McpTokenController::class, 'connectionInfo'])->name('connection-info');
    // });

    // // Voice Assistant Routes
    // Route::prefix('voice-assistant')->name('voice-assistant.')->middleware(['throttle:20,1'])->group(function () {
    //     Route::get('/', [VoiceAssistantController::class, 'show'])->name('show');
    //     Route::post('/activate', [VoiceAssistantController::class, 'activate'])
    //         // ->middleware('throttle:3,1')
    //         ->name('activate');
    //     Route::post('/deactivate', [VoiceAssistantController::class, 'deactivate'])
    //         ->name('deactivate');
    //     Route::patch('/preferences', [VoiceAssistantController::class, 'updatePreferences'])
    //         ->name('preferences');
    //     Route::post('/transcribe', [VoiceAssistantController::class, 'transcribeToAssistant'])
    //         ->name('transcribe');
    // });

    // Route::group(['prefix' => 'clock-entry'], function () {
    //     Route::post('/store', [ClockEntryController::class, 'store'])->name('clock-entry.store');
    // });

    // Route::group(['prefix' => 'audio'], function () {
    //     Route::post('/transcribe', [AudioController::class, 'transcribe'])->name('audio.transcribe');
    //     Route::post('/command', [AudioController::class, 'command'])->name('audio.command');
    //     Route::post('/assistant', [VoiceAssistantController::class, 'transcribeToAssistant'])->name('audio.assistant');
    // });

    // Route::middleware([HandlePrecognitiveRequests::class])->prefix('projects')->group(function () {
    //     Route::get('/', [ProjectController::class, 'index'])->name('project.index');
    //     Route::get('/create', [ProjectController::class, 'create'])->name('project.create');
    //     Route::post('/store', [ProjectController::class, 'store'])->name('project.store');
    //     Route::get('/{project}', [ProjectController::class, 'show'])->name('project.show');
    //     Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    //     Route::patch('/{project}', [ProjectController::class, 'update'])->name('project.update');
    //     Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');
    // });

    // Route::middleware([HandlePrecognitiveRequests::class])->prefix('organizations')->group(function () {
    //     Route::get('/', [OrganizationController::class, 'index'])->name('organization.index');
    //     Route::get('/create', [OrganizationController::class, 'create'])->name('organization.create');
    //     Route::post('/store', [OrganizationController::class, 'store'])->name('organization.store');
    //     Route::get('/{organization}', [OrganizationController::class, 'show'])->name('organization.show');
    //     Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('organization.edit');
    //     Route::patch('/{organization}', [OrganizationController::class, 'update'])->name('organization.update');
    //     Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('organization.destroy');
    // });

    // Route::middleware([HandlePrecognitiveRequests::class])->prefix('activity-types')->group(function () {
    //     Route::get('/', [ActivityTypeController::class, 'index'])->name('activity-type.index');
    //     Route::get('/create', [ActivityTypeController::class, 'create'])->name('activity-type.create');
    //     Route::post('/store', [ActivityTypeController::class, 'store'])->name('activity-type.store');
    //     Route::get('/{activityType}', [ActivityTypeController::class, 'show'])->name('activity-type.show');
    //     Route::get('/{activityType}/edit', [ActivityTypeController::class, 'edit'])->name('activity-type.edit');
    //     Route::patch('/{activityType}', [ActivityTypeController::class, 'update'])->name('activity-type.update');
    //     Route::delete('/{activityType}', [ActivityTypeController::class, 'destroy'])->name('activity-type.destroy');
    // });

    // Route::middleware([HandlePrecognitiveRequests::class])->prefix('daily-logs')->group(function () {
    //     Route::patch('/{date}', [DailyLogController::class, 'update'])->name('daily-log.update');
    // });

    // Route::group(['prefix' => 'clock-entries'], function () {
    //     Route::get('/', [ClockEntryController::class, 'index'])->name('clock-entry.index');
    //     Route::get('/{clockEntry}', [ClockEntryController::class, 'show'])->name('clock-entry.show');
    //     Route::get('/{clockEntry}/edit', [ClockEntryController::class, 'edit'])->name('clock-entry.edit');
    //     Route::patch('/{clockEntry}', [ClockEntryController::class, 'update'])->name('clock-entry.update');
    //     Route::delete('/{clockEntry}', [ClockEntryController::class, 'destroy'])->name('clock-entry.destroy');
    // });
    Route::middleware([HandlePrecognitiveRequests::class])->prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('project.index');
        Route::get('/create', [ProjectController::class, 'create'])->name('project.create');
        Route::post('/', [ProjectController::class, 'store'])->name('project.store');
    });

    Route::middleware([HandlePrecognitiveRequests::class])->prefix('daily-logs')->group(function () {
        Route::post('/clock-in', [DailyLogController::class, 'clockIn'])->name('daily-log.clock-in');
        Route::post('/clock-out', [DailyLogController::class, 'clockOut'])->name('daily-log.clock-out');
        Route::post('/', [DailyLogController::class, 'store'])->name('daily-log.store');
    });

    // Timelog routes (alias for clock entries, used by frontend)
    Route::group(['prefix' => 'timelog'], function () {
        Route::put('/batch-update', [ClockEntryController::class, 'batchUpdate'])->name('timelog.batch-update');
        Route::delete('/{clockEntry}', [ClockEntryController::class, 'destroy'])->name('timelog.destroy');
    });

    // Route::group(['prefix' => 'rates'], function () {
    //     Route::get('/', [RateController::class, 'index'])->name('rate.index');
    //     Route::get('/create', [RateController::class, 'create'])->name('rate.create');
    //     Route::post('/store', [RateController::class, 'store'])->name('rate.store');
    //     Route::get('/{rate}', [RateController::class, 'show'])->name('rate.show');
    //     Route::get('/{rate}/edit', [RateController::class, 'edit'])->name('rate.edit');
    //     Route::patch('/{rate}', [RateController::class, 'update'])->name('rate.update');
    //     Route::delete('/{rate}', [RateController::class, 'destroy'])->name('rate.destroy');
    // });

    // Route::get('/quick-action', [QuickActionController::class, 'load'])->name('quick-action.load');

    // Route::prefix('quick')->group(function () {
    //     Route::get('/projects', [QuickActionController::class, 'projects'])->name('quick.projects');
    //     Route::get('/organizations', [QuickActionController::class, 'organizations'])->name('quick.organizations');
    //     Route::get('/rates', [QuickActionController::class, 'rates'])->name('quick.rates');
    //     Route::get('/activity-daily', [QuickActionController::class, 'daily'])->name('quick.daily');
    //     Route::get('/activity-monthly', [QuickActionController::class, 'monthly'])->name('quick.monthly');
    // });

    // Route::group(['prefix' => 'report'], function () {
    //     Route::get('/', [ReportController::class, 'index'])->name('report.index');
    //     Route::get('/create', [ReportController::class, 'create'])->name('report.create');
    //     Route::post('/store', [ReportController::class, 'store'])->name('report.store');
    //     Route::post('/generate', [ReportController::class, 'generate'])->name('report.generate');
    //     Route::post('/logs', [ReportController::class, 'fetchCommits'])->name('report.fetch-commits');
    //     Route::post('/bust-cache', [ReportController::class, 'bustCache'])->name('report.bust-cache');
    //     Route::get('/{report}', [ReportController::class, 'show'])->name('report.show');
    //     Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('report.edit');
    //     Route::patch('/{report}', [ReportController::class, 'update'])
    //         ->middleware(HandlePrecognitiveRequests::class)
    //         ->name('report.update');
    //     Route::delete('/{report}', [ReportController::class, 'destroy'])->name('report.destroy');
    // });

    // Route::group(['prefix' => 'activities'], function () {
    //     Route::get('/', [ActivityController::class, 'index'])->name('activities.index');
    //     Route::post('/store', [ActivityController::class, 'store'])->name('activities.store');
    //     Route::get('/{date}', [ActivityController::class, 'show'])->name('activities.show');
    //     Route::delete('/clock-entry/{clockEntry}', [ActivityController::class, 'deleteEntry'])->name('activities.delete-entry');
    // });

    // Route::group(['prefix' => 'activity-logs'], function () {
    //     Route::post('/store', [ActivityLogController::class, 'store'])->name('activity-logs.store');
    // });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/integrations', function () {
            $connected = Auth::user()->hasGitHubConnection();
            $tokenIsInvalid = $connected ? Auth::user()->gitHubConnection->isTokenExpired() : false;
            $githubStatus = [
                'connected' => $connected,
                'token_expired' => $connected ? Auth::user()->gitHubConnection->isTokenExpired() : false,
                'status' => $tokenIsInvalid ? 'invalid' : ($connected ? 'connected' : 'disconnected'),
                'username' => $connected ? Auth::user()->gitHubConnection->username : '',
                'connected_at' => $connected ? Auth::user()->gitHubConnection->created_at->toDateTimeString() : null,
            ];

            return Inertia::render('Settings/Integrations', compact('githubStatus'));
        })->name('settings.integrations');
    });

    Route::get('/download', function () {
        return Inertia::render('Download');
    })->name('my-downloads');

    // Route::group(['prefix' => 'profile'], function () {
    //     Route::post('/timezone', [ProfileController::class, 'updateTimezone'])->name('user.timezone.update');
    //     Route::patch('/preferences', [ProfileController::class, 'updatePreferences'])->name('user.preferences.update');
    // });
});

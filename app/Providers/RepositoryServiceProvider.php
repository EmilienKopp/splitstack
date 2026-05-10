<?php

namespace App\Providers;

use App\Domain\GitIntegration\Contracts\GitLogRepository;
use App\Domain\Identity\Contracts\OrganizationRepository;
use App\Domain\Identity\Contracts\UserRepository;
use App\Domain\Landlord\Contracts\TenantRepository;
use App\Domain\Monitoring\Contracts\MonitoredActivityRepository;
use App\Domain\Reporting\Contracts\ReportRepository;
use App\Domain\Repository\Contracts\RepositoryRepository;
use App\Domain\TimeTracking\Contracts\ActivityLogRepository;
use App\Domain\TimeTracking\Contracts\ClockEntryRepository;
use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Contracts\ProjectRepository;
use App\Domain\TimeTracking\Contracts\TaskRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentActivityLogRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentClockEntryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentDailyLogRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentGitLogRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentMonitoredActivityRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentOrganizationRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentProjectRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentReportRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRepositoryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentTaskRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentTenantRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClockEntryRepository::class, EloquentClockEntryRepository::class);
        $this->app->bind(RepositoryRepository::class, EloquentRepositoryRepository::class);
        $this->app->bind(ProjectRepository::class, EloquentProjectRepository::class);
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(ActivityLogRepository::class, EloquentActivityLogRepository::class);
        $this->app->bind(DailyLogRepository::class, EloquentDailyLogRepository::class);
        $this->app->bind(GitLogRepository::class, EloquentGitLogRepository::class);
        $this->app->bind(MonitoredActivityRepository::class, EloquentMonitoredActivityRepository::class);
        $this->app->bind(OrganizationRepository::class, EloquentOrganizationRepository::class);
        $this->app->bind(TenantRepository::class, EloquentTenantRepository::class);
        $this->app->bind(ReportRepository::class, EloquentReportRepository::class);
        $this->app->bind(TaskRepository::class, EloquentTaskRepository::class);
    }

    public function boot(): void {}
}

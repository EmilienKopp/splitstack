<?php

namespace App\Console\Commands\Crud;

use Illuminate\Console\Command;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Spatie\Multitenancy\Models\Tenant;

abstract class BaseTenantAwareCrudCommand extends Command
{
    use TenantAware;

    /**
     * Execute the console command with tenant context.
     */
    public function handle()
    {
        // Show which tenant we're operating in if available
        $currentTenant = Tenant::current();
        if ($currentTenant) {
            $this->info("🏢 Operating in tenant: {$currentTenant->name} ({$currentTenant->domain})");
            $user = auth()->user();
            if ($user) {
                $this->info("👤 Running as user: {$user->name}");
            }
            $this->newLine();
        }

        return $this->executeCommand();
    }

    /**
     * Execute the actual command logic. Override this in child classes.
     */
    abstract protected function executeCommand(): int;

    /**
     * Validate required arguments and show helpful error messages.
     */
    protected function validateArguments(array $required): bool
    {
        foreach ($required as $arg) {
            if (! $this->argument($arg)) {
                $this->error("❌ Missing required argument: {$arg}");

                return false;
            }
        }

        return true;
    }

    /**
     * Display model information in a formatted way.
     */
    protected function displayModel($model, string $title = 'Model Details'): void
    {
        $this->info("✅ {$title}:");
        $this->table(
            ['Field', 'Value'],
            collect($model->toArray())->map(fn ($value, $key) => [$key, $this->formatValue($value)])->toArray()
        );
    }

    /**
     * Format values for display.
     */
    private function formatValue($value): string
    {
        if (is_null($value)) {
            return '<null>';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Show operation success with helpful information.
     */
    protected function showSuccess(string $operation, $model): void
    {
        $modelName = class_basename($model);
        $this->info("🎉 {$operation} successful!");
        $this->displayModel($model, "{$modelName} {$operation}");
    }
}

<?php

namespace App\Console\Commands\Crud;

use App\Models\Organization;
use App\Models\User;

class ProjectCreateCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:create {name : The project name} 
      {--description= : Project description} 
      {--organization= : Organization ID (optional)} 
      {--status=active : Project status (active, inactive, completed, on_hold)} 
      {--type=client : Project type (client, internal, personal)} 
      {--userId= : User ID to associate with the project} 
      {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new project within the current tenant';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        if (! $this->validateArguments(['name'])) {
            return self::FAILURE;
        }

        $name = $this->argument('name');
        $description = $this->option('description');
        $organizationId = $this->option('organization');
        $status = $this->option('status');
        $type = $this->option('type');
        $userId = $this->option('userId');

        // Validate organization if provided
        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if (! $organization) {
                $this->error("❌ Organization with ID {$organizationId} not found.");

                return self::FAILURE;
            }
            $this->info("🏢 Using organization: {$organization->name}");
        }

        // Validate user if provided
        if ($userId) {
            $user = User::find($userId);
            if (! $user) {
                $this->error("❌ User with ID {$userId} not found.");

                return self::FAILURE;
            }
            $this->info("👤 Using user: {$user->name} ({$user->email})");
        }

        // Validate status
        if (! in_array($status, ['active', 'inactive', 'completed', 'on_hold'])) {
            $this->error('❌ Invalid status. Must be one of: active, inactive, completed, on_hold');

            return self::FAILURE;
        }

        // Validate type
        if (! in_array($type, ['client', 'internal', 'personal'])) {
            $this->error('❌ Invalid type. Must be one of: client, internal, personal');

            return self::FAILURE;
        }

        try {
            $project = auth('tenant')->user()->projects()->create([
                'name' => $name,
                'description' => $description,
                'organization_id' => $organizationId,
                'status' => $status,
                'type' => $type,
            ]);

            $this->showSuccess('Project created', $project);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create project: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}

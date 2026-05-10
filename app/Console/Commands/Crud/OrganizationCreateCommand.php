<?php

namespace App\Console\Commands\Crud;

use App\Models\Organization;

class OrganizationCreateCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'organization:create 
                            {name : The organization name}
                            {--description= : Organization description}
                            {--type=company : Organization type}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new organization within the current tenant';

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
        $type = $this->option('type');

        try {
            $organization = Organization::create([
                'name' => $name,
                'description' => $description,
                'type' => $type,
            ]);

            $this->showSuccess('Organization created', $organization);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create organization: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}

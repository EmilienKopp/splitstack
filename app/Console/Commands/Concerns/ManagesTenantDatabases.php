<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait ManagesTenantDatabases
{
    protected function databaseExists(string $name): bool
    {
        $result = DB::connection('landlord')->select(
            'SELECT 1 FROM pg_database WHERE datname = ?',
            [$name]
        );

        return ! empty($result);
    }

    protected function createDatabase(string $name): void
    {
        DB::connection('landlord')->statement("CREATE DATABASE \"{$name}\"");
    }

    protected function dropDatabase(string $name): void
    {
        $this->terminateConnections($name);
        DB::connection('landlord')->statement("DROP DATABASE IF EXISTS \"{$name}\"");
    }

    protected function duplicateFromTemplate(string $targetDatabase): void
    {
        $this->terminateConnections('tenant_template');

        DB::connection('landlord')->statement(
            "CREATE DATABASE \"{$targetDatabase}\" WITH TEMPLATE \"tenant_template\""
        );
    }

    protected function terminateConnections(string $database): void
    {
        DB::connection('landlord')->statement(
            'SELECT pg_terminate_backend(pg_stat_activity.pid)
             FROM pg_stat_activity
             WHERE pg_stat_activity.datname = ?
             AND pid <> pg_backend_pid()',
            [$database]
        );
    }

    protected function deriveHostFromDomain(string $domain): string
    {
        return str($domain)->before('.')->toString();
    }

    protected function deriveDatabaseFromHost(string $host): string
    {
        return $host.'_db';
    }

    protected function configureTenantConnection(string $database): void
    {
        Config::set('database.connections.tenant.database', $database);
        DB::purge('tenant');
    }
}

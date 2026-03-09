<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class TenantDatabaseService
{
    /**
     * Create a new database for the tenant.
     */
    public static function createDatabase($dbName)
    {
        return DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
    }

    /**
     * Switch to the tenant database.
     */
    public static function switchToTenant($dbName)
    {
        // Set the database name for the tenant connection
        Config::set('database.connections.tenant.database', $dbName);
        
        // Purge the connection to ensure the new database is used
        DB::purge('tenant');
        
        // Reconnect
        DB::reconnect('tenant');
        
        // Set as default connection for the current request
        DB::setDefaultConnection('tenant');
    }

    /**
     * Run migrations on the tenant database.
     */
    public static function runTenantMigrations()
    {
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    /**
     * Delete a tenant database.
     */
    public static function deleteDatabase($dbName)
    {
        return DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
    }
}

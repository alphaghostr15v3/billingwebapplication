<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MigrateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for all tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businesses = Business::all();
        $this->info("Found " . $businesses->count() . " tenants.");

        foreach ($businesses as $business) {
            $this->info("Migrating tenant: {$business->business_name} ({$business->database_name})");
            
            try {
                TenantDatabaseService::switchToTenant($business->database_name);
                
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
                
                $this->info(Artisan::output());
            } catch (\Exception $e) {
                $this->error("Failed to migrate {$business->business_name}: " . $e->getMessage());
            }
        }

        $this->info("Tenant migrations completed.");
    }
}

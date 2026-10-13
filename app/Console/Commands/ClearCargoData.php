<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearCargoData extends Command
{
    protected $signature = 'cargo:clear-data {--force : Skip confirmation}';

    protected $description = 'Clear cargo transactional data while keeping tables, admin user, roles, and permissions';

    /**
     * Tables cleared in FK-safe order (children first).
     */
    protected array $tables = [
        'drop_off_parcels',
        'parcel_car_assignments',
        'pacel_histories',
        'customer_pacels',
        'tempolary_pacels',
        'package_loads',
        'users_cars_selection',
        'car_history',
        'driver_routes',
        'drop_off_points',
        'cars',
        'drivers',
        'delivery_agents',
        'customers',
        'mikoa',
        'sms',
        'document_settings',
    ];

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This deletes all cargo data but keeps users, roles, and company settings. Continue?')) {
            $this->info('Cancelled.');
            return 0;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)->truncate();
            $this->line("Cleared: {$table}");
        }

        // Remove driver/delivery-agent users; keep admin and staff.
        if (Schema::hasTable('users') && Schema::hasTable('users_roles')) {
            $driverUserIds = DB::table('users')
                ->where('email', '!=', 'admin@gmail.com')
                ->where(function ($q) {
                    $q->where('is_driver', 1)->orWhere('is_delivery_agent', 1);
                })
                ->pluck('id');

            if ($driverUserIds->isNotEmpty()) {
                DB::table('users_roles')->whereIn('user_id', $driverUserIds)->delete();
                DB::table('users')->whereIn('id', $driverUserIds)->delete();
                $this->line('Removed driver/delivery-agent user accounts.');
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Cargo data cleared. Admin login and permissions are unchanged.');
        $this->comment('Tip: use "php artisan cargo:clear-data" next time instead of "migrate:fresh".');

        return 0;
    }
}

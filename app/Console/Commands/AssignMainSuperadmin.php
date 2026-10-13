<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class AssignMainSuperadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:assign-main-superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign admin@gmail.com to company_id 1 and reassign other users with company_id 1 to new companies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting main superadmin assignment...');

        // Step 1: Ensure company with id=1 exists
        $mainCompany = Company::find(1);
        if (!$mainCompany) {
            $this->info('Creating main company (id=1)...');
            $mainCompany = Company::create([
                'cname' => 'Main SaaS Company',
                'organization' => 'Main SaaS Company',
                'email' => 'admin@gmail.com',
                'contactName' => 'Main Administrator',
            ]);
            $this->info('Main company created with id: ' . $mainCompany->id);
        } else {
            $this->info('Main company (id=1) already exists');
        }

        $mainCompany->is_platform = true;
        $mainCompany->is_disabled = false;
        $mainCompany->save();
        $this->info('Main company marked as platform company');

        // Step 2: Set admin@gmail.com user's company_id to 1
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        if ($adminUser) {
            $oldCompanyId = $adminUser->company_id;
            $adminUser->company_id = 1;
            $adminUser->save();
            $this->info("Admin user (admin@gmail.com) assigned to company_id 1 (was: {$oldCompanyId})");
        } else {
            $this->warn('Admin user (admin@gmail.com) not found!');
            return 1;
        }

        // Step 3: Find all other users with company_id = 1
        $usersToReassign = User::where('company_id', 1)
            ->where('email', '!=', 'admin@gmail.com')
            ->get();

        if ($usersToReassign->count() == 0) {
            $this->info('No other users found with company_id = 1. All done!');
            return 0;
        }

        $this->info("Found {$usersToReassign->count()} user(s) to reassign...");

        // Step 4: Reassign each user to a new company
        $bar = $this->output->createProgressBar($usersToReassign->count());
        $bar->start();

        foreach ($usersToReassign as $user) {
            // Create a new company for this user
            $newCompany = Company::create([
                'cname' => $user->name . ' Company',
                'organization' => $user->name . ' Company',
                'email' => $user->email,
                'contactName' => $user->name,
            ]);

            $this->line("\nProcessing user: {$user->name} ({$user->email})");
            $this->line("Created new company: {$newCompany->cname} (id: {$newCompany->id})");

            // Update user's company_id
            $user->company_id = $newCompany->id;
            $user->save();

            // Update all related data for this user
            $this->updateRelatedData($user, $newCompany->id);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All users have been reassigned successfully!');

        // Verification
        $remainingUsers = User::where('company_id', 1)
            ->where('email', '!=', 'admin@gmail.com')
            ->count();

        if ($remainingUsers == 0) {
            $this->info('✓ Verification passed: Only admin@gmail.com has company_id = 1');
        } else {
            $this->warn("⚠ Warning: {$remainingUsers} user(s) still have company_id = 1");
        }

        return 0;
    }

    /**
     * Update all related data for a user to use the new company_id
     *
     * @param User $user
     * @param int $newCompanyId
     * @return void
     */
    private function updateRelatedData(User $user, int $newCompanyId)
    {
        $updated = 0;

        // Update customers
        $count = DB::table('customers')
            ->where('added_by', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update customer_pacels
        $count = DB::table('customer_pacels')
            ->where('added_by', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update pacel_histories
        $count = DB::table('pacel_histories')
            ->where('added_by', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update roles
        $count = DB::table('roles')
            ->where('added_by', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update package_loads
        $count = DB::table('package_loads')
            ->where('user_id', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update driver_routes
        $count = DB::table('driver_routes')
            ->where('user_id', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update users_cars_selection
        $count = DB::table('users_cars_selection')
            ->where('user_id', $user->id)
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update cars that belong to this user
        $count = DB::table('cars')
            ->whereIn('id', function ($query) use ($user) {
                $query->select('car_id')
                    ->from('users_cars_selection')
                    ->where('user_id', $user->id);
            })
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        // Update drivers that belong to cars of this user
        $count = DB::table('drivers')
            ->whereIn('id', function ($query) use ($newCompanyId) {
                $query->select('driver_id')
                    ->from('cars')
                    ->where('company_id', $newCompanyId);
            })
            ->where('company_id', 1)
            ->update(['company_id' => $newCompanyId]);
        $updated += $count;

        $this->line("  Updated {$updated} related records");
    }
}


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;

class AssignMainSuperadminAndReassignUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Ensure company with id=1 exists, if not create it
        $mainCompany = Company::find(1);
        if (!$mainCompany) {
            $mainCompany = Company::create([
                'cname' => 'Main SaaS Company',
                'organization' => 'Main SaaS Company',
                'email' => 'admin@gmail.com',
                'contactName' => 'Main Administrator',
            ]);
        }

        // Step 2: Set admin@gmail.com user's company_id to 1
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        if ($adminUser) {
            $adminUser->company_id = 1;
            $adminUser->save();
        }

        // Step 3: Find all other users with company_id = 1 (except admin@gmail.com)
        $usersToReassign = User::where('company_id', 1)
            ->where('email', '!=', 'admin@gmail.com')
            ->get();

        // Step 4: Reassign each user to a new company
        foreach ($usersToReassign as $user) {
            // Create a new company for this user
            $newCompany = Company::create([
                'cname' => $user->name . ' Company',
                'organization' => $user->name . ' Company',
                'email' => $user->email,
                'contactName' => $user->name,
            ]);

            $newCompanyId = $newCompany->id;

            // Update user's company_id
            $user->company_id = $newCompanyId;
            $user->save();

            // Update all related data for this user to use the new company_id
            // Update customers
            DB::table('customers')
                ->where('added_by', $user->id)
                ->where('company_id', 1)
                ->update(['company_id' => $newCompanyId]);

            // Update customer_pacels
            DB::table('customer_pacels')
                ->where('added_by', $user->id)
                ->where('company_id', 1)
                ->update(['company_id' => $newCompanyId]);

            // Update pacel_histories
            DB::table('pacel_histories')
                ->where('added_by', $user->id)
                ->where('company_id', 1)
                ->update(['company_id' => $newCompanyId]);

            // Update cars
            DB::table('cars')
                ->where('company_id', 1)
                ->whereExists(function ($query) use ($user) {
                    $query->select(DB::raw(1))
                        ->from('users_cars_selection')
                        ->whereColumn('users_cars_selection.car_id', 'cars.id')
                        ->where('users_cars_selection.user_id', $user->id);
                })
                ->update(['company_id' => $newCompanyId]);

            // Update drivers
            DB::table('drivers')
                ->where('company_id', 1)
                ->whereExists(function ($query) use ($user, $newCompanyId) {
                    $query->select(DB::raw(1))
                        ->from('cars')
                        ->whereColumn('cars.driver_id', 'drivers.id')
                        ->where('cars.company_id', $newCompanyId);
                })
                ->update(['company_id' => $newCompanyId]);

            // Update roles
            DB::table('roles')
                ->where('added_by', $user->id)
                ->where('company_id', 1)
                ->update(['company_id' => $newCompanyId]);

            // Update package_loads
            DB::table('package_loads')
                ->where('company_id', 1)
                ->where('user_id', $user->id)
                ->update(['company_id' => $newCompanyId]);

            // Update driver_routes
            DB::table('driver_routes')
                ->where('company_id', 1)
                ->where('user_id', $user->id)
                ->update(['company_id' => $newCompanyId]);

            // Update users_cars_selection
            DB::table('users_cars_selection')
                ->where('company_id', 1)
                ->where('user_id', $user->id)
                ->update(['company_id' => $newCompanyId]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be easily reversed as it creates new companies
        // If you need to reverse, you would need to manually reassign users back
        // For safety, we'll leave this empty
    }
}


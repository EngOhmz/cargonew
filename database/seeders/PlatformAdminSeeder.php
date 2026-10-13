<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlatformAdminSeeder extends Seeder
{
    /**
     * Mark platform company and assign platform_admin role to main admin.
     */
    public function run()
    {
        $user = User::where('email', 'admin@gmail.com')->first();
        if (!$user) {
            return;
        }

        $company = Company::find($user->company_id ?? 1);
        if ($company) {
            $company->is_platform = true;
            $company->is_disabled = false;
            $company->save();
        }

        $platformRole = Role::firstOrCreate(
            [
                'slug' => 'platform_admin',
                'company_id' => $company ? $company->id : 1,
            ],
            [
                'added_by' => $user->id,
            ]
        );

        $permissionIds = Permission::pluck('id')->all();
        if (!empty($permissionIds)) {
            $platformRole->permissions()->sync($permissionIds);
        }

        if (!$user->roles()->where('roles.id', $platformRole->id)->exists()) {
            $user->roles()->attach($platformRole->id);
        }
    }
}

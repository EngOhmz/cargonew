<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Assign superadmin role with full module access to the default admin user.
     */
    public function run()
    {
        $user = User::where('email', 'admin@gmail.com')->first();
        if (!$user) {
            return;
        }

        $role = Role::firstOrCreate(
            [
                'slug' => 'superadmin',
                'company_id' => $user->company_id ?? 1,
            ],
            [
                'added_by' => $user->id,
            ]
        );

        $permissionIds = Permission::pluck('id')->all();
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        if (!$user->roles()->where('roles.id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AttachSmsManagementPermissionToAdminRoles extends Migration
{
    public function up()
    {
        $moduleId = DB::table('sys_modules')->where('slug', 'SMS Management')->whereNull('parent_id')->value('id');
        if (!$moduleId) {
            return;
        }

        $permissionId = DB::table('permissions')
            ->where('sys_module_id', $moduleId)
            ->where('slug', 'access')
            ->value('id');

        if (!$permissionId) {
            return;
        }

        $roleIds = DB::table('roles')
            ->whereRaw('LOWER(slug) IN (?, ?)', ['superadmin', 'platform_admin'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('roles_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists) {
                DB::table('roles_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down()
    {
        // Keep assignments; safe no-op.
    }
}

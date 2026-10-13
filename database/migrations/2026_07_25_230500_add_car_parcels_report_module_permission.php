<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCarParcelsReportModulePermission extends Migration
{
    public function up()
    {
        $reportsParentId = DB::table('sys_modules')
            ->where('slug', 'Reports')
            ->whereNull('parent_id')
            ->value('id');

        if (!$reportsParentId) {
            $reportsParentId = DB::table('sys_modules')->insertGetId([
                'slug' => 'Reports',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $moduleId = DB::table('sys_modules')
            ->where('slug', 'Car Parcels Report')
            ->where('parent_id', $reportsParentId)
            ->value('id');

        if (!$moduleId) {
            $moduleId = DB::table('sys_modules')->insertGetId([
                'slug' => 'Car Parcels Report',
                'parent_id' => $reportsParentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (['create', 'read', 'update', 'delete'] as $slug) {
            $exists = DB::table('permissions')
                ->where('sys_module_id', $moduleId)
                ->where('slug', $slug)
                ->exists();

            if (!$exists) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'sys_module_id' => $moduleId,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $permissionId = DB::table('permissions')
                    ->where('sys_module_id', $moduleId)
                    ->where('slug', $slug)
                    ->value('id');
            }

            if ($slug !== 'read' || !$permissionId) {
                continue;
            }

            // Attach read permission to company superadmin roles
            $roleIds = DB::table('roles')
                ->whereRaw('LOWER(slug) IN (?, ?)', ['superadmin', 'platform_admin'])
                ->pluck('id');

            foreach ($roleIds as $roleId) {
                $linked = DB::table('roles_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$linked) {
                    DB::table('roles_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $moduleId = DB::table('sys_modules')->where('slug', 'Car Parcels Report')->value('id');
        if (!$moduleId) {
            return;
        }

        $permissionIds = DB::table('permissions')->where('sys_module_id', $moduleId)->pluck('id');
        if ($permissionIds->isNotEmpty()) {
            DB::table('roles_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }
        DB::table('sys_modules')->where('id', $moduleId)->delete();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemModule;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing modules and permissions
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        SystemModule::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menus = [
            [
                'name' => 'Dashboard',
                'submenus' => []
            ],
            [
                'name' => 'Registration',
                'submenus' => ['Sajili Wateja & Mizigo']
            ],
            [
                'name' => 'Cargo Management',
                'submenus' => ['List ya Mizigo']
            ],
            [
                'name' => 'Sajili mikoa',
                'submenus' => []
            ],
            [
                'name' => 'Fleet Management',
                'submenus' => ['All Cars', 'Drivers', 'Routes & Drop-off Points']
            ],
            [
                'name' => 'Delivery Agents',
                'submenus' => ['Delivery Agent List', 'New Delivery Agent']
            ],
            [
                'name' => 'Reports',
                'submenus' => ['Revenue Report', 'Car Parcels Report']
            ],
            [
                'name' => 'Store records',
                'submenus' => []
            ],
            [
                'name' => 'Access Control',
                'submenus' => [
                    'User Management', 
                    'Change Password', 
                    'Role', 
                    'Upload Company Logo', 
                    'Edit document', 
                    'Permission', 
                    'Registered Companies'
                ]
            ],
            [
                'name' => 'Mobile Settings',
                'submenus' => ['App Details']
            ],
            [
                'name' => 'SMS Management',
                'submenus' => []
            ],
        ];

        foreach ($menus as $menu) {
            $parent = SystemModule::create([
                'slug' => $menu['name'],
                'parent_id' => null,
            ]);

            if (empty($menu['submenus'])) {
                // If no submenus, the parent itself gets an "access" permission
                Permission::create([
                    'sys_module_id' => $parent->id,
                    'slug' => 'access',
                ]);
            } else {
                // If has submenus, each submenu gets CRUD permissions
                foreach ($menu['submenus'] as $submenuName) {
                    $child = SystemModule::create([
                        'slug' => $submenuName,
                        'parent_id' => $parent->id,
                    ]);

                    $crud = ['create', 'read', 'update', 'delete'];
                    foreach ($crud as $action) {
                        Permission::create([
                            'sys_module_id' => $child->id,
                            'slug' => $action,
                        ]);
                    }
                }
            }
        }
    }
}

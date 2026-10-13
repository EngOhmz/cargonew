<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
       
       
     
         
        $data = [
            #1. manage-dashboard permissions
            ['slug' => 'view-role','sys_module_id'=>1],
            ['slug' => 'edit-role','sys_module_id'=>1],
            ['slug' => 'delete-role','sys_module_id'=>1],
            ['slug' => 'add-role','sys_module_id'=>1],
            ['slug' => 'manage-department','sys_module_id'=>1],
            ['slug' => 'approve-payment','sys_module_id'=>2],
       ];

         foreach ($data as $row) {
            Permission::firstOrCreate($row);
         }
    }
}

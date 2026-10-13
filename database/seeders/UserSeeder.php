<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'cname' => 'Madekenya Cargo',
                'organization' => 'Madekenya Cargo',
                'contactName' => 'Main Administrator',
                'is_platform' => true,
            ]
        );

        if (!$company->is_platform) {
            $company->is_platform = true;
            $company->save();
        }

        if (empty($company->app_name)) {
            $company->app_name = 'Madekenya';
            $company->save();
        }

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'superadmin',
                'last_ip' => '0',
                'last_login' => now(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'status' => '1',
                'is_active' => '1',
                'company_id' => $company->id,
            ]
        );
    }
}

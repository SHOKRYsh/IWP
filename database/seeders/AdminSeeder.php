<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $permissions = [];
        
        $UserRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        $AdminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $AdminRole->syncPermissions($permissions);

        $superAdminUser = User::updateOrCreate([
            'email' => 'shokrymansor123@gmail.com',
        ], [
            'name' => 'shokry',
            'phone' => '01014001055',
            'password' => bcrypt('123456789'),
            'gender' => 'male',
        ]);

        $superAdminUser->assignRole($AdminRole);
    }

}
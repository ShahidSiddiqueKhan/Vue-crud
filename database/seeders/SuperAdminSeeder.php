<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
   
    public function run(): void
    {
       
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        
        $superAdmin = User::firstOrCreate(
            ['email' => 'shahidiiui372@gmail.com'], 
            [
                'name' => 'Super Admin',
                'password' => bcrypt('12345'), 
            ]
        );

        
        $superAdmin->assignRole($superAdminRole);

        echo "Super Admin created successfully.\n";
    }
}

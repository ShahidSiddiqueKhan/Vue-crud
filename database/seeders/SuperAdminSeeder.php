<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the 'super-admin' role exists
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Create the Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'shahidiiui372@gmail.com'], // Check if user already exists
            [
                'name' => 'Super Admin',
                'password' => bcrypt('12345'), // Change to a more secure password in production
            ]
        );

        // Assign Super Admin role
        $superAdmin->assignRole($superAdminRole);

        echo "Super Admin created successfully.\n";
    }
}

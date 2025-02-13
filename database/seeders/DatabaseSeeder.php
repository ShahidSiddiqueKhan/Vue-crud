<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Create Shahid and assign both roles
        $shahid = User::updateOrCreate(
            ['email' => 'shahidiiui372@gmail.com'],
            [
                'name' => 'Shahid',
                'password' => bcrypt('12345678'),
            ]
        );

        // Assign multiple roles
        $shahid->syncRoles(['admin', 'super-admin']);

        $this->command->info("✅ Shahid is now an Admin & Super Admin!");
    }
}


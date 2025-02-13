<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  
    public function run(): void
    {
        // Check if the user already exists
        $user = User::where('email', 'shahidiiui372@gmail.com')->first();

        if (!$user) {
            // Create a new admin user
            $user = User::create([
                'name' => 'Shahid Admin',
                'email' => 'shahidiiui372@gmail.com',
                'password' => Hash::make('12345678'),
            ]);
        }

        // Assign the admin or super admin role (if you're using Spatie Roles)
        if ($user->hasRole('admin') === false) {
            $user->assignRole('super-admin'); // Change to 'admin' if needed
        }
    }
}

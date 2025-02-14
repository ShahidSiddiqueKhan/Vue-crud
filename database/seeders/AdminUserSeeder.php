<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  
    public function run(): void
    {
        
        $user = User::where('email', 'shahidiiui372@gmail.com')->first();

        if (!$user) {
            
            $user = User::create([
                'name' => 'Shahid Admin',
                'email' => 'shahidiiui372@gmail.com',
                'password' => Hash::make('12345678'),
            ]);
        }

        
        if ($user->hasRole('admin') === false) {
            $user->assignRole('super-admin'); 
        }
    }
}

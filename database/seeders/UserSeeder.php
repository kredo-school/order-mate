<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Manager
        User::create([
            'name' => 'Test3',
            'email' => 'test3@gmail.com',
            'password' => Hash::make('33333333'),
            'role' => 1, // Manager
        ]);

        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 2, // Admin
        ]);
    }
}

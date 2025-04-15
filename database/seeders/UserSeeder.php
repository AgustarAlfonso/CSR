<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => 'password', // Otomatis di-hash karena cast
            'role' => 1,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2,
        ]);

        User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => 3,
        ]);
    }
}

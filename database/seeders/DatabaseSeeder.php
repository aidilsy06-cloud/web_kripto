<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin User
        User::create([
            'name' => 'Administrator TulipCrypt',
            'email' => 'admin@tulip.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'admin',
            'is_verified' => true,
        ]);

        // Default Regular User
        User::create([
            'name' => 'Tulip User',
            'email' => 'user@tulip.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'user',
            'is_verified' => true,
        ]);
    }
}

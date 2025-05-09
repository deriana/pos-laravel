<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // jangan gunakan plain text
            'phone_number' => '081234567890',
            'address' => 'Jalan Contoh No. 1',
            'role' => 'admin',
            'verification_token' => Str::random(60),
            'is_verified' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => Hash::make('password'),
            'role' => 'Customer',
            'contact_number' => '1234567890',
            'status' => 'Active'
        ]);
    }
}
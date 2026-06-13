<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Mentor User',
            'email' => 'mentor@womenhub.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'role' => 'mentor'
        ]);
    }
}

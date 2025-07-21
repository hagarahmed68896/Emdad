<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'full_name' => 'Admin',
                'password' => Hash::make('123456789A'),
                'account_type' => 'admin',
            ]
        );
    }
}

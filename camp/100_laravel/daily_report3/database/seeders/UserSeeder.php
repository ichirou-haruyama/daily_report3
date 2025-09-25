<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('Pass!word-1234'),
                'email_verified_at' => now(),
            ]
        );

        // メンバー
        User::updateOrCreate(
            ['email' => 'member01@example.com'],
            [
                'name' => 'Member 01',
                'email' => 'member01@example.com',
                'password' => Hash::make('Pass!word-1234'),
                'email_verified_at' => now(),
            ]
        );
    }
}

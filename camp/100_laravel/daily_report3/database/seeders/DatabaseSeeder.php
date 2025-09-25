<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MemoSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\VehicleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 既定ユーザー・メモのサンプルデータを投入
        $this->call([
            UserSeeder::class,
            MemoSeeder::class,
            VehicleSeeder::class,
        ]);
    }
}

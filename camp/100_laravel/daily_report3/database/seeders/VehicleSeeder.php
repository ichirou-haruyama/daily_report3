<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'ハイエース',
            '4tユニック',
            '乗用車',
            '軽自動車',
        ];

        foreach ($defaults as $name) {
            Vehicle::updateOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}

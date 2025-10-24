<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'plate_number' => 'B1234ABC',
                'brand' => 'Toyota',
                'model' => 'Avanza',
                'year' => 2020,
                'type' => 'MPV',
                'current_odometer' => 45000,
                'status' => 'active',
                'notes' => 'Company vehicle for general use',
            ],
            [
                'plate_number' => 'B5678DEF',
                'brand' => 'Honda',
                'model' => 'Civic',
                'year' => 2019,
                'type' => 'Sedan',
                'current_odometer' => 65000,
                'status' => 'active',
                'notes' => 'Executive vehicle',
            ],
            [
                'plate_number' => 'B9012GHI',
                'brand' => 'Mitsubishi',
                'model' => 'L300',
                'year' => 2018,
                'type' => 'Pickup',
                'current_odometer' => 80000,
                'status' => 'maintenance',
                'notes' => 'Delivery vehicle',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}

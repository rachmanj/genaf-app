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
                'unit_no' => 'VA 045',
                'nomor_polisi' => 'B 1234 ABC',
                'brand' => 'Toyota',
                'model' => 'Avanza 1.3 G',
                'year' => 2021,
                'plant_group' => 'Light Vehicles',
                'current_odometer' => 45000,
                'status' => 'active',
                'current_project_code' => '000H',
                'remarks' => 'Company vehicle for general use',
                'arkfleet_synced_at' => now()->subDays(3),
                'arkfleet_sync_status' => 'synced',
            ],
            [
                'unit_no' => 'VA 046',
                'nomor_polisi' => 'B 5678 DEF',
                'brand' => 'Honda',
                'model' => 'BRV Prestige',
                'year' => 2020,
                'plant_group' => 'Light Vehicles',
                'current_odometer' => 61200,
                'status' => 'active',
                'current_project_code' => '015C',
                'remarks' => 'Executive vehicle assigned to management',
                'arkfleet_synced_at' => now()->subDay(),
                'arkfleet_sync_status' => 'imported',
            ],
            [
                'unit_no' => 'TR 201',
                'nomor_polisi' => 'B 9012 GHI',
                'brand' => 'Mitsubishi',
                'model' => 'L300',
                'year' => 2018,
                'plant_group' => 'Truck',
                'current_odometer' => 87550,
                'status' => 'maintenance',
                'current_project_code' => '017C',
                'remarks' => 'Delivery vehicle awaiting inspection',
                'arkfleet_synced_at' => now()->subDays(10),
                'arkfleet_sync_status' => 'missing',
                'arkfleet_sync_message' => 'Unit not returned by latest sync; flagged for review.',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}

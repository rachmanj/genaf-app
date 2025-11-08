<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_no' => 'VA ' . $this->faker->unique()->numberBetween(100, 999),
            'nomor_polisi' => strtoupper($this->faker->bothify('KB #### ??')),
            'brand' => $this->faker->company(),
            'model' => $this->faker->randomElement(['Hilux', 'Avanza', 'Fortuner', 'Kijang', 'Daihatsu Xenia']),
            'year' => $this->faker->numberBetween(2010, now()->year),
            'plant_group' => $this->faker->randomElement(['Light Vehicles', 'Truck', 'Motorbike']),
            'current_odometer' => $this->faker->numberBetween(500, 250000),
            'status' => $this->faker->randomElement(['active', 'maintenance', 'retired']),
            'current_project_code' => strtoupper($this->faker->bothify('0##?')),
            'remarks' => $this->faker->optional()->sentence(),
            'arkfleet_synced_at' => now(),
            'arkfleet_sync_status' => 'synced',
            'arkfleet_sync_message' => null,
            'arkfleet_last_payload' => [],
            'is_active' => true,
        ];
    }
}

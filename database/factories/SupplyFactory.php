<?php

namespace Database\Factories;

use App\Models\Supply;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplyFactory extends Factory
{
    protected $model = Supply::class;

    public function definition(): array
    {
        $categories = ['ATK', 'Cleaning', 'Pantry', 'IT', 'Office', 'Other'];
        $units = ['pcs', 'box', 'pack', 'rim', 'roll', 'bottle', 'kg', 'liter', 'meter'];

        return [
            'code' => 'SUP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement($categories),
            'unit' => $this->faker->randomElement($units),
            'current_stock' => $this->faker->numberBetween(0, 500),
            'min_stock' => $this->faker->numberBetween(5, 50),
            'price' => $this->faker->numberBetween(1000, 100000),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}


<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleDocumentType>
 */
class VehicleDocumentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = strtoupper($this->faker->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->unique()->lexify('type ???')),
            'default_validity_days' => $this->faker->numberBetween(90, 730),
            'default_reminder_days' => 90,
            'description' => $this->faker->sentence(),
        ];
    }
}

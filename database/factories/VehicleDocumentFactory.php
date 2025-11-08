<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleDocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleDocument>
 */
class VehicleDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $dueDate = (clone $documentDate)->modify('+1 year');

        return [
            'vehicle_id' => Vehicle::factory(),
            'vehicle_document_type_id' => VehicleDocumentType::factory(),
            'document_number' => strtoupper($this->faker->bothify('DOC-#####')),
            'document_date' => $documentDate,
            'due_date' => $dueDate,
            'supplier' => $this->faker->optional()->company(),
            'amount' => $this->faker->optional()->randomFloat(2, 50000, 2000000),
            'file_path' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

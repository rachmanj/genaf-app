<?php

namespace Database\Seeders;

use App\Models\VehicleDocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'STNK',
                'slug' => Str::slug('STNK'),
                'default_validity_days' => 365,
                'default_reminder_days' => 90,
                'description' => 'Vehicle registration certificate; typically renewed annually.',
            ],
            [
                'name' => 'KIR',
                'slug' => Str::slug('KIR'),
                'default_validity_days' => 180,
                'default_reminder_days' => 90,
                'description' => 'Periodic roadworthiness inspection; commonly renewed every six months.',
            ],
        ];

        VehicleDocumentType::query()->upsert(
            $types,
            ['slug'],
            ['name', 'default_validity_days', 'default_reminder_days', 'description']
        );
    }
}

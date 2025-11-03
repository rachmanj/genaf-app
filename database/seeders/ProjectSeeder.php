<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['code' => '000H', 'owner' => 'HO Balikpapan', 'location' => 'Balikpapan', 'is_active' => true],
            ['code' => '001H', 'owner' => 'HO Jakarta', 'location' => 'Jakarta', 'is_active' => true],
            ['code' => '017C', 'owner' => 'KPUC', 'location' => 'Malinau', 'is_active' => true],
            ['code' => '021C', 'owner' => 'SBI', 'location' => 'Bogor', 'is_active' => true],
            ['code' => '022C', 'owner' => 'GPK', 'location' => 'Melak', 'is_active' => true],
            ['code' => '023C', 'owner' => 'TRUST', 'location' => 'Melak', 'is_active' => true],
            ['code' => '025C', 'owner' => 'SBI', 'location' => 'Cilacap', 'is_active' => true],
            ['code' => 'APS', 'owner' => 'APS', 'location' => 'Kariangau', 'is_active' => true],
            ['code' => '005P', 'owner' => 'Pratasaba', 'location' => 'Maratua', 'is_active' => true],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['code' => $project['code']],
                $project
            );
        }
    }
}

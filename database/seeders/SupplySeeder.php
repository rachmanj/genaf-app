<?php

namespace Database\Seeders;

use App\Models\Supply;
use Illuminate\Database\Seeder;

class SupplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplies = [
            [
                'code' => 'ATK001',
                'name' => 'Ballpoint Pen',
                'category' => 'ATK',
                'unit' => 'pcs',
                'current_stock' => 100,
                'min_stock' => 20,
                'price' => 2500,
                'description' => 'Blue ballpoint pen',
            ],
            [
                'code' => 'ATK002',
                'name' => 'A4 Paper',
                'category' => 'ATK',
                'unit' => 'ream',
                'current_stock' => 50,
                'min_stock' => 10,
                'price' => 45000,
                'description' => 'A4 paper 70gsm',
            ],
            [
                'code' => 'CLEAN001',
                'name' => 'Detergent',
                'category' => 'Cleaning',
                'unit' => 'bottle',
                'current_stock' => 15,
                'min_stock' => 5,
                'price' => 25000,
                'description' => 'Floor cleaner detergent',
            ],
            [
                'code' => 'PANTRY001',
                'name' => 'Coffee',
                'category' => 'Pantry',
                'unit' => 'kg',
                'current_stock' => 8,
                'min_stock' => 3,
                'price' => 75000,
                'description' => 'Coffee beans for office',
            ],
        ];

        foreach ($supplies as $supply) {
            Supply::create($supply);
        }
    }
}

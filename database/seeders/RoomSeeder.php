<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'room_number' => 'R001',
                'room_type' => 'Standard',
                'floor' => 1,
                'capacity' => 2,
                'status' => 'available',
                'daily_rate' => 150000,
                'description' => 'Standard guest room with basic amenities',
            ],
            [
                'room_number' => 'R002',
                'room_type' => 'Standard',
                'floor' => 1,
                'capacity' => 2,
                'status' => 'available',
                'daily_rate' => 150000,
                'description' => 'Standard guest room with basic amenities',
            ],
            [
                'room_number' => 'R101',
                'room_type' => 'Deluxe',
                'floor' => 2,
                'capacity' => 4,
                'status' => 'available',
                'daily_rate' => 250000,
                'description' => 'Deluxe room with better amenities',
            ],
            [
                'room_number' => 'R102',
                'room_type' => 'Deluxe',
                'floor' => 2,
                'capacity' => 4,
                'status' => 'maintenance',
                'daily_rate' => 250000,
                'description' => 'Deluxe room with better amenities',
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MeetingRoom;
use Illuminate\Database\Seeder;

class MeetingRoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Rose',
                'location' => 'HO Balikpapan',
                'capacity' => 20,
                'facilities' => 'Ruang meeting & TV',
                'is_active' => true,
            ],
            [
                'name' => 'Jasmin',
                'location' => 'HO Balikpapan',
                'capacity' => 15,
                'facilities' => 'Ruang meeting & TV',
                'is_active' => true,
            ],
            [
                'name' => 'Lotus',
                'location' => 'HO Balikpapan',
                'capacity' => 25,
                'facilities' => 'Ruang meeting & TV',
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'location' => 'HO Balikpapan',
                'capacity' => 30,
                'facilities' => 'Ruang meeting & TV',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            MeetingRoom::updateOrCreate(
                ['name' => $room['name']],
                $room
            );
        }
    }
}

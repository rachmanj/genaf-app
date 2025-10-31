<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomReservation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PmsSeeder extends Seeder
{
    public function run(): void
    {
        // Buildings
        $hq = Building::firstOrCreate(
            ['code' => 'HQ-JKT'],
            [
                'name' => 'Headquarters Jakarta',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'status' => 'active',
            ]
        );

        $bdg = Building::firstOrCreate(
            ['code' => 'OPS-BDG'],
            [
                'name' => 'Operations Bandung',
                'city' => 'Bandung',
                'country' => 'Indonesia',
                'status' => 'active',
            ]
        );

        // Rooms (attach to buildings)
        $roomsData = [
            [$hq->id, 'R001', 'Standard', 1, 2, 'available', 150000],
            [$hq->id, 'R002', 'Standard', 1, 2, 'available', 150000],
            [$hq->id, 'R101', 'Deluxe', 2, 4, 'available', 250000],
            [$hq->id, 'R102', 'Deluxe', 2, 4, 'maintenance', 250000],
            [$bdg->id, 'B201', 'Standard', 2, 2, 'available', 130000],
        ];

        $rooms = [];
        foreach ($roomsData as [$buildingId, $number, $type, $floor, $capacity, $status, $rate]) {
            $rooms[] = Room::firstOrCreate(
                ['building_id' => $buildingId, 'room_number' => $number],
                [
                    'room_type' => $type,
                    'floor' => $floor,
                    'capacity' => $capacity,
                    'status' => $status,
                    'daily_rate' => $rate,
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }

        // Reservations (spread across statuses)
        $today = Carbon::today();
        RoomReservation::firstOrCreate([
            'room_id' => $rooms[0]->id,
            'guest_name' => 'John Doe',
            'phone' => '+62-811-0000-001',
            'check_in' => $today->copy()->subDays(2),
            'check_out' => $today->copy()->addDays(1),
            'status' => 'checked_in',
            'created_by' => 1,
        ], [
            'company' => 'Acme Corp',
            'email' => 'john@example.com',
            'total_cost' => 0,
        ]);

        RoomReservation::firstOrCreate([
            'room_id' => $rooms[2]->id,
            'guest_name' => 'Jane Smith',
            'phone' => '+62-811-0000-002',
            'check_in' => $today->copy()->addDays(5),
            'check_out' => $today->copy()->addDays(8),
            'status' => 'pending',
            'created_by' => 1,
        ], [
            'company' => null,
            'email' => 'jane@example.com',
            'total_cost' => 0,
        ]);

        // Maintenances (some completed within 30 days)
        RoomMaintenance::firstOrCreate([
            'room_id' => $rooms[3]->id,
            'maintenance_type' => 'AC Service',
            'scheduled_date' => $today->copy()->subDays(10),
        ], [
            'completed_date' => $today->copy()->subDays(7),
            'cost' => 350000,
            'notes' => 'AC gas refill and cleaning',
        ]);

        RoomMaintenance::firstOrCreate([
            'room_id' => $rooms[4]->id,
            'maintenance_type' => 'Plumbing',
            'scheduled_date' => $today->copy()->addDays(3),
        ], [
            'completed_date' => null,
            'cost' => 0,
            'notes' => 'Fix sink leakage',
        ]);
    }
}



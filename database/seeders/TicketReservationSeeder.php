<?php

namespace Database\Seeders;

use App\Models\TicketReservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some employees for reservations
        $employees = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['employee', 'department head', 'manager']);
        })->take(5)->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Skipping ticket reservations seeder.');
            return;
        }

        $reservations = [
            // Pending reservations
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'flight',
                'destination' => 'Jakarta',
                'departure_date' => now()->addDays(15),
                'return_date' => now()->addDays(18),
                'cost' => 2500000,
                'status' => 'pending',
                'notes' => 'Business trip for client meeting',
            ],
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'train',
                'destination' => 'Bandung',
                'departure_date' => now()->addDays(20),
                'return_date' => null,
                'cost' => 350000,
                'status' => 'pending',
                'notes' => 'Training session attendance',
            ],
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'hotel',
                'destination' => 'Bali',
                'departure_date' => now()->addDays(25),
                'return_date' => now()->addDays(28),
                'cost' => 4500000,
                'status' => 'pending',
                'notes' => 'Company retreat',
            ],

            // Approved reservations
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'flight',
                'destination' => 'Surabaya',
                'departure_date' => now()->addDays(10),
                'return_date' => now()->addDays(12),
                'cost' => 2200000,
                'status' => 'approved',
                'approved_by' => User::whereHas('roles', function ($q) { $q->where('name', 'manager'); })->first()?->id,
                'approved_at' => now()->subDays(2),
                'notes' => 'Site visit',
            ],
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'bus',
                'destination' => 'Yogyakarta',
                'departure_date' => now()->addDays(12),
                'return_date' => now()->addDays(14),
                'cost' => 400000,
                'status' => 'approved',
                'approved_by' => User::whereHas('roles', function ($q) { $q->where('name', 'manager'); })->first()?->id,
                'approved_at' => now()->subDays(1),
                'notes' => 'Training course',
            ],

            // Booked reservations
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'flight',
                'destination' => 'Singapore',
                'departure_date' => now()->addDays(8),
                'return_date' => now()->addDays(11),
                'cost' => 5500000,
                'status' => 'booked',
                'approved_by' => User::whereHas('roles', function ($q) { $q->where('name', 'manager'); })->first()?->id,
                'approved_at' => now()->subDays(5),
                'booking_reference' => 'GA-123456',
                'notes' => 'International conference',
            ],

            // Completed reservations
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'train',
                'destination' => 'Malang',
                'departure_date' => now()->subDays(10),
                'return_date' => now()->subDays(8),
                'cost' => 320000,
                'status' => 'completed',
                'approved_by' => User::whereHas('roles', function ($q) { $q->where('name', 'manager'); })->first()?->id,
                'approved_at' => now()->subDays(15),
                'booking_reference' => 'KAI-789456',
                'notes' => 'Completed successfully',
            ],

            // Rejected reservation
            [
                'employee_id' => $employees->random()->id,
                'ticket_type' => 'hotel',
                'destination' => 'Lombok',
                'departure_date' => now()->addDays(30),
                'return_date' => now()->addDays(35),
                'cost' => 8000000,
                'status' => 'rejected',
                'approved_by' => User::whereHas('roles', function ($q) { $q->where('name', 'manager'); })->first()?->id,
                'approved_at' => now()->subDays(3),
                'rejection_reason' => 'Budget constraints for personal vacation',
                'notes' => 'Rejected due to policy',
            ],
        ];

        foreach ($reservations as $reservation) {
            TicketReservation::create($reservation);
        }

        $this->command->info('Ticket reservations seeded successfully!');
    }
}
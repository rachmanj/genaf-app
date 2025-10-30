<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, create roles/permissions and master data
        $this->call([
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            SupplySeeder::class,
            RoomSeeder::class,
            VehicleSeeder::class,
            TicketReservationSeeder::class,
        ]);

        // Create users and assign roles
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@genaf.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'department_id' => null,
            'phone' => '+62-123-456-7890',
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@genaf.com',
            'username' => 'manager',
            'password' => bcrypt('password'),
            'department_id' => null,
            'phone' => '+62-123-456-7891',
            'is_active' => true,
        ]);
        $managerUser->assignRole('manager');

        $employeeUser = User::create([
            'name' => 'Employee User',
            'email' => 'employee@genaf.com',
            'username' => 'employee',
            'password' => bcrypt('password'),
            'department_id' => null,
            'phone' => '+62-123-456-7892',
            'is_active' => true,
        ]);
        $employeeUser->assignRole('employee');
    }
}

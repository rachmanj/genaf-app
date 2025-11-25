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
            ProjectSeeder::class,
            VehicleDocumentTypeSeeder::class,
        ]);

        // Create users and assign roles BEFORE other seeders that reference users
        $adminUser = User::create([
            'name' => 'Superadmin',
            'email' => 'admin@genaf.com',
            'username' => 'superadmin',
            'password' => '20132013',
            'department_id' => 1, // General Department
            'phone' => '+62-123-456-7890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@genaf.com',
            'username' => 'manager',
            'password' => 'password',
            'department_id' => 1, // General Department
            'phone' => '+62-123-456-7891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $managerUser->assignRole('manager');

        $employeeUser = User::create([
            'name' => 'Employee User',
            'email' => 'employee@genaf.com',
            'username' => 'employee',
            'password' => 'password',
            'department_id' => 1, // General Department
            'phone' => '+62-123-456-7892',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $employeeUser->assignRole('employee');

        // Create test users with specific departments for department-based filtering tests
        $gaAdminUser = User::create([
            'name' => 'GA Admin User',
            'email' => 'gaadmin@genaf.com',
            'username' => 'gaadmin',
            'password' => 'password',
            'department_id' => 1, // General Department
            'phone' => '+62-123-456-7893',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $gaAdminUser->assignRole('ga admin');

        // Now create other master data that may reference users
        $this->call([
            SupplySeeder::class,
            VehicleSeeder::class,
            TicketReservationSeeder::class,
            PmsSeeder::class,
        ]);
    }
}

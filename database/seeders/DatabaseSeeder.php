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
        // First, create roles and permissions
        $this->call([
            RolePermissionSeeder::class,
            SupplySeeder::class,
            RoomSeeder::class,
            VehicleSeeder::class,
        ]);

        // Create users and assign roles
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@genaf.com',
            'password' => bcrypt('password'),
            'department' => 'IT',
            'phone' => '+62-123-456-7890',
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@genaf.com',
            'password' => bcrypt('password'),
            'department' => 'Operations',
            'phone' => '+62-123-456-7891',
            'is_active' => true,
        ]);
        $managerUser->assignRole('manager');

        $employeeUser = User::create([
            'name' => 'Employee User',
            'email' => 'employee@genaf.com',
            'password' => bcrypt('password'),
            'department' => 'Sales',
            'phone' => '+62-123-456-7892',
            'is_active' => true,
        ]);
        $employeeUser->assignRole('employee');
    }
}

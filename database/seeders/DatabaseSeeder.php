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
        ]);

        // Create users and assign roles BEFORE other seeders that reference users
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

        // Create test users with specific departments for department-based filtering tests
        $gaAdminUser = User::create([
            'name' => 'GA Admin User',
            'email' => 'gaadmin@genaf.com',
            'username' => 'gaadmin',
            'password' => bcrypt('password'),
            'department_id' => null, // GA Admin has no specific department - can see all
            'phone' => '+62-123-456-7893',
            'is_active' => true,
        ]);
        $gaAdminUser->assignRole('ga admin');

        // Department Head for Finance department (department_id = 7 based on DepartmentSeeder)
        $deptHeadFinance = User::create([
            'name' => 'Finance Dept Head',
            'email' => 'finance.depthead@genaf.com',
            'username' => 'finance_depthead',
            'password' => bcrypt('password'),
            'department_id' => 7,
            'phone' => '+62-123-456-7894',
            'is_active' => true,
        ]);
        $deptHeadFinance->assignRole('department head');

        // Department Head for IT department (department_id = 16)
        $deptHeadIT = User::create([
            'name' => 'IT Dept Head',
            'email' => 'it.depthead@genaf.com',
            'username' => 'it_depthead',
            'password' => bcrypt('password'),
            'department_id' => 16,
            'phone' => '+62-123-456-7895',
            'is_active' => true,
        ]);
        $deptHeadIT->assignRole('department head');

        // Employee in Finance department
        $employeeFinance = User::create([
            'name' => 'Finance Employee',
            'email' => 'finance.employee@genaf.com',
            'username' => 'finance_employee',
            'password' => bcrypt('password'),
            'department_id' => 7,
            'phone' => '+62-123-456-7896',
            'is_active' => true,
        ]);
        $employeeFinance->assignRole('employee');

        // Employee in IT department
        $employeeIT = User::create([
            'name' => 'IT Employee',
            'email' => 'it.employee@genaf.com',
            'username' => 'it_employee',
            'password' => bcrypt('password'),
            'department_id' => 16,
            'phone' => '+62-123-456-7897',
            'is_active' => true,
        ]);
        $employeeIT->assignRole('employee');

        // Now create other master data that may reference users
        $this->call([
            SupplySeeder::class,
            VehicleSeeder::class,
            TicketReservationSeeder::class,
            PmsSeeder::class,
        ]);
    }
}

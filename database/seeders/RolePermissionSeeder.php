<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'toggle user status',

            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permission Management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Supplies Management
            'view supplies',
            'create supplies',
            'edit supplies',
            'delete supplies',
            'view supply requests',
            'create supply requests',
            'edit supply requests',
            'approve supply requests',

            // Ticket Reservations
            'view ticket reservations',
            'create ticket reservations',
            'edit ticket reservations',
            'delete ticket reservations',
            'approve ticket reservations',

            // Property Management (PMS)
            'view rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',
            'view room reservations',
            'create room reservations',
            'edit room reservations',
            'approve room reservations',

            // Vehicle Administration
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'view fuel records',
            'create fuel records',
            'edit fuel records',
            'view vehicle maintenance',
            'create vehicle maintenance',
            'edit vehicle maintenance',

            // Asset Inventory
            'view assets',
            'create assets',
            'edit assets',
            'delete assets',
            'view asset maintenance',
            'create asset maintenance',
            'edit asset maintenance',
            'view asset transfers',
            'create asset transfers',
            'approve asset transfers',

            // Reports
            'view reports',
            'export reports',

            // System Settings
            'view system settings',
            'edit system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin role - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Manager role - can manage most things but not system settings or admin functions
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerPermissions = Permission::whereNotIn('name', [
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'edit system settings',
        ])->get();
        $managerRole->syncPermissions($managerPermissions);

        // Employee role - basic permissions
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeePermissions = [
            'view supplies',
            'create supply requests',
            'view supply requests',
            'view ticket reservations',
            'create ticket reservations',
            'view ticket reservations',
            'view rooms',
            'view room reservations',
            'create room reservations',
            'view vehicles',
            'view fuel records',
            'create fuel records',
            'view vehicle maintenance',
            'view assets',
            'view asset maintenance',
            'view asset transfers',
        ];
        $employeeRole->syncPermissions($employeePermissions);
    }
}

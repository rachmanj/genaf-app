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
            'reject supply requests',
            'delete supply requests',
            'view supply transactions',
            'create supply transactions',
            'delete supply transactions',

            // Department Management
            'view departments',
            'create departments',
            'edit departments',

            // Department Head Approvals
            'approve dept head supply requests',
            'reject dept head supply requests',

            // GA Admin Approvals
            'approve ga admin supply requests',
            'reject ga admin supply requests',

            // Supply Fulfillment
            'view supply fulfillment',
            'fulfill supply requests',

            // Department Stock Management
            'view department stock',
            'view department stock reports',

            // Stock Opname
            'view stock opname',
            'create stock opname',
            'start stock opname',
            'count stock opname',
            'verify stock opname',
            'complete stock opname',
            'approve stock opname',
            'cancel stock opname',
            'export stock opname',
            'view stock opname reports',

            // Ticket Reservations
            'view ticket reservations',
            'create ticket reservations',
            'edit ticket reservations',
            'delete ticket reservations',
            'approve ticket reservations',

            // Meeting Room Reservations
            'view meeting room reservations',
            'create meeting room reservations',
            'edit meeting room reservations',
            'delete meeting room reservations',
            'approve dept head meeting room reservations',
            'reject dept head meeting room reservations',
            'approve ga admin meeting room reservations',
            'reject ga admin meeting room reservations',
            'allocate meeting room reservations',
            'send response meeting room reservations',
            'view meeting room allocation diagram',

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
            'delete fuel records',
            'view vehicle maintenance',
            'create vehicle maintenance',
            'edit vehicle maintenance',
            'delete vehicle maintenance',
            'view vehicle documents',
            'create vehicle documents',
            'delete vehicle documents',
            'download vehicle documents',

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

        // GA Admin role - General Affairs Admin with supply management permissions
        $gaAdminRole = Role::firstOrCreate(['name' => 'ga admin']);
        $gaAdminPermissions = [
            'view supplies',
            'create supplies',
            'edit supplies',
            'view supply requests',
            'approve ga admin supply requests',
            'reject ga admin supply requests',
            'view supply transactions',
            'create supply transactions',
            'delete supply transactions',
            'view supply fulfillment',
            'fulfill supply requests',
            'view department stock',
            'view department stock reports',
            'view departments',
            'view stock opname',
            'create stock opname',
            'start stock opname',
            'count stock opname',
            'verify stock opname',
            'complete stock opname',
            'approve stock opname',
            'cancel stock opname',
            'export stock opname',
            'view stock opname reports',
            'view ticket reservations',
            'view meeting room reservations',
            'create meeting room reservations',
            'approve ga admin meeting room reservations',
            'reject ga admin meeting room reservations',
            'allocate meeting room reservations',
            'send response meeting room reservations',
            'view meeting room allocation diagram',
            'view rooms',
            'view room reservations',
            'view vehicles',
            'view fuel records',
            'view vehicle maintenance',
            'view assets',
            'view asset maintenance',
            'view asset transfers',
            'view reports',
        ];
        $gaAdminRole->syncPermissions($gaAdminPermissions);

        // Department Head role - can approve department requests
        $deptHeadRole = Role::firstOrCreate(['name' => 'department head']);
        $deptHeadPermissions = [
            'view supplies',
            'view supply requests',
            'create supply requests',
            'approve dept head supply requests',
            'reject dept head supply requests',
            'view department stock',
            'view stock opname',
            'count stock opname',
            'view ticket reservations',
            'create ticket reservations',
            'view meeting room reservations',
            'create meeting room reservations',
            'approve dept head meeting room reservations',
            'reject dept head meeting room reservations',
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
        $deptHeadRole->syncPermissions($deptHeadPermissions);

        // Employee role - basic permissions
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeePermissions = [
            'view supplies',
            'create supply requests',
            'view supply requests',
            'view stock opname',
            'count stock opname',
            'view ticket reservations',
            'create ticket reservations',
            'view meeting room reservations',
            'create meeting room reservations',
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

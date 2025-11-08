<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = ['import vehicles', 'sync vehicles'];

        $permissions = collect($permissionNames)->map(function (string $name) {
            return Permission::firstOrCreate(['name' => $name]);
        });

        $roles = Role::whereIn('name', ['admin', 'ga admin'])->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = ['import vehicles', 'sync vehicles'];

        $roles = Role::whereIn('name', ['admin', 'ga admin'])->get();
        $permissions = Permission::whereIn('name', $permissionNames)->get();

        foreach ($roles as $role) {
            $role->revokePermissionTo($permissions);
        }

        Permission::whereIn('name', $permissionNames)->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};

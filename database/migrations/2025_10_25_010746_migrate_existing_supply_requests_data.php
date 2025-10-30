<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create a default department for existing data (only if it doesn't exist)
        $existingDepartment = DB::table('departments')->where('department_name', 'General Department')->first();
        if (!$existingDepartment) {
            $defaultDepartmentId = DB::table('departments')->insertGetId([
                'department_name' => 'General Department',
                'slug' => 'general-department',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $defaultDepartmentId = $existingDepartment->id;
        }

        // Update existing supply requests to use the default department
        DB::table('supply_requests')
            ->whereNull('department_id')
            ->update(['department_id' => $defaultDepartmentId]);

        // Update existing users to use the default department if they don't have one
        DB::table('users')
            ->whereNull('department_id')
            ->update(['department_id' => $defaultDepartmentId]);

        // Update existing supply request items to set approved_quantity = quantity (only if column exists)
        if (Schema::hasColumn('supply_request_items', 'approved_quantity')) {
            DB::table('supply_request_items')
                ->whereNull('approved_quantity')
                ->update(['approved_quantity' => DB::raw('quantity')]);
        }

        // Update existing supply request items to set fulfillment_status (only if column exists)
        if (Schema::hasColumn('supply_request_items', 'fulfillment_status')) {
            DB::table('supply_request_items')
                ->whereNull('fulfillment_status')
                ->update(['fulfillment_status' => 'pending']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the default department
        DB::table('departments')
            ->where('department_name', 'General Department')
            ->delete();

        // Reset supply requests status to old format
        DB::table('supply_requests')
            ->where('status', 'pending_dept_head')
            ->update(['status' => 'pending']);

        DB::table('supply_requests')
            ->where('status', 'pending_ga_admin')
            ->update(['status' => 'pending']);

        DB::table('supply_requests')
            ->where('status', 'partially_fulfilled')
            ->update(['status' => 'approved']);
    }
};
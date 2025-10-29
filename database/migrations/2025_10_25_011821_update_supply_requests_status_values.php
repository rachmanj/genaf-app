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
        // Update existing supply requests status to new format
        DB::table('supply_requests')
            ->where('status', 'pending')
            ->update(['status' => 'pending_dept_head']);

        DB::table('supply_requests')
            ->where('status', 'approved')
            ->update(['status' => 'approved']);

        DB::table('supply_requests')
            ->where('status', 'rejected')
            ->update(['status' => 'rejected']);

        DB::table('supply_requests')
            ->where('status', 'fulfilled')
            ->update(['status' => 'fulfilled']);

        // Now update the enum to only include new values
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->enum('status', ['pending_dept_head', 'pending_ga_admin', 'approved', 'rejected', 'partially_fulfilled', 'fulfilled'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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

        // Restore old enum
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'fulfilled'])->change();
        });
    }
};
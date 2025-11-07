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
        // For MySQL/MariaDB, we need to modify the enum carefully
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending_dept_head',
                'pending_ga_admin',
                'approved',
                'rejected',
                'pending_verification',
                'partially_fulfilled',
                'fulfilled'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert any pending_verification back to approved if reverting
        DB::table('supply_requests')
            ->where('status', 'pending_verification')
            ->update(['status' => 'approved']);

        Schema::table('supply_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending_dept_head',
                'pending_ga_admin',
                'approved',
                'rejected',
                'partially_fulfilled',
                'fulfilled'
            ])->change();
        });
    }
};

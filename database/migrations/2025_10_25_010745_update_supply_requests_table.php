<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            // Add new fields only if they don't exist
            if (!Schema::hasColumn('supply_requests', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade')->after('employee_id');
            }
            if (!Schema::hasColumn('supply_requests', 'department_head_approved_by')) {
                $table->foreignId('department_head_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('department_id');
            }
            if (!Schema::hasColumn('supply_requests', 'department_head_approved_at')) {
                $table->timestamp('department_head_approved_at')->nullable()->after('department_head_approved_by');
            }
            if (!Schema::hasColumn('supply_requests', 'ga_admin_approved_by')) {
                $table->foreignId('ga_admin_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('department_head_approved_at');
            }
            if (!Schema::hasColumn('supply_requests', 'ga_admin_approved_at')) {
                $table->timestamp('ga_admin_approved_at')->nullable()->after('ga_admin_approved_by');
            }
            
            // Update status enum to include both old and new values temporarily
            $table->enum('status', ['pending', 'pending_dept_head', 'pending_ga_admin', 'approved', 'rejected', 'partially_fulfilled', 'fulfilled'])->change();
            
            // Remove old fields
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            // Restore old fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Restore old status enum
            $table->enum('status', ['pending', 'approved', 'rejected', 'fulfilled'])->change();
            
            // Remove new fields
            $table->dropForeign(['department_id', 'department_head_approved_by', 'ga_admin_approved_by']);
            $table->dropColumn([
                'department_id',
                'department_head_approved_by',
                'department_head_approved_at',
                'ga_admin_approved_by',
                'ga_admin_approved_at'
            ]);
        });
    }
};
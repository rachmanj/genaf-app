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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable();
            }
        });

        // Add the foreign key in a separate schema call to avoid issues on some drivers
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                // Drop FK first if exists, then the column
                try {
                    $table->dropForeign(['department_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('department_id');
            }
        });
    }
};

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
        // Assign General Department (id: 1) to users without departments
        DB::table('users')
            ->whereNull('department_id')
            ->update(['department_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove department assignments from users in General Department
        DB::table('users')
            ->where('department_id', 1)
            ->whereIn('email', ['admin@genaf.com', 'manager@genaf.com', 'employee@genaf.com', 'test.project@genaf.com'])
            ->update(['department_id' => null]);
    }
};

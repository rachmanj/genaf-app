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
        Schema::table('supply_transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out', 'adjustment'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out'])->change();
        });
    }
};

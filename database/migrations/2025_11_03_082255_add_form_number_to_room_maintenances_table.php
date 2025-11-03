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
        Schema::table('room_maintenances', function (Blueprint $table) {
            $table->string('form_number')->unique()->after('id');
            $table->index('form_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_maintenances', function (Blueprint $table) {
            $table->dropIndex(['form_number']);
            $table->dropColumn('form_number');
        });
    }
};

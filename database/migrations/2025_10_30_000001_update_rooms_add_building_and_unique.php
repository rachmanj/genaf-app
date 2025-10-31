<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'building_id')) {
                $table->foreignId('building_id')
                    ->nullable()
                    ->constrained('buildings')
                    ->restrictOnDelete();
            }

            $table->dropUnique('rooms_room_number_unique');
            $table->unique(['building_id', 'room_number'], 'rooms_building_room_unique');
            $table->index('building_id');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropUnique('rooms_building_room_unique');
            $table->unique('room_number');

            if (Schema::hasColumn('rooms', 'building_id')) {
                $table->dropConstrainedForeignId('building_id');
            }
        });
    }
};



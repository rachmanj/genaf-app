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
        Schema::create('meeting_consumption_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('meeting_room_reservations')->onDelete('cascade');
            $table->date('consumption_date');
            $table->enum('consumption_type', ['coffee_break_morning', 'coffee_break_afternoon', 'lunch', 'dinner']);
            $table->boolean('requested')->default(false);
            $table->text('description')->nullable();
            $table->boolean('fulfilled')->default(false);
            $table->timestamp('fulfilled_at')->nullable();
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'consumption_date', 'consumption_type'], 'mcr_reservation_date_type_idx');
            $table->unique(['reservation_id', 'consumption_date', 'consumption_type'], 'mcr_reservation_date_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_consumption_requests');
    }
};

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
        Schema::create('reservation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('ticket_reservations')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('original_name');
            $table->integer('file_size');
            $table->timestamps();

            $table->index('reservation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_documents');
    }
};

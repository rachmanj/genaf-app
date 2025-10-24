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
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('from_location');
            $table->string('to_location');
            $table->foreignId('from_employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('transfer_date');
            $table->text('reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->timestamps();

            $table->index(['asset_id', 'transfer_date']);
            $table->index(['from_employee_id', 'to_employee_id']);
            $table->index(['status', 'transfer_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};

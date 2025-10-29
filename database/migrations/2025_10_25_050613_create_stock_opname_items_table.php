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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('stock_opname_sessions')->onDelete('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->integer('system_stock')->default(0);
            $table->integer('actual_count')->nullable();
            $table->integer('variance')->default(0);
            $table->decimal('variance_value', 15, 2)->default(0);
            $table->enum('status', ['pending', 'counting', 'counted', 'verified'])->default('pending');
            $table->enum('reason_code', ['damaged', 'expired', 'lost', 'found', 'miscount', 'other'])->nullable();
            $table->text('reason_notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('location_verified')->default(false);
            $table->foreignId('counted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('counted_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'status']);
            $table->index(['supply_id', 'session_id']);
            $table->unique(['session_id', 'supply_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};

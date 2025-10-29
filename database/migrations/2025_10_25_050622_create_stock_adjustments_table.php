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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('stock_opname_sessions')->onDelete('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->enum('adjustment_type', ['increase', 'decrease']);
            $table->integer('quantity');
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->enum('reason_code', ['damaged', 'expired', 'lost', 'found', 'miscount', 'other'])->nullable();
            $table->text('reason_notes')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('supply_transactions')->onDelete('set null');
            $table->foreignId('adjusted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('adjusted_at');
            $table->timestamps();

            $table->index(['session_id', 'supply_id']);
            $table->index(['adjusted_by', 'adjusted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};

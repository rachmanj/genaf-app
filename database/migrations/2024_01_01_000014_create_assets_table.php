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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category');
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 12, 2);
            $table->decimal('depreciation_rate', 5, 2)->default(0);
            $table->decimal('current_value', 12, 2)->default(0);
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->string('location');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'maintenance', 'retired', 'disposed'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['location', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

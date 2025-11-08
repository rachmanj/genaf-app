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
        Schema::create('vehicle_document_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_document_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_document_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_document_revisions');
    }
};

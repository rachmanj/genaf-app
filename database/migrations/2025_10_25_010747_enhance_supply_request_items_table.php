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
        Schema::table('supply_request_items', function (Blueprint $table) {
            $table->integer('approved_quantity')->default(0)->after('quantity');
            $table->enum('fulfillment_status', ['pending', 'partial', 'completed'])->default('pending')->after('approved_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_request_items', function (Blueprint $table) {
            $table->dropColumn(['approved_quantity', 'fulfillment_status']);
        });
    }
};
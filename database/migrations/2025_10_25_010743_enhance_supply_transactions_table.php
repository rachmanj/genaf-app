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
        Schema::table('supply_transactions', function (Blueprint $table) {
            $table->enum('source', ['SAP', 'manual'])->default('SAP')->after('type');
            $table->string('supplier_name')->nullable()->after('source');
            $table->string('purchase_order_no')->nullable()->after('supplier_name');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null')->after('purchase_order_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_transactions', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['source', 'supplier_name', 'purchase_order_no', 'department_id']);
        });
    }
};
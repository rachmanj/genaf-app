<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('unit_no')->nullable()->after('id');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->renameColumn('plate_number', 'nomor_polisi');
            $table->renameColumn('type', 'plant_group');
            $table->renameColumn('notes', 'remarks');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('year')->nullable()->change();
            $table->string('plant_group')->nullable()->change();
            $table->enum('status', ['active', 'maintenance', 'retired', 'inactive', 'scrap', 'sold'])
                ->default('active')
                ->change();
            $table->string('current_project_code')->nullable()->after('status');
            $table->timestamp('arkfleet_synced_at')->nullable()->after('current_project_code');
            $table->string('arkfleet_sync_status')->default('never')->after('arkfleet_synced_at');
            $table->text('arkfleet_sync_message')->nullable()->after('arkfleet_sync_status');
            $table->json('arkfleet_last_payload')->nullable()->after('arkfleet_sync_message');
            $table->boolean('is_active')->default(true)->change();
        });

        DB::table('vehicles')
            ->whereNull('unit_no')
            ->update(['unit_no' => DB::raw('nomor_polisi')]);

        Schema::table('vehicles', function (Blueprint $table) {
            $table->unique('unit_no', 'vehicles_unit_no_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique('vehicles_unit_no_unique');
            $table->dropColumn([
                'unit_no',
                'current_project_code',
                'arkfleet_synced_at',
                'arkfleet_sync_status',
                'arkfleet_sync_message',
                'arkfleet_last_payload',
            ]);
            $table->integer('year')->nullable(false)->change();
            $table->renameColumn('nomor_polisi', 'plate_number');
            $table->renameColumn('plant_group', 'type');
            $table->renameColumn('remarks', 'notes');
            $table->enum('status', ['active', 'maintenance', 'retired'])->default('active')->change();
            $table->boolean('is_active')->default(true)->change();
        });
    }
};

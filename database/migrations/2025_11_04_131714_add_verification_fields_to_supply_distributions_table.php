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
        Schema::table('supply_distributions', function (Blueprint $table) {
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('notes');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_notes')->nullable()->after('verified_at');
            $table->text('rejection_reason')->nullable()->after('verification_notes');

            $table->index(['verification_status', 'distribution_date']);
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_distributions', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropIndex(['verification_status', 'distribution_date']);
            $table->dropIndex(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'verified_by',
                'verified_at',
                'verification_notes',
                'rejection_reason',
            ]);
        });
    }
};

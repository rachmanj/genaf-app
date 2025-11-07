<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('room_reservations', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            }
            if (!Schema::hasColumn('room_reservations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('room_reservations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('room_reservations', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            }
            if (!Schema::hasColumn('room_reservations', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('checked_out_at');
            }
            if (!Schema::hasColumn('room_reservations', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            if (!Schema::hasColumn('room_reservations', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn([
                'approved_by',
                'approved_at',
                'checked_in_at',
                'checked_out_at',
                'cancelled_by',
                'cancelled_at',
                'cancellation_reason',
            ]);
        });
    }
};


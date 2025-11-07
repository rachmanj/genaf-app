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
        Schema::create('meeting_room_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('form_number')->unique();
            $table->foreignId('requestor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('requested_room_id')->nullable()->constrained('meeting_rooms')->onDelete('set null');
            $table->foreignId('allocated_room_id')->nullable()->constrained('meeting_rooms')->onDelete('set null');
            $table->string('location');
            $table->string('meeting_title');
            $table->date('meeting_date_start');
            $table->date('meeting_date_end')->nullable();
            $table->time('meeting_time_start');
            $table->time('meeting_time_end');
            $table->integer('participant_count');
            $table->text('required_facilities')->nullable();
            $table->enum('status', ['pending_dept_head', 'pending_ga_admin', 'approved', 'confirmed', 'rejected', 'cancelled'])->default('pending_dept_head');
            $table->foreignId('department_head_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('department_head_approved_at')->nullable();
            $table->text('department_head_rejection_reason')->nullable();
            $table->foreignId('ga_admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('ga_admin_approved_at')->nullable();
            $table->text('ga_admin_rejection_reason')->nullable();
            $table->timestamp('room_allocated_at')->nullable();
            $table->timestamp('response_sent_at')->nullable();
            $table->text('response_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['requestor_id', 'meeting_date_start'], 'mr_res_requestor_date_idx');
            $table->index(['allocated_room_id', 'meeting_date_start', 'meeting_date_end'], 'mr_res_allocated_room_idx');
            $table->index(['requested_room_id', 'meeting_date_start', 'meeting_date_end'], 'mr_res_requested_room_idx');
            $table->index(['status', 'meeting_date_start'], 'mr_res_status_date_idx');
            $table->index(['department_id', 'status'], 'mr_res_dept_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_room_reservations');
    }
};

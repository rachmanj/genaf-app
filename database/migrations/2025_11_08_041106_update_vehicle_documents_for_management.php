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
        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->foreignId('vehicle_document_type_id')
                ->nullable()
                ->after('vehicle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_number')
                ->nullable()
                ->after('vehicle_document_type_id');

            $table->date('document_date')
                ->nullable()
                ->after('document_number');

            $table->date('due_date')
                ->nullable()
                ->after('document_date');

            $table->string('supplier')
                ->nullable()
                ->after('due_date');

            $table->decimal('amount', 12, 2)
                ->nullable()
                ->after('supplier');
        });

        $typeMap = DB::table('vehicle_document_types')
            ->select('id', 'slug')
            ->pluck('id', 'slug')
            ->toArray();

        $documentTypeLookup = [
            'stnk' => $typeMap['stnk'] ?? null,
            'kir' => $typeMap['kir'] ?? null,
        ];

        DB::table('vehicle_documents')
            ->orderBy('id')
            ->lazy()
            ->each(function ($document) use ($documentTypeLookup) {
                $slug = strtolower($document->document_type ?? '');
                $typeId = $documentTypeLookup[$slug] ?? null;

                DB::table('vehicle_documents')
                    ->where('id', $document->id)
                    ->update([
                        'vehicle_document_type_id' => $typeId,
                        'document_number' => $document->form_number,
                        'document_date' => $document->issue_date,
                        'due_date' => $document->expiry_date,
                        'amount' => $document->cost,
                    ]);
            });

        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'issue_date', 'expiry_date', 'cost']);

            $table->index(['vehicle_document_type_id', 'due_date']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->enum('document_type', ['STNK', 'Insurance', 'KIR', 'SIM', 'Other'])
                ->after('vehicle_id');
            $table->date('issue_date')->nullable()->after('document_type');
            $table->date('expiry_date')->nullable()->after('issue_date');
            $table->decimal('cost', 10, 2)->default(0)->after('expiry_date');

            $table->index(['vehicle_id', 'document_type']);
            $table->index(['expiry_date', 'document_type']);
        });

        DB::table('vehicle_documents')
            ->orderBy('id')
            ->lazy()
            ->each(function ($document) {
                DB::table('vehicle_documents')
                    ->where('id', $document->id)
                    ->update([
                        'document_type' => strtoupper(optional(
                            DB::table('vehicle_document_types')->find($document->vehicle_document_type_id)
                        )->name ?? 'Other'),
                        'issue_date' => $document->document_date,
                        'expiry_date' => $document->due_date,
                        'cost' => $document->amount ?? 0,
                    ]);
            });

        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->dropIndex(['vehicle_document_type_id', 'due_date']);
            $table->dropIndex(['due_date']);
            $table->dropColumn([
                'vehicle_document_type_id',
                'document_number',
                'document_date',
                'due_date',
                'supplier',
                'amount',
            ]);
        });
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocumentRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_document_id',
        'document_number',
        'document_date',
        'due_date',
        'supplier',
        'amount',
        'file_path',
        'notes',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(VehicleDocument::class, 'vehicle_document_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

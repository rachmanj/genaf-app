<?php

namespace App\Models;

use App\Services\FormNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_number',
        'vehicle_id',
        'vehicle_document_type_id',
        'document_number',
        'document_date',
        'due_date',
        'supplier',
        'amount',
        'file_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleDocumentType::class, 'vehicle_document_type_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(VehicleDocumentRevision::class);
    }

    public function scopeExpiringWithin($query, int $days)
    {
        return $query->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->addDays($days))
            ->whereDate('due_date', '>', now());
    }

    public function snapshotForRevision(): array
    {
        return [
            'document_number' => $this->document_number,
            'document_date' => $this->document_date,
            'due_date' => $this->due_date,
            'supplier' => $this->supplier,
            'amount' => $this->amount,
            'file_path' => $this->file_path,
            'notes' => $this->notes,
            'changed_by' => auth()->id(),
        ];
    }

    protected static function booted()
    {
        static::creating(function (self $model) {
            if (empty($model->form_number)) {
                $model->form_number = FormNumberService::generateFormNumber('43', 'vehicle_documents');
            }
        });

        static::created(function (self $document) {
            $document->revisions()->create($document->snapshotForRevision());
        });

        static::updated(function (self $document) {
            if ($document->wasChanged([
                'document_number',
                'document_date',
                'due_date',
                'supplier',
                'amount',
                'file_path',
                'notes',
            ])) {
                $document->revisions()->create($document->snapshotForRevision());
            }
        });
    }
}



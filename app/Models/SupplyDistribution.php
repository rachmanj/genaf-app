<?php

namespace App\Models;

use App\Services\FormNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_number',
        'supply_id',
        'department_id',
        'request_item_id',
        'quantity',
        'distribution_date',
        'distributed_by',
        'notes',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the supply that was distributed.
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Get the department that received the distribution.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the request item that this distribution fulfills.
     */
    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(SupplyRequestItem::class, 'request_item_id');
    }

    /**
     * Get the user who made the distribution.
     */
    public function distributedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    /**
     * Get the user who verified the distribution.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if distribution can be verified by a user
     */
    public function canBeVerifiedBy(User $user): bool
    {
        // Only requestor (employee who made the request) can verify
        if (!$this->requestItem || !$this->requestItem->request) {
            return false;
        }

        return $this->verification_status === 'pending'
            && $this->requestItem->request->employee_id === $user->id;
    }

    /**
     * Check if distribution is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if distribution is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if distribution is rejected
     */
    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Mark distribution as verified
     */
    public function markAsVerified(User $user, ?string $notes = null): void
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Mark distribution as rejected
     */
    public function markAsRejected(User $user, string $reason, ?string $notes = null): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Get the request that this distribution belongs to (through request_item)
     */
    public function getRequestAttribute()
    {
        return $this->requestItem?->request;
    }

    /**
     * Scope a query to only include pending verification distributions.
     */
    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', 'pending');
    }

    /**
     * Scope a query to only include verified distributions.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope a query to only include rejected distributions.
     */
    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    /**
     * Get department stock summary aggregated by department.
     */
    public static function getDepartmentStockSummary()
    {
        return self::selectRaw('
                department_id,
                departments.department_name,
                SUM(quantity) as total_distributed,
                COUNT(*) as distribution_count
            ')
            ->join('departments', 'supply_distributions.department_id', '=', 'departments.id')
            ->groupBy('department_id', 'departments.department_name')
            ->orderBy('total_distributed', 'desc')
            ->get();
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->form_number)) {
                $model->form_number = FormNumberService::generateFormNumber('12', 'supply_distributions');
            }
        });
    }
}

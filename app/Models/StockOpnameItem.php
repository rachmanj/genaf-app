<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'supply_id',
        'system_stock',
        'actual_count',
        'variance',
        'variance_value',
        'status',
        'reason_code',
        'reason_notes',
        'photo_path',
        'location_verified',
        'counted_by',
        'counted_at',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'variance_value' => 'decimal:2',
            'location_verified' => 'boolean',
            'counted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the session that owns this item
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'session_id');
    }

    /**
     * Get the supply for this item
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Get the user who counted this item
     */
    public function counter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    /**
     * Get the user who verified this item
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Calculate variance
     */
    public function calculateVariance()
    {
        if ($this->actual_count !== null) {
            $this->variance = $this->actual_count - $this->system_stock;
            $this->variance_value = $this->variance * ($this->supply->price ?? 0);
        }
    }

    /**
     * Mark as counted
     */
    public function markAsCounted($countedBy, $actualCount, $reasonCode = null, $reasonNotes = null)
    {
        $this->update([
            'actual_count' => $actualCount,
            'reason_code' => $reasonCode,
            'reason_notes' => $reasonNotes,
            'counted_by' => $countedBy,
            'counted_at' => now(),
            'status' => 'counted',
        ]);

        $this->calculateVariance();
        $this->save();
    }

    /**
     * Verify count
     */
    public function verify($verifiedBy)
    {
        $this->update([
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'status' => 'verified',
        ]);
    }

    /**
     * Check if item has variance
     */
    public function hasVariance()
    {
        return $this->variance != 0;
    }

    /**
     * Get variance status badge
     */
    public function getVarianceBadge()
    {
        if ($this->variance > 0) {
            return '<span class="badge badge-success">+' . $this->variance . '</span>';
        } elseif ($this->variance < 0) {
            return '<span class="badge badge-danger">' . $this->variance . '</span>';
        } else {
            return '<span class="badge badge-secondary">0</span>';
        }
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            'pending' => '<span class="badge badge-secondary">Pending</span>',
            'counting' => '<span class="badge badge-warning">Counting</span>',
            'counted' => '<span class="badge badge-info">Counted</span>',
            'verified' => '<span class="badge badge-success">Verified</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    /**
     * Get reason code badge
     */
    public function getReasonCodeBadge()
    {
        if (!$this->reason_code) {
            return '<span class="badge badge-secondary">No Reason</span>';
        }

        $badges = [
            'damaged' => '<span class="badge badge-danger">Damaged</span>',
            'expired' => '<span class="badge badge-warning">Expired</span>',
            'lost' => '<span class="badge badge-danger">Lost</span>',
            'found' => '<span class="badge badge-success">Found</span>',
            'miscount' => '<span class="badge badge-info">Miscount</span>',
            'other' => '<span class="badge badge-secondary">Other</span>',
        ];

        return $badges[$this->reason_code] ?? '<span class="badge badge-secondary">Unknown</span>';
    }
}

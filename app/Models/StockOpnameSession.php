<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpnameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'title',
        'description',
        'type',
        'schedule_type',
        'status',
        'started_at',
        'completed_at',
        'approved_at',
        'created_by',
        'approved_by',
        'total_items',
        'counted_items',
        'items_with_variance',
        'total_variance_value',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'approved_at' => 'datetime',
            'total_variance_value' => 'decimal:2',
        ];
    }

    /**
     * Get the items for this session
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'session_id');
    }

    /**
     * Get the user who created this session
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this session
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the adjustments for this session
     */
    public function adjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'session_id');
    }

    /**
     * Scope for draft sessions
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for in progress sessions
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for approved sessions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Start the session (draft → in_progress)
     */
    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Complete the session (in_progress → completed)
     */
    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Approve the session (completed → approved)
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Cancel the session
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage()
    {
        if ($this->total_items == 0) {
            return 0;
        }
        return round(($this->counted_items / $this->total_items) * 100, 2);
    }

    /**
     * Get total variance value
     */
    public function getTotalVarianceValue()
    {
        return $this->items()->sum('variance_value');
    }

    /**
     * Get accuracy rate
     */
    public function getAccuracyRate()
    {
        if ($this->total_items == 0) {
            return 100;
        }
        $accurateItems = $this->total_items - $this->items_with_variance;
        return round(($accurateItems / $this->total_items) * 100, 2);
    }

    /**
     * Check if session can be started
     */
    public function canBeStarted()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if session can be completed
     */
    public function canBeCompleted()
    {
        return $this->status === 'in_progress' && $this->counted_items >= $this->total_items;
    }

    /**
     * Check if session can be approved
     */
    public function canBeApproved()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if session can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['draft', 'in_progress']);
    }

    /**
     * Generate session code
     */
    public static function generateSessionCode()
    {
        $year = date('Y');
        $lastSession = self::where('session_code', 'like', "SO-{$year}-%")
            ->orderBy('session_code', 'desc')
            ->first();

        if ($lastSession) {
            $lastNumber = (int) substr($lastSession->session_code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "SO-{$year}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}

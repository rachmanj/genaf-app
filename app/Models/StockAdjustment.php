<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'supply_id',
        'adjustment_type',
        'quantity',
        'old_stock',
        'new_stock',
        'reason_code',
        'reason_notes',
        'transaction_id',
        'adjusted_by',
        'adjusted_at',
    ];

    protected function casts(): array
    {
        return [
            'adjusted_at' => 'datetime',
        ];
    }

    /**
     * Get the session that owns this adjustment
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'session_id');
    }

    /**
     * Get the supply for this adjustment
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Get the transaction for this adjustment
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SupplyTransaction::class, 'transaction_id');
    }

    /**
     * Get the user who made this adjustment
     */
    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    /**
     * Apply the adjustment to supply stock
     */
    public function apply()
    {
        DB::beginTransaction();

        try {
            // Update supply stock
            $this->supply->update([
                'current_stock' => $this->new_stock,
            ]);

            // Create supply transaction
            $transaction = SupplyTransaction::create([
                'supply_id' => $this->supply_id,
                'type' => 'adjustment',
                'source' => 'stock_opname',
                'quantity' => $this->quantity,
                'reference_no' => 'ADJ-' . $this->id,
                'transaction_date' => $this->adjusted_at,
                'notes' => "Stock adjustment from session {$this->session->session_code}. Reason: {$this->reason_code}",
                'user_id' => $this->adjusted_by,
            ]);

            // Update this adjustment with transaction ID
            $this->update([
                'transaction_id' => $transaction->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get adjustment type badge
     */
    public function getAdjustmentTypeBadge()
    {
        if ($this->adjustment_type === 'increase') {
            return '<span class="badge badge-success">+' . $this->quantity . '</span>';
        } else {
            return '<span class="badge badge-danger">-' . $this->quantity . '</span>';
        }
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

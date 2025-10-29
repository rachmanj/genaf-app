<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'department_id',
        'request_item_id',
        'quantity',
        'distribution_date',
        'distributed_by',
        'notes',
    ];

    protected $casts = [
        'distribution_date' => 'date',
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
}
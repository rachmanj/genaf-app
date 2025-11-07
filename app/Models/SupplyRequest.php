<?php

namespace App\Models;

use App\Services\FormNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_number',
        'employee_id',
        'department_id',
        'request_date',
        'status',
        'department_head_approved_by',
        'department_head_approved_at',
        'ga_admin_approved_by',
        'ga_admin_approved_at',
        'notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'department_head_approved_at' => 'datetime',
            'ga_admin_approved_at' => 'datetime',
        ];
    }

    /**
     * Get the employee that made the request
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the department that made the request
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the department head that approved the request
     */
    public function departmentHeadApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head_approved_by');
    }

    /**
     * Get the GA admin that approved the request
     */
    public function gaAdminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ga_admin_approved_by');
    }

    /**
     * Get the request items
     */
    public function items(): HasMany
    {
        return $this->hasMany(SupplyRequestItem::class, 'request_id');
    }

    /**
     * Check if request can be approved by department head
     */
    public function canBeDeptHeadApproved(): bool
    {
        return $this->status === 'pending_dept_head';
    }

    /**
     * Check if request can be approved by GA admin
     */
    public function canBeGAAdminApproved(): bool
    {
        return $this->status === 'pending_ga_admin';
    }

    /**
     * Check if request can be fulfilled
     */
    public function canBeFulfilled(): bool
    {
        return in_array($this->status, ['approved', 'partially_fulfilled']);
    }

    /**
     * Check if request is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->status === 'pending_verification';
    }

    /**
     * Check if request is partially fulfilled
     */
    public function isPartiallyFulfilled(): bool
    {
        return $this->status === 'partially_fulfilled';
    }

    /**
     * Check if request is fully fulfilled
     */
    public function isFullyFulfilled(): bool
    {
        return $this->status === 'fulfilled';
    }

    /**
     * Check if request can be rejected by department head
     */
    public function canBeDeptHeadRejected(): bool
    {
        return $this->status === 'pending_dept_head';
    }

    /**
     * Check if request can be rejected by GA admin
     */
    public function canBeGAAdminRejected(): bool
    {
        return $this->status === 'pending_ga_admin';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->form_number)) {
                $model->form_number = FormNumberService::generateFormNumber('11', 'supply_requests');
            }
        });
    }
}

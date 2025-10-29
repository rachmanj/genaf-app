<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the users for the department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the supply requests for the department.
     */
    public function supplyRequests(): HasMany
    {
        return $this->hasMany(SupplyRequest::class);
    }

    /**
     * Get the supply distributions for the department.
     */
    public function supplyDistributions(): HasMany
    {
        return $this->hasMany(SupplyDistribution::class);
    }

    /**
     * Get the outgoing supply transactions for the department.
     */
    public function supplyTransactions(): HasMany
    {
        return $this->hasMany(SupplyTransaction::class);
    }

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get department heads (users with department head role).
     */
    public function getDepartmentHeads()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'Department Head');
        })->get();
    }

    /**
     * Generate slug from department name.
     */
    public static function generateSlug($departmentName)
    {
        return strtolower(str_replace(' ', '-', $departmentName));
    }
}
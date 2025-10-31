<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'nik',
        'department_id',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Helper methods for role checking (using Spatie Permission)
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    public function canApprove(): bool
    {
        return $this->hasAnyRole(['admin', 'manager']);
    }

    public function canViewAllDepartments(): bool
    {
        return $this->hasAnyRole(['admin', 'ga admin']);
    }

    /**
     * Get the department that the user belongs to.
     */
    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the supply requests made by this user.
     */
    public function supplyRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupplyRequest::class, 'employee_id');
    }

    /**
     * Get the supply distributions made by this user.
     */
    public function supplyDistributions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupplyDistribution::class, 'distributed_by');
    }

    /**
     * Get the supply transactions recorded by this user.
     */
    public function supplyTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupplyTransaction::class, 'user_id');
    }

    /**
     * Get the ticket reservations made by this user.
     */
    public function ticketReservations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketReservation::class, 'employee_id');
    }
}

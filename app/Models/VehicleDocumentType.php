<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'default_validity_days',
        'default_reminder_days',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'default_validity_days' => 'integer',
            'default_reminder_days' => 'integer',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }
}

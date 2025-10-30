<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit vehicles') ?? false;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');
        return [
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number,' . $vehicleId],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (int) now()->format('Y') + 1],
            'type' => ['required', 'string', 'max:50'],
            'current_odometer' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,maintenance,retired'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}



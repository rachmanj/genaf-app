<?php

namespace App\Http\Requests\VehicleMaintenance;

use Illuminate\Foundation\Http\FormRequest;

class VehicleMaintenanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit vehicle maintenance') ?? false;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'maintenance_type' => ['required', 'string', 'max:100'],
            'service_date' => ['required', 'date'],
            'odometer' => ['required', 'integer', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'vendor' => ['required', 'string', 'max:100'],
            'next_service_date' => ['nullable', 'date', 'after_or_equal:service_date'],
            'next_service_odometer' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}



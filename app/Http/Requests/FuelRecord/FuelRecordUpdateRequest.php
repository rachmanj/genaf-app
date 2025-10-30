<?php

namespace App\Http\Requests\FuelRecord;

use Illuminate\Foundation\Http\FormRequest;

class FuelRecordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit fuel records') ?? false;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'date' => ['required', 'date'],
            'odometer' => ['required', 'integer', 'min:0'],
            'liters' => ['required', 'numeric', 'min:0.01'],
            'cost' => ['required', 'numeric', 'min:0'],
            'gas_station' => ['required', 'string', 'max:100'],
            'receipt_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }
}



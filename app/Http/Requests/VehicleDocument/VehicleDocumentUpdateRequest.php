<?php

namespace App\Http\Requests\VehicleDocument;

use Illuminate\Foundation\Http\FormRequest;

class VehicleDocumentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('edit vehicle documents') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_document_type_id' => ['required', 'exists:vehicle_document_types,id'],
            'document_number' => ['required', 'string', 'max:100'],
            'document_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:document_date'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}

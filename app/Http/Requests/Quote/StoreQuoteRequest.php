<?php

namespace App\Http\Requests\Quote;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('quotes.create') || $this->user()?->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_request_id' => ['required', 'integer', 'exists:service_requests,id'],
            'service_description' => ['required', 'string', 'max:5000'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'materials_cost' => ['nullable', 'numeric', 'min:0'],
            'equipment_cost' => ['nullable', 'numeric', 'min:0'],
            'trip_charge' => ['nullable', 'numeric', 'min:0'],
            'additional_charges' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'terms_and_conditions' => ['nullable', 'string', 'max:10000'],
            'expires_at' => ['required', 'date', 'after_or_equal:today'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_request_id.exists' => 'The selected service request is invalid.',
            'expires_at.after_or_equal' => 'Quote expiration date must be today or a future date.',
        ];
    }
}

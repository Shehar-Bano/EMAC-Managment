<?php

namespace App\Http\Requests\RegionalServicePrice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteRegionalServicePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('regional_prices.bulk-delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['required', 'integer', 'exists:regional_service_prices,id'],
        ];
    }
}

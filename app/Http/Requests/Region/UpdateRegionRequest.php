<?php

namespace App\Http\Requests\Region;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('regions.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $region = $this->route('region');
        $regionId = is_object($region) ? $region->id : $region;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('regions', 'name')->ignore($regionId)->whereNull('deleted_at')],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('regions', 'slug')->ignore($regionId)->whereNull('deleted_at')],
            'code' => ['nullable', 'string', 'max:10'],
            'currency' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}

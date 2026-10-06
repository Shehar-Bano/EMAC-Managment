<?php

namespace App\Http\Requests\RegionalServicePrice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionalServicePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('regional_prices.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $priceModel = $this->route('regional_service_price');
        $priceId = is_object($priceModel) ? $priceModel->id : $priceModel;

        $regionId = $this->input('region_id', is_object($priceModel) ? $priceModel->region_id : null);
        $categoryId = $this->input('category_id', is_object($priceModel) ? $priceModel->category_id : null);

        return [
            'region_id' => [
                'required',
                'integer',
                Rule::exists('regions', 'id')->whereNull('deleted_at'),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'subcategory_id' => [
                'required',
                'integer',
                Rule::exists('subcategories', 'id')->where(function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId)
                        ->whereNull('deleted_at');
                }),
                Rule::unique('regional_service_prices', 'subcategory_id')
                    ->ignore($priceId)
                    ->where(function ($query) use ($regionId, $categoryId) {
                        return $query->where('region_id', $regionId)
                            ->where('category_id', $categoryId)
                            ->whereNull('deleted_at');
                    }),
            ],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
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
            'subcategory_id.unique' => 'A service price for this Region, Category, and Subcategory combination already exists.',
            'subcategory_id.exists' => 'The selected subcategory is invalid or does not belong to the selected category.',
            'region_id.exists' => 'The selected region is invalid or has been archived.',
            'category_id.exists' => 'The selected category is invalid or has been archived.',
        ];
    }
}

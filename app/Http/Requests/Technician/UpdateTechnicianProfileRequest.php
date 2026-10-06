<?php

namespace App\Http\Requests\Technician;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTechnicianProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            // Contact Information
            'name' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:50'],

            // Professional & Bio Details
            'bio' => ['nullable', 'string', 'max:2000'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:70'],
            'duty_status' => ['nullable', Rule::in(['on_duty', 'off_duty', 'break'])],
            'certification_id' => ['nullable', 'string', 'max:100'],
            'certification_body' => ['nullable', 'string', 'max:150'],

            // Emergency Contact Details
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],

            // Skills & Sub-Skills
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'subcategories' => ['nullable', 'array'],
            'subcategories.*' => ['integer', 'exists:subcategories,id'],
            'subcategory_ids' => ['nullable', 'array'],
            'subcategory_ids.*' => ['integer', 'exists:subcategories,id'],

            // Operating Territory / Region / Address
            'operating_territory_id' => ['nullable', 'integer', 'exists:regions,id'],
            'operating_region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'operating_region' => ['nullable', 'array'],
            'operating_region.id' => ['nullable', 'integer', 'exists:regions,id'],
            'operating_region.region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'operating_region.name' => ['nullable', 'string', 'max:150'],
            'operating_region.code' => ['nullable', 'string', 'max:50'],
            'operating_region.city' => ['nullable', 'string', 'max:100'],

            // Physical Address Details
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['nullable', 'string', 'max:50'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'addresses.*.region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'addresses.*.country' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.zipcode' => ['nullable', 'string', 'max:50'],
            'addresses.*.address' => ['nullable', 'string', 'max:500'],
            'addresses.*.is_primary' => ['nullable', 'boolean'],

            // Profile Photo Upload (Up to 5MB)
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }
}

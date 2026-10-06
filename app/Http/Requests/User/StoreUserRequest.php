<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') ?? false;
    }

    /**
     * Prepare input for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('role_id')) {
            $this->merge([
                'roles' => [(int) $this->input('role_id')],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['required', 'string', Password::min(8)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'type' => ['nullable', 'string', 'in:customers,technicians,admins'],
            'role' => ['nullable', 'string', 'max:50'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],

            // Technician Specific Attributes
            'category_id' => ['nullable', 'required_if:type,technicians', 'integer', 'exists:categories,id'],
            'subcategories' => ['nullable', 'required_if:type,technicians', 'array', 'min:1'],
            'subcategories.*' => ['integer', 'exists:subcategories,id'],
            'duty_status' => ['nullable', Rule::in(['on_duty', 'off_duty', 'break'])],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:70'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'certification_id' => ['nullable', 'string', 'max:100'],
            'certification_body' => ['nullable', 'string', 'max:150'],
            'is_verified' => ['nullable', 'boolean'],

            'addresses' => ['nullable', 'array'],
            'addresses.*.region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'addresses.*.country' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.zipcode' => ['nullable', 'string', 'max:50'],
            'addresses.*.address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'skill',
            'subcategories' => 'sub skills',
            'subcategories.*' => 'sub skill',
            'duty_status' => 'duty status',
            'experience_years' => 'years of experience',
        ];
    }
}

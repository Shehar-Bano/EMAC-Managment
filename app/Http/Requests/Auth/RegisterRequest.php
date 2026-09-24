<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends BaseAuthRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required_without:addresses', 'nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.country' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.address' => ['nullable', 'string', 'max:500'],
            'addresses.*.is_primary' => ['nullable', 'boolean'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'string', 'same:password'],
            'terms_accepted' => ['required', 'accepted'],
            'privacy_policy_accepted' => ['required', 'accepted'],
            'role' => ['required', 'string', 'in:customer'],
        ];
    }

    /**
     * Custom messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirm_password.same' => 'The password confirmation does not match.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
            'privacy_policy_accepted.accepted' => 'You must accept the privacy policy.',
            'role.in' => 'Registration is currently limited to customer accounts.',
        ];
    }
}

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
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.country' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.address' => ['nullable', 'string', 'max:500'],
        ];
    }
}

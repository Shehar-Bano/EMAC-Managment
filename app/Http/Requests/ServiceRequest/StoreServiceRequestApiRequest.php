<?php

namespace App\Http\Requests\ServiceRequest;

use App\Http\Requests\Auth\BaseAuthRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreServiceRequestApiRequest extends BaseAuthRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Handle photographs: if single file uploaded or empty string sent
        if ($this->hasFile('photographs') && ! is_array($this->file('photographs'))) {
            $this->merge([
                'photographs' => [$this->file('photographs')],
            ]);
        } elseif (! $this->hasFile('photographs')) {
            // Remove empty string or null if no actual file was uploaded
            $this->request->remove('photographs');
        }

        // Handle videos: if single file uploaded or empty string sent
        if ($this->hasFile('videos') && ! is_array($this->file('videos'))) {
            $this->merge([
                'videos' => [$this->file('videos')],
            ]);
        } elseif (! $this->hasFile('videos')) {
            // Remove empty string or null if no actual file was uploaded
            $this->request->remove('videos');
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
            'description' => ['required', 'string', 'max:5000'],
            'property_information' => ['required', 'string', 'max:2000'],
            'user_address_id' => ['required', 'integer', 'exists:user_addresses,id'],
            'preferred_service_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_service_time' => ['required', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,emergency'],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
            'photographs' => ['nullable', 'array', 'max:10'],
            'photographs.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'videos' => ['nullable', 'array', 'max:3'],
            'videos.*' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm,qt', 'max:102400'],
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
            'user_address_id.exists' => 'The selected location address does not exist.',
            'preferred_service_date.after_or_equal' => 'The preferred service date must be today or a future date.',
            'photographs.*.max' => 'Each photograph must not exceed 10MB.',
            'videos.*.max' => 'Each video must not exceed 100MB.',
        ];
    }
}

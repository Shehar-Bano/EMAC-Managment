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
        // Handle photographs: normalize photographs, photos, photo, images
        $photoFiles = $this->file('photographs')
            ?? $this->file('photos')
            ?? $this->file('photo')
            ?? $this->file('images');

        if ($photoFiles) {
            $this->merge([
                'photographs' => is_array($photoFiles) ? $photoFiles : [$photoFiles],
            ]);
        } elseif (! $this->hasFile('photographs') && ! $this->hasFile('photos')) {
            $this->request->remove('photographs');
            $this->request->remove('photos');
        }

        // Handle videos: normalize videos, video
        $videoFiles = $this->file('videos')
            ?? $this->file('video');

        if ($videoFiles) {
            $this->merge([
                'videos' => is_array($videoFiles) ? $videoFiles : [$videoFiles],
            ]);
        } elseif (! $this->hasFile('videos') && ! $this->hasFile('video')) {
            $this->request->remove('videos');
            $this->request->remove('video');
        }

        // Resolve priority & emergency flag
        if ($this->has('is_emergency')) {
            $isEmergency = filter_var($this->input('is_emergency'), FILTER_VALIDATE_BOOLEAN);
            $this->merge([
                'priority' => $isEmergency ? 'emergency' : 'normal',
                'is_emergency' => $isEmergency,
            ]);
        } elseif ($this->has('priority')) {
            $rawPriority = strtolower(trim((string) $this->input('priority')));
            $isEmergency = in_array($rawPriority, ['emergency', 'urgent', '1', 'true'], true);
            $this->merge([
                'priority' => $isEmergency ? 'emergency' : 'normal',
                'is_emergency' => $isEmergency,
            ]);
        } else {
            $this->merge([
                'priority' => 'normal',
                'is_emergency' => false,
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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:subcategories,id'],
            'description' => ['required', 'string', 'max:5000'],
            'property_information' => ['nullable', 'string', 'max:2000'],
            'user_address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'preferred_service_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_service_time' => ['required', 'string', 'max:100'],
            'is_emergency' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'string', 'in:normal,emergency'],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
            'photographs' => ['nullable', 'array', 'max:10'],
            'photographs.*' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/jpg,image/heic,image/heif', 'max:10240'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/jpg,image/heic,image/heif', 'max:10240'],
            'videos' => ['nullable', 'array', 'max:3'],
            'videos.*' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/3gpp,video/avi,video/mpeg,video/x-matroska,video/ogg,video/x-m4v,video/x-flv,video/x-ms-wmv,application/octet-stream', 'max:204800'],
            'video' => ['nullable'],
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
            'category_id.required' => 'The service category is required.',
            'category_id.exists' => 'The selected service category is invalid.',
            'subcategory_id.exists' => 'The selected service subcategory is invalid.',
            'user_address_id.exists' => 'The selected location address does not exist.',
            'preferred_service_date.after_or_equal' => 'The preferred service date must be today or a future date.',
            'photographs.*.max' => 'Each photograph must not exceed 10MB.',
            'videos.*.max' => 'Each video must not exceed 200MB.',
            'videos.*.uploaded' => 'The video file failed to upload. Please ensure the file size is within limits.',
            'video.uploaded' => 'The video file failed to upload. Please ensure the file size is within limits.',
        ];
    }
}

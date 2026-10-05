<?php

namespace App\Actions\ServiceRequest;

use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateServiceRequestAction
{
    /**
     * Create a new service request with optional photographs and videos.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): ServiceRequest
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Resolve User Address
            $userAddressId = $data['user_address_id'] ?? null;
            if (! $userAddressId) {
                $primaryAddress = $user->addresses()->where('is_primary', true)->first() ?: $user->addresses()->first();
                if ($primaryAddress) {
                    $userAddressId = $primaryAddress->id;
                } else {
                    $propertyInfo = ! empty($data['property_information']) ? $data['property_information'] : 'Customer Service Location';
                    $newAddress = $user->addresses()->create([
                        'region_id' => null,
                        'address' => $propertyInfo,
                        'city' => 'Local City',
                        'state' => 'Local State',
                        'country' => 'USA',
                        'zipcode' => '00000',
                        'is_primary' => true,
                    ]);
                    $userAddressId = $newAddress->id;
                }
            }

            $propertyInformation = ! empty($data['property_information'])
                ? $data['property_information']
                : ($user->addresses()->find($userAddressId)?->address ?? 'Customer Service Property');

            // 2. Create Service Request
            $serviceRequest = ServiceRequest::create([
                'user_id' => $user->id,
                'user_address_id' => $userAddressId,
                'category_id' => $data['category_id'] ?? null,
                'subcategory_id' => $data['subcategory_id'] ?? null,
                'description' => $data['description'],
                'property_information' => $propertyInformation,
                'preferred_service_date' => $data['preferred_service_date'],
                'preferred_service_time' => $data['preferred_service_time'],
                'priority' => ServiceRequestPriority::fromInput($data['priority'] ?? null, $data['is_emergency'] ?? null)->value,
                'additional_notes' => $data['additional_notes'] ?? null,
                'status' => ServiceRequestStatus::PENDING,
                'type' => $data['type'] ?? 'app',
            ]);

            // 3. Handle Photographs
            $photos = $data['photographs'] ?? $data['photos'] ?? $data['photo'] ?? [];
            if ($photos instanceof UploadedFile) {
                $photos = [$photos];
            }
            if (is_array($photos)) {
                foreach ($photos as $photo) {
                    if ($photo instanceof UploadedFile && $photo->isValid()) {
                        try {
                            $path = $photo->store('service_requests/photos', 'public');
                        } catch (\Throwable) {
                            $fname = Str::random(40).'.'.($photo->getClientOriginalExtension() ?: 'jpg');
                            $path = 'service_requests/photos/'.$fname;
                            Storage::disk('public')->putFileAs('service_requests/photos', $photo, $fname);
                        }

                        $serviceRequest->photographs()->create([
                            'file_path' => $path,
                            'file_name' => $photo->getClientOriginalName(),
                            'file_size' => $photo->getSize(),
                            'mime_type' => $photo->getClientMimeType(),
                        ]);
                    }
                }
            }

            // 4. Handle Videos
            $videos = $data['videos'] ?? $data['video'] ?? [];
            if ($videos instanceof UploadedFile) {
                $videos = [$videos];
            }
            if (is_array($videos)) {
                foreach ($videos as $video) {
                    if ($video instanceof UploadedFile && $video->isValid()) {
                        try {
                            $path = $video->store('service_requests/videos', 'public');
                        } catch (\Throwable) {
                            $fname = Str::random(40).'.'.($video->getClientOriginalExtension() ?: 'mp4');
                            $path = 'service_requests/videos/'.$fname;
                            Storage::disk('public')->putFileAs('service_requests/videos', $video, $fname);
                        }

                        $serviceRequest->videos()->create([
                            'file_path' => $path,
                            'file_name' => $video->getClientOriginalName(),
                            'file_size' => $video->getSize(),
                            'mime_type' => $video->getClientMimeType(),
                        ]);
                    }
                }
            }

            return $serviceRequest->load(['category', 'subcategory', 'address', 'photographs', 'videos', 'user']);
        });
    }
}

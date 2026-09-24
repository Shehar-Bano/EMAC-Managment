<?php

namespace App\Actions\ServiceRequest;

use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

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
            $serviceRequest = ServiceRequest::create([
                'user_id' => $user->id,
                'user_address_id' => $data['user_address_id'],
                'description' => $data['description'],
                'property_information' => $data['property_information'],
                'preferred_service_date' => $data['preferred_service_date'],
                'preferred_service_time' => $data['preferred_service_time'],
                'priority' => $data['priority'] ?? ServiceRequestPriority::MEDIUM->value,
                'additional_notes' => $data['additional_notes'] ?? null,
                'status' => ServiceRequestStatus::PENDING,
            ]);

            // Handle Photographs
            $photos = $data['photographs'] ?? [];
            if ($photos instanceof UploadedFile) {
                $photos = [$photos];
            }
            if (is_array($photos)) {
                foreach ($photos as $photo) {
                    if ($photo instanceof UploadedFile) {
                        $path = $photo->store('service_requests/photos', 'public');
                        $serviceRequest->photographs()->create([
                            'file_path' => $path,
                            'file_name' => $photo->getClientOriginalName(),
                            'file_size' => $photo->getSize(),
                            'mime_type' => $photo->getClientMimeType(),
                        ]);
                    }
                }
            }

            // Handle Videos
            $videos = $data['videos'] ?? [];
            if ($videos instanceof UploadedFile) {
                $videos = [$videos];
            }
            if (is_array($videos)) {
                foreach ($videos as $video) {
                    if ($video instanceof UploadedFile) {
                        $path = $video->store('service_requests/videos', 'public');
                        $serviceRequest->videos()->create([
                            'file_path' => $path,
                            'file_name' => $video->getClientOriginalName(),
                            'file_size' => $video->getSize(),
                            'mime_type' => $video->getClientMimeType(),
                        ]);
                    }
                }
            }

            return $serviceRequest->load(['address', 'photographs', 'videos', 'user']);
        });
    }
}

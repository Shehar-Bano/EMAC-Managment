<?php

namespace App\Actions\User;

use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateUserAction
{
    /**
     * Execute the user update business workflow.
     */
    public function execute(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? $user->status,
            ];

            // Handle Avatar Upload or Removal
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $updateData['avatar'] = $data['avatar']->store('avatars', 'public');
            } elseif (! empty($data['remove_avatar'])) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $updateData['avatar'] = null;
            }

            if (! empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            if (isset($data['role'])) {
                $updateData['role'] = $data['role'];
            }

            if (array_key_exists('category_id', $data)) {
                $updateData['category_id'] = $data['category_id'];
            }

            if (array_key_exists('duty_status', $data)) {
                $updateData['duty_status'] = $data['duty_status'];
            }

            if (array_key_exists('experience_years', $data)) {
                $updateData['experience_years'] = $data['experience_years'] !== null ? (int) $data['experience_years'] : null;
            }

            if (array_key_exists('bio', $data)) {
                $updateData['bio'] = $data['bio'];
            }

            if (array_key_exists('emergency_contact_name', $data)) {
                $updateData['emergency_contact_name'] = $data['emergency_contact_name'];
            }

            if (array_key_exists('emergency_contact_phone', $data)) {
                $updateData['emergency_contact_phone'] = $data['emergency_contact_phone'];
            }

            if (array_key_exists('certification_id', $data)) {
                $updateData['certification_id'] = $data['certification_id'];
            }

            if (array_key_exists('certification_body', $data)) {
                $updateData['certification_body'] = $data['certification_body'];
            }

            if (array_key_exists('is_verified', $data)) {
                $updateData['is_verified'] = (bool) $data['is_verified'];
            }

            if (array_key_exists('verified_at', $data)) {
                $updateData['verified_at'] = $data['verified_at'];
            }

            $user->update($updateData);

            if (isset($data['roles'])) {
                $user->roles()->sync($data['roles']);
            } elseif (isset($data['role'])) {
                $roleModel = Role::where('slug', $data['role'])->first();
                if ($roleModel) {
                    $user->roles()->sync([$roleModel->id]);
                }
            }

            if (isset($data['subcategories']) && is_array($data['subcategories'])) {
                $user->subcategories()->sync($data['subcategories']);
            } elseif (isset($data['subcategory_ids']) && is_array($data['subcategory_ids'])) {
                $user->subcategories()->sync($data['subcategory_ids']);
            }

            // Sync Addresses without breaking existing address IDs
            if (isset($data['addresses']) && is_array($data['addresses'])) {
                $existingAddresses = $user->addresses()->get();
                $retainedIds = [];

                foreach ($data['addresses'] as $index => $addr) {
                    if (! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country']) || ! empty($addr['state']) || ! empty($addr['zipcode']) || ! empty($addr['region_id'])) {
                        $regionId = ! empty($addr['region_id']) ? (int) $addr['region_id'] : null;
                        $region = $regionId ? Region::find($regionId) : null;
                        $isPrimary = isset($addr['is_primary']) ? (bool) $addr['is_primary'] : ($index === 0);

                        $addressAttributes = [
                            'region_id' => $regionId,
                            'country' => $addr['country'] ?? null,
                            'state' => $addr['state'] ?? $region?->name,
                            'city' => $addr['city'] ?? null,
                            'zipcode' => $addr['zipcode'] ?? null,
                            'address' => $addr['address'] ?? null,
                            'is_primary' => $isPrimary,
                        ];

                        $targetAddress = null;
                        if (! empty($addr['id'])) {
                            $targetAddress = $existingAddresses->firstWhere('id', (int) $addr['id']);
                        }

                        if (! $targetAddress) {
                            $targetAddress = $existingAddresses->first(fn ($a) => ! in_array($a->id, $retainedIds, true));
                        }

                        if ($targetAddress) {
                            $targetAddress->update($addressAttributes);
                            $retainedIds[] = $targetAddress->id;
                        } else {
                            $newAddress = $user->addresses()->create($addressAttributes);
                            $retainedIds[] = $newAddress->id;
                        }

                        if ($isPrimary && ! empty($addr['address'])) {
                            $user->address = $addr['address'];
                        }
                    }
                }

                if (! empty($retainedIds)) {
                    $user->addresses()->whereNotIn('id', $retainedIds)->delete();
                }
            }

            return $user;
        });
    }
}

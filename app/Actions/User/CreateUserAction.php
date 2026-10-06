<?php

namespace App\Actions\User;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\ProfileStatus;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    /**
     * Execute the user creation business workflow.
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $avatarPath = null;
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'avatar' => $avatarPath,
                'status' => $data['status'] ?? 'active',
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'duty_status' => $data['duty_status'] ?? 'on_duty',
                'experience_years' => isset($data['experience_years']) ? (int) $data['experience_years'] : null,
                'bio' => $data['bio'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'certification_id' => $data['certification_id'] ?? null,
                'certification_body' => $data['certification_body'] ?? null,
                'is_verified' => $data['is_verified'] ?? true,
                'verified_at' => ! empty($data['is_verified']) ? now() : ($data['verified_at'] ?? null),
                'email_verified_at' => now(),
                'phone_verified_at' => ! empty($data['phone']) ? now() : null,
                'account_status' => AccountStatus::VERIFIED,
                'profile_status' => ProfileStatus::COMPLETE,
                'source' => AuthSource::EMAIL,
            ]);

            if (! empty($data['roles'])) {
                $user->roles()->sync($data['roles']);
            } elseif (! empty($data['role'])) {
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

            if (! empty($data['addresses']) && is_array($data['addresses'])) {
                foreach ($data['addresses'] as $index => $addr) {
                    if (! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country']) || ! empty($addr['state']) || ! empty($addr['zipcode']) || ! empty($addr['region_id'])) {
                        $regionId = ! empty($addr['region_id']) ? (int) $addr['region_id'] : null;
                        $region = $regionId ? Region::find($regionId) : null;

                        $user->addresses()->create([
                            'region_id' => $regionId,
                            'country' => $addr['country'] ?? null,
                            'state' => $addr['state'] ?? $region?->name,
                            'city' => $addr['city'] ?? null,
                            'zipcode' => $addr['zipcode'] ?? null,
                            'address' => $addr['address'] ?? null,
                            'is_primary' => $index === 0,
                        ]);
                    }
                }
            }

            return $user;
        });
    }
}

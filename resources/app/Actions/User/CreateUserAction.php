<?php

namespace App\Actions\User;

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
                'email_verified_at' => now(),
            ]);

            if (! empty($data['roles'])) {
                $user->roles()->sync($data['roles']);
            } elseif (! empty($data['role'])) {
                $roleModel = Role::where('slug', $data['role'])->first();
                if ($roleModel) {
                    $user->roles()->sync([$roleModel->id]);
                }
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

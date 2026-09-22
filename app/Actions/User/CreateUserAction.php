<?php

namespace App\Actions\User;

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
                'email_verified_at' => now(),
            ]);

            if (! empty($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            if (! empty($data['addresses']) && is_array($data['addresses'])) {
                foreach ($data['addresses'] as $index => $addr) {
                    if (! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country'])) {
                        $user->addresses()->create([
                            'country' => $addr['country'] ?? null,
                            'state' => $addr['state'] ?? null,
                            'city' => $addr['city'] ?? null,
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

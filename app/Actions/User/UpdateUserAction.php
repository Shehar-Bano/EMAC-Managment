<?php

namespace App\Actions\User;

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

            $user->update($updateData);

            if (isset($data['roles'])) {
                $user->roles()->sync($data['roles']);
            } elseif (isset($data['role'])) {
                $roleModel = Role::where('slug', $data['role'])->first();
                if ($roleModel) {
                    $user->roles()->sync([$roleModel->id]);
                }
            }

            // Sync Addresses
            if (isset($data['addresses']) && is_array($data['addresses'])) {
                $user->addresses()->delete();

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

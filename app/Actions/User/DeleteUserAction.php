<?php

namespace App\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    /**
     * Execute the single user deletion with dependency safety.
     *
     * @throws Exception
     */
    public function execute(User $user): bool
    {
        $user->loadMissing('roles');

        if (Auth::id() === $user->id) {
            throw new Exception('You cannot delete your own active administrator account.');
        }

        if ($user->isSuperAdmin() && User::whereHas('roles', fn ($q) => $q->where('slug', 'super-admin'))->count() <= 1) {
            throw new Exception('Cannot delete the last remaining Super Admin account.');
        }

        return DB::transaction(function () use ($user) {
            $user->roles()->detach();

            return (bool) $user->delete();
        });
    }
}

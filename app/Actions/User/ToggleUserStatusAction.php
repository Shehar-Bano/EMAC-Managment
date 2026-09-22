<?php

namespace App\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class ToggleUserStatusAction
{
    /**
     * Toggle the active/inactive status of a user.
     *
     * @throws Exception
     */
    public function execute(User $user): User
    {
        if (Auth::id() === $user->id) {
            throw new Exception('You cannot deactivate your own administrative account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return $user;
    }
}

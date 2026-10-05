<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BulkDeleteUsersAction
{
    /**
     * Execute bulk user deletion.
     *
     * @param  list<int>  $userIds
     * @return array{deleted: int, skipped: int}
     */
    public function execute(array $userIds): array
    {
        $currentUserId = Auth::id();
        $users = User::with('roles')->whereIn('id', $userIds)->get();

        $deletedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($users, $currentUserId, &$deletedCount, &$skippedCount) {
            foreach ($users as $user) {
                // Skip self-deletion or protected root user
                if ($user->id === $currentUserId || ($user->isSuperAdmin() && $user->id === 1)) {
                    $skippedCount++;

                    continue;
                }

                $user->roles()->detach();
                $user->delete();
                $deletedCount++;
            }
        });

        return [
            'deleted' => $deletedCount,
            'skipped' => $skippedCount,
        ];
    }
}

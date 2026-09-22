<?php

namespace App\Actions\Role;

use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteRoleAction
{
    /**
     * Delete a non-system role with dependency safety.
     *
     * @throws Exception
     */
    public function execute(Role $role): bool
    {
        if ($role->is_system) {
            throw new Exception("System core role '{$role->name}' cannot be removed.");
        }

        if ($role->users()->exists()) {
            $userCount = $role->users()->count();
            throw new Exception("Cannot delete role '{$role->name}' because it is assigned to {$userCount} employee(s). Reassign them first.");
        }

        return DB::transaction(function () use ($role) {
            $role->permissions()->detach();

            return (bool) $role->delete();
        });
    }
}

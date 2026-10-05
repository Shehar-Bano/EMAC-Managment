<?php

namespace App\Actions\Role;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateRoleAction
{
    /**
     * Update role details and synchronize permissions.
     */
    public function execute(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $updateData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ];

            if (! $role->is_system) {
                $updateData['slug'] = Str::slug($data['name']);
            }

            $role->update($updateData);

            if (isset($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }

            return $role;
        });
    }
}

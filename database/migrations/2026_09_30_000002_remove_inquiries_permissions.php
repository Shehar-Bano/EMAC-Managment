<?php

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = Permission::where('name', 'like', 'inquiries.%')->get();
        foreach ($permissions as $permission) {
            $permission->roles()->detach();
            $permission->delete();
        }

        PermissionGroup::where('slug', 'inquiries')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as inquiries module is retired
    }
};

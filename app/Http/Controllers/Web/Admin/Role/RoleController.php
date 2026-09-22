<?php

namespace App\Http\Controllers\Web\Admin\Role;

use App\Actions\Role\CreateRoleAction;
use App\Actions\Role\DeleteRoleAction;
use App\Actions\Role\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\PermissionGroup;
use App\Models\Role;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of security roles.
     */
    public function index(Request $request): View
    {
        $this->authorize('roles.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = Role::withCount(['users', 'permissions'])
            ->filter($request->only(['search', 'from_date', 'to_date']))
            ->latest();

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $roles = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $roles = $query->paginate($perPage);
        }

        return view('dashboard.modules.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $this->authorize('roles.create');

        $permissionGroups = PermissionGroup::with('permissions')
            ->orderBy('sort_order')
            ->get();

        return view('dashboard.modules.roles.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request, CreateRoleAction $action): RedirectResponse
    {
        $role = $action->execute($request->validated());

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', "Security role '{$role->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $this->authorize('roles.edit');

        $role->load('permissions');
        $permissionGroups = PermissionGroup::with('permissions')
            ->orderBy('sort_order')
            ->get();

        return view('dashboard.modules.roles.edit', compact('role', 'permissionGroups'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): RedirectResponse
    {
        $action->execute($role, $request->validated());

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', "Security role '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role, DeleteRoleAction $action): RedirectResponse
    {
        $this->authorize('roles.delete');

        try {
            $roleName = $role->name;
            $action->execute($role);

            return redirect()
                ->route('dashboard.roles.index')
                ->with('success', "Role '{$roleName}' has been removed.");
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.roles.index')
                ->with('error', $e->getMessage());
        }
    }
}

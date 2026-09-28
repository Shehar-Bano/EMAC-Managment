<?php

namespace App\Http\Controllers\Api\V1\Role;

use App\Actions\Role\CreateRoleAction;
use App\Actions\Role\DeleteRoleAction;
use App\Actions\Role\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('roles.view');

        $perPage = (int) $request->get('per_page', 15);
        $roles = Role::withCount(['users', 'permissions'])
            ->with('permissions')
            ->filter($request->only(['search', 'from_date', 'to_date']))
            ->latest()
            ->paginate($perPage);

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        $role = $action->execute($request->validated());

        return (new RoleResource($role->load(['permissions'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): RoleResource
    {
        $this->authorize('roles.view');

        return new RoleResource($role->load(['permissions'])->loadCount(['users', 'permissions']));
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): RoleResource
    {
        $updated = $action->execute($role, $request->validated());

        return new RoleResource($updated->load(['permissions'])->loadCount(['users', 'permissions']));
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role, DeleteRoleAction $action): JsonResponse
    {
        $this->authorize('roles.delete');

        try {
            $action->execute($role);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

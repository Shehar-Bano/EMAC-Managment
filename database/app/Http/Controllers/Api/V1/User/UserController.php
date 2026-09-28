<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\User\BulkDeleteUsersAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\ToggleUserStatusAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BulkDeleteUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Display a paginated listing of users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('users.view');

        $perPage = (int) $request->get('per_page', 15);
        $users = User::with(['roles', 'addresses'])
            ->filter($request->only(['search', 'status', 'role_id', 'from_date', 'to_date']))
            ->latest()
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action): JsonResponse
    {
        $user = $action->execute($request->validated());

        return (new UserResource($user->load(['roles', 'addresses'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): UserResource
    {
        $this->authorize('users.view');

        return new UserResource($user->load(['roles.permissions', 'addresses']));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): UserResource
    {
        $updatedUser = $action->execute($user, $request->validated());

        return new UserResource($updatedUser->load(['roles', 'addresses']));
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        $this->authorize('users.delete');

        try {
            $action->execute($user);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete users.
     */
    public function bulkDelete(BulkDeleteUserRequest $request, BulkDeleteUsersAction $action): JsonResponse
    {
        $result = $action->execute($request->validated('ids'));

        return response()->json([
            'success' => true,
            'message' => "Bulk delete operation completed. Deleted: {$result['deleted']}, Skipped: {$result['skipped']}.",
            'data' => $result,
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(User $user, ToggleUserStatusAction $action): JsonResponse
    {
        $this->authorize('users.status');

        try {
            $updated = $action->execute($user);

            return response()->json([
                'success' => true,
                'message' => "User status updated to {$updated->status}.",
                'data' => new UserResource($updated),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

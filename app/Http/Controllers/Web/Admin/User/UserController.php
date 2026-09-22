<?php

namespace App\Http\Controllers\Web\Admin\User;

use App\Actions\User\BulkDeleteUsersAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\ToggleUserStatusAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BulkDeleteUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users with filters and dynamic pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('users.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = User::with(['roles'])
            ->filter($request->only(['search', 'status', 'role_id', 'from_date', 'to_date']))
            ->latest();

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $users = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $users = $query->paginate($perPage);
        }

        $roles = Role::orderBy('name')->get();

        return view('dashboard.modules.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $this->authorize('users.create');

        $roles = Role::orderBy('name')->get();

        return view('dashboard.modules.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $user = $action->execute($request->validated());

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', "Staff account for '{$user->name}' created successfully.");
    }

    /**
     * Display the specified user profile.
     */
    public function show(User $user): View
    {
        $this->authorize('users.view');

        $user->load(['roles.permissions', 'addresses']);

        return view('dashboard.modules.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorize('users.edit');

        $user->load(['roles', 'addresses']);
        $roles = Role::orderBy('name')->get();

        return view('dashboard.modules.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $action->execute($user, $request->validated());

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', "Staff account for '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        $this->authorize('users.delete');

        try {
            $userName = $user->name;
            $action->execute($user);

            return redirect()
                ->route('dashboard.users.index')
                ->with('success', "User '{$userName}' has been deleted successfully.");
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.users.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete selected users.
     */
    public function bulkDelete(BulkDeleteUserRequest $request, BulkDeleteUsersAction $action): JsonResponse|RedirectResponse
    {
        $result = $action->execute($request->validated('ids'));

        $message = "Deleted {$result['deleted']} selected staff account(s).";
        if ($result['skipped'] > 0) {
            $message .= " ({$result['skipped']} account(s) skipped due to protection rules).";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('dashboard.users.index')->with('success', $message);
    }

    /**
     * Toggle the active/inactive status of a user.
     */
    public function toggleStatus(User $user, ToggleUserStatusAction $action): RedirectResponse
    {
        $this->authorize('users.status');

        try {
            $updatedUser = $action->execute($user);

            return back()->with('success', "Status for '{$updatedUser->name}' changed to ".ucfirst($updatedUser->status).'.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export users matching active filter criteria to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('users.export');

        $users = User::with(['roles'])
            ->filter($request->only(['search', 'status', 'role_id', 'from_date', 'to_date']))
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Date Registered']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? 'N/A',
                    $user->roles->pluck('name')->implode(', ') ?: 'None',
                    ucfirst($user->status),
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

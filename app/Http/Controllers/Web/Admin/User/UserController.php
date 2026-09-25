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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users segmented by Customers, Technicians, and Administrators.
     */
    public function index(Request $request): View
    {
        $this->authorize('users.view');

        $activeType = $request->get('type', 'customers');
        if (! in_array($activeType, ['customers', 'technicians', 'admins', 'all'], true)) {
            $activeType = 'customers';
        }

        $counts = [
            'customers' => User::where(function (Builder $q) {
                $q->whereHas('roles', fn ($rq) => $rq->where('slug', 'customer'))
                    ->orWhere('role', 'customer');
            })->count(),
            'technicians' => User::where(function (Builder $q) {
                $q->whereHas('roles', fn ($rq) => $rq->where('slug', 'technician'))
                    ->orWhere('role', 'technician');
            })->count(),
            'admins' => User::where(function (Builder $q) {
                $q->whereHas('roles', fn ($rq) => $rq->whereIn('slug', ['super-admin', 'admin']))
                    ->orWhereIn('role', ['super-admin', 'admin']);
            })->count(),
            'all' => User::count(),
        ];

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = User::with(['roles', 'addresses'])
            ->filter($request->only(['search', 'status', 'role_id', 'from_date', 'to_date']))
            ->when($activeType !== 'all', function (Builder $q) use ($activeType) {
                if ($activeType === 'customers') {
                    $q->where(function (Builder $sub) {
                        $sub->whereHas('roles', fn ($rq) => $rq->where('slug', 'customer'))
                            ->orWhere('role', 'customer');
                    });
                } elseif ($activeType === 'technicians') {
                    $q->where(function (Builder $sub) {
                        $sub->whereHas('roles', fn ($rq) => $rq->where('slug', 'technician'))
                            ->orWhere('role', 'technician');
                    });
                } elseif ($activeType === 'admins') {
                    $q->where(function (Builder $sub) {
                        $sub->whereHas('roles', fn ($rq) => $rq->whereIn('slug', ['super-admin', 'admin']))
                            ->orWhereIn('role', ['super-admin', 'admin']);
                    });
                }
            })
            ->latest();

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $users = $query->paginate(max($totalCount, 1))->withQueryString();
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $users = $query->paginate($perPage)->withQueryString();
        }

        $roles = Role::orderBy('name')->get();

        return view('dashboard.modules.users.index', compact('users', 'roles', 'activeType', 'counts'));
    }

    /**
     * Show the form for creating a new user based on category type (Customer, Technician, Admin).
     */
    public function create(Request $request): View
    {
        $this->authorize('users.create');

        $activeType = $request->get('type', 'customers');
        if (! in_array($activeType, ['customers', 'technicians', 'admins'], true)) {
            $activeType = 'customers';
        }

        $targetRoleSlug = match ($activeType) {
            'customers' => 'customer',
            'technicians' => 'technician',
            'admins' => 'super-admin',
            default => 'customer',
        };

        $roles = Role::orderBy('name')->get();
        $defaultRole = $roles->firstWhere('slug', $targetRoleSlug) ?? $roles->first();

        return view('dashboard.modules.users.create', compact('roles', 'activeType', 'defaultRole'));
    }

    /**
     * Store a newly created user in storage with appropriate role assignment.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $validated = $request->validated();
        $type = $request->get('type', 'customers');

        $targetRoleSlug = match ($type) {
            'technicians' => 'technician',
            'admins' => 'super-admin',
            default => 'customer',
        };

        // If roles not explicitly selected, assign the default role for the tab
        if (empty($validated['roles'])) {
            $role = Role::where('slug', $targetRoleSlug)->first();
            if ($role) {
                $validated['roles'] = [$role->id];
            }
        }

        $validated['role'] = $targetRoleSlug;

        $user = $action->execute($validated);

        $typeLabel = match ($type) {
            'customers' => 'Customer',
            'technicians' => 'Technician / Employee',
            'admins' => 'Administrator User',
            default => 'User',
        };

        return redirect()
            ->route('dashboard.users.index', ['type' => $type])
            ->with('success', "{$typeLabel} account for '{$user->name}' created successfully.");
    }

    /**
     * Display the specified user profile.
     */
    public function show(User $user, Request $request): View
    {
        $this->authorize('users.view');

        $user->load(['roles.permissions', 'addresses', 'serviceRequests.latestQuote', 'quotes']);

        $activeType = $request->get('type', $this->resolveUserType($user));

        return view('dashboard.modules.users.show', compact('user', 'activeType'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user, Request $request): View
    {
        $this->authorize('users.edit');

        $user->load(['roles', 'addresses']);
        $roles = Role::orderBy('name')->get();
        $activeType = $request->get('type', $this->resolveUserType($user));

        return view('dashboard.modules.users.edit', compact('user', 'roles', 'activeType'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $validated = $request->validated();
        $type = $request->get('type', $this->resolveUserType($user));

        $action->execute($user, $validated);

        return redirect()
            ->route('dashboard.users.index', ['type' => $type])
            ->with('success', "Account profile for '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user, DeleteUserAction $action, Request $request): RedirectResponse
    {
        $this->authorize('users.delete');

        $type = $request->get('type', $this->resolveUserType($user));

        try {
            $userName = $user->name;
            $action->execute($user);

            return redirect()
                ->route('dashboard.users.index', ['type' => $type])
                ->with('success', "User '{$userName}' has been deleted successfully.");
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.users.index', ['type' => $type])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete selected users.
     */
    public function bulkDelete(BulkDeleteUserRequest $request, BulkDeleteUsersAction $action): JsonResponse|RedirectResponse
    {
        $result = $action->execute($request->validated('ids'));
        $type = $request->get('type', 'customers');

        $message = "Deleted {$result['deleted']} selected account(s).";
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

        return redirect()->route('dashboard.users.index', ['type' => $type])->with('success', $message);
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
     * Export users matching active filter criteria and tab to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('users.export');

        $activeType = $request->get('type', 'all');

        $users = User::with(['roles', 'addresses'])
            ->filter($request->only(['search', 'status', 'role_id', 'from_date', 'to_date']))
            ->when($activeType !== 'all', function (Builder $q) use ($activeType) {
                if ($activeType === 'customers') {
                    $q->where(function (Builder $sub) {
                        $sub->whereHas('roles', fn ($rq) => $rq->where('slug', 'customer'))
                            ->orWhere('role', 'customer');
                    });
                } elseif ($activeType === 'technicians') {
                    $q->where(function (Builder $sub) {
                        $sub->whereHas('roles', fn ($rq) => $rq->where('slug', 'technician'))
                            ->orWhere('role', 'technician');
                    });
                } elseif ($activeType === 'admins') {
                    $q->where(function (Builder $sub) {
                        $sub->whereHas('roles', fn ($rq) => $rq->whereIn('slug', ['super-admin', 'admin']))
                            ->orWhereIn('role', ['super-admin', 'admin']);
                    });
                }
            })
            ->get();

        $filenameType = $activeType !== 'all' ? $activeType : 'users';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filenameType}_export_".date('Y_m_d_His').'.csv"',
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
                    $user->roles->pluck('name')->implode(', ') ?: ($user->role ? ucfirst($user->role) : 'None'),
                    ucfirst($user->status),
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Resolve the primary tab category key for a user.
     */
    protected function resolveUserType(User $user): string
    {
        if ($user->hasRole(['super-admin', 'admin']) || in_array($user->role, ['super-admin', 'admin'], true)) {
            return 'admins';
        }

        if ($user->hasRole('technician') || $user->role === 'technician') {
            return 'technicians';
        }

        return 'customers';
    }
}

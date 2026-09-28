<?php

namespace App\Http\Controllers\Web\Admin\Permission;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use Illuminate\Contracts\View\View;

class PermissionController extends Controller
{
    /**
     * Display a comprehensive overview of all system permission groups.
     */
    public function index(): View
    {
        $this->authorize('roles.view');

        $groups = PermissionGroup::with(['permissions.roles'])
            ->orderBy('sort_order')
            ->get();

        return view('dashboard.modules.permissions.index', compact('groups'));
    }
}

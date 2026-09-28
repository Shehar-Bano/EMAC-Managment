<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Region;
use App\Models\RegionalServicePrice;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main ERP administrative dashboard overview.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_categories' => Category::count(),
            'total_subcategories' => Subcategory::count(),
            'total_regions' => Region::count(),
            'total_regional_prices' => RegionalServicePrice::count(),
        ];

        $recentUsers = User::with(['roles'])
            ->latest()
            ->take(6)
            ->get();

        $roles = Role::withCount('users')
            ->orderBy('name')
            ->get();

        $categories = Category::withCount('subcategories')
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentUsers', 'roles', 'categories'));
    }
}

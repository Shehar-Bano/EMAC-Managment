<?php

use App\Http\Controllers\Web\Admin\Category\CategoryController;
use App\Http\Controllers\Web\Admin\Category\SubcategoryController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\Inquiry\InquiryController;
use App\Http\Controllers\Web\Admin\Legal\PrivacyController;
use App\Http\Controllers\Web\Admin\Legal\TermsController;
use App\Http\Controllers\Web\Admin\Permission\PermissionController;
use App\Http\Controllers\Web\Admin\Quote\QuoteController;
use App\Http\Controllers\Web\Admin\Region\RegionController;
use App\Http\Controllers\Web\Admin\RegionalServicePrice\RegionalServicePriceController;
use App\Http\Controllers\Web\Admin\Role\RoleController;
use App\Http\Controllers\Web\Admin\ServiceRequest\ServiceRequestController;
use App\Http\Controllers\Web\Admin\Setting\SettingController;
use App\Http\Controllers\Web\Admin\User\UserController;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\WebsiteController;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Public Website Routes (resources/views/website/)
|--------------------------------------------------------------------------
*/
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/services', [WebsiteController::class, 'services'])->name('services');
Route::get('/faq', [WebsiteController::class, 'faq'])->name('faq');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::post('/contact', [WebsiteController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy-policy', [WebsiteController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [WebsiteController::class, 'terms'])->name('terms');

// Direct storage file serving fallback (ensures images work even if host symlink is missing or broken)
$serveStorageFile = function (string $path) {
    $cleanPath = ltrim($path, '/');
    $candidates = [
        storage_path('app/public/'.$cleanPath),
        storage_path('app/'.$cleanPath),
        public_path('storage/'.$cleanPath),
        storage_path('app/private/'.$cleanPath),
        public_path($cleanPath),
    ];

    $targetFile = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && ! is_dir($candidate)) {
            $targetFile = $candidate;
            break;
        }
    }

    if (! $targetFile) {
        abort(404, 'File not found on server.');
    }

    $mimeType = mime_content_type($targetFile) ?: 'application/octet-stream';

    return response()->file($targetFile, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
        'Access-Control-Allow-Origin' => '*',
    ]);
};

Route::get('storage/{path}', $serveStorageFile)->where('path', '.*')->name('storage.fallback');
Route::get('public/storage/{path}', $serveStorageFile)->where('path', '.*');

// Quick storage link repair route
Route::get('/fix-storage', function () {
    try {
        Artisan::call('storage:link');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return '<h2 style="color:green; font-family:sans-serif;">Storage link created & caches cleared successfully!</h2>';
    } catch (Throwable $e) {
        return '<h2 style="color:red; font-family:sans-serif;">Error: '.$e->getMessage().'</h2>';
    }
});

// Diagnostic storage check
Route::get('/debug-storage', function () {
    $publicStorage = storage_path('app/public');
    $symlinkTarget = public_path('storage');

    $listDir = function (string $dir) {
        return is_dir($dir) ? array_diff(scandir($dir) ?: [], ['.', '..']) : [];
    };

    $files = $listDir($publicStorage);
    $avatarFiles = $listDir($publicStorage.'/avatars');
    $categoryFiles = $listDir($publicStorage.'/categories');
    $subcategoryFiles = $listDir($publicStorage.'/subcategories');

    $firstUser = User::whereNotNull('avatar')->first() ?? User::first();
    $firstCategory = Category::whereNotNull('image')->first();
    $firstSubcategory = Subcategory::whereNotNull('image')->first();

    return response()->json([
        'status' => 'Diagnostic Storage Report',
        'app_url' => config('app.url'),
        'current_request_root' => request()->root(),
        'storage_path_app_public' => $publicStorage,
        'storage_path_exists' => is_dir($publicStorage),
        'storage_path_writable' => is_writable($publicStorage),
        'public_path_storage' => $symlinkTarget,
        'public_path_storage_exists' => file_exists($symlinkTarget),
        'public_path_is_link' => is_link($symlinkTarget),
        'folders_in_storage' => array_values($files),
        'files_count' => [
            'avatars' => count($avatarFiles),
            'categories' => count($categoryFiles),
            'subcategories' => count($subcategoryFiles),
        ],
        'sample_files' => [
            'avatars' => array_slice(array_values($avatarFiles), 0, 5),
            'categories' => array_slice(array_values($categoryFiles), 0, 5),
            'subcategories' => array_slice(array_values($subcategoryFiles), 0, 5),
        ],
        'test_entities' => [
            'user' => [
                'name' => $firstUser?->name,
                'avatar_db' => $firstUser?->avatar,
                'avatar_url' => $firstUser?->avatar_url,
            ],
            'category' => [
                'name' => $firstCategory?->name,
                'image_db' => $firstCategory?->image,
                'image_url' => $firstCategory?->image_url,
            ],
            'subcategory' => [
                'name' => $firstSubcategory?->name,
                'image_db' => $firstSubcategory?->image,
                'image_url' => $firstSubcategory?->image_url,
            ],
        ],

    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

/*
|--------------------------------------------------------------------------
| 2. Authentication Routes (resources/views/auth/)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 3. Authenticated ERP Dashboard Routes (resources/views/dashboard/)
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'active.user'])->group(function () {
    // Dashboard Overview
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Leads & Inquiries Module
    Route::get('inquiries/export', [InquiryController::class, 'export'])->name('inquiries.export');
    Route::post('inquiries/bulk-delete', [InquiryController::class, 'bulkDelete'])->name('inquiries.bulk-delete');
    Route::delete('inquiries/bulk-delete', [InquiryController::class, 'bulkDelete']);
    Route::patch('inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.status');
    Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'destroy']);

    // Customer Service Requests Module
    Route::get('service-requests/export', [ServiceRequestController::class, 'export'])->name('service-requests.export');
    Route::post('service-requests/bulk-delete', [ServiceRequestController::class, 'bulkDelete'])->name('service-requests.bulk-delete');
    Route::delete('service-requests/bulk-delete', [ServiceRequestController::class, 'bulkDelete']);
    Route::patch('service-requests/{service_request}/status', [ServiceRequestController::class, 'updateStatus'])->name('service-requests.status');
    Route::resource('service-requests', ServiceRequestController::class)->only(['index', 'show', 'destroy']);

    // Quotes & Estimates Module
    Route::get('quotes/{quote}/print', [QuoteController::class, 'print'])->name('quotes.print');
    Route::patch('quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('quotes.status');
    Route::resource('quotes', QuoteController::class)->only(['index', 'store', 'show', 'destroy']);

    // Category Management Module
    Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    Route::delete('categories/bulk-delete', [CategoryController::class, 'bulkDelete']);
    Route::patch('categories/{category}/status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::resource('categories', CategoryController::class);

    // Subcategory Management Module
    Route::get('subcategories/export', [SubcategoryController::class, 'export'])->name('subcategories.export');
    Route::post('subcategories/bulk-delete', [SubcategoryController::class, 'bulkDelete'])->name('subcategories.bulk-delete');
    Route::delete('subcategories/bulk-delete', [SubcategoryController::class, 'bulkDelete']);
    Route::patch('subcategories/{subcategory}/status', [SubcategoryController::class, 'toggleStatus'])->name('subcategories.toggle-status');
    Route::resource('subcategories', SubcategoryController::class);

    // Service Regions Module
    Route::get('regions/export', [RegionController::class, 'export'])->name('regions.export');
    Route::post('regions/bulk-delete', [RegionController::class, 'bulkDelete'])->name('regions.bulk-delete');
    Route::delete('regions/bulk-delete', [RegionController::class, 'bulkDelete']);
    Route::patch('regions/{region}/status', [RegionController::class, 'toggleStatus'])->name('regions.toggle-status');
    Route::resource('regions', RegionController::class);

    // Regional Service Pricing Module
    Route::get('regional-service-prices/export', [RegionalServicePriceController::class, 'export'])->name('regional-service-prices.export');
    Route::get('regional-service-prices/subcategories/{category}', [RegionalServicePriceController::class, 'getSubcategories'])->name('regional-service-prices.subcategories');
    Route::post('regional-service-prices/bulk-delete', [RegionalServicePriceController::class, 'bulkDelete'])->name('regional-service-prices.bulk-delete');
    Route::delete('regional-service-prices/bulk-delete', [RegionalServicePriceController::class, 'bulkDelete']);
    Route::patch('regional-service-prices/{regional_service_price}/status', [RegionalServicePriceController::class, 'toggleStatus'])->name('regional-service-prices.toggle-status');
    Route::resource('regional-service-prices', RegionalServicePriceController::class);

    // User Management Module
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');
    Route::resource('users', UserController::class);

    // Role Management Module
    Route::resource('roles', RoleController::class);

    // Permission Groups & Matrix
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');

    // System Settings Module
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('settings/credentials', [SettingController::class, 'updateCredentials'])->name('settings.credentials');
    Route::delete('settings/logo/{type}', [SettingController::class, 'removeLogo'])->name('settings.remove-logo');

    // Legal & Compliance Module (Terms & Privacy)
    Route::get('terms', [TermsController::class, 'index'])->name('terms.index');
    Route::get('terms/edit', [TermsController::class, 'edit'])->name('terms.edit');
    Route::put('terms', [TermsController::class, 'update'])->name('terms.update');

    Route::get('privacy', [PrivacyController::class, 'index'])->name('privacy.index');
    Route::get('privacy/edit', [PrivacyController::class, 'edit'])->name('privacy.edit');
    Route::put('privacy', [PrivacyController::class, 'update'])->name('privacy.update');
});

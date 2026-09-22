<?php

use App\Http\Controllers\Web\Admin\Category\CategoryController;
use App\Http\Controllers\Web\Admin\Category\SubcategoryController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\Inquiry\InquiryController;
use App\Http\Controllers\Web\Admin\Legal\PrivacyController;
use App\Http\Controllers\Web\Admin\Legal\TermsController;
use App\Http\Controllers\Web\Admin\Permission\PermissionController;
use App\Http\Controllers\Web\Admin\Role\RoleController;
use App\Http\Controllers\Web\Admin\Setting\SettingController;
use App\Http\Controllers\Web\Admin\User\UserController;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\WebsiteController;
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
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
    // Dashboard Overview
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Leads & Inquiries Module
    Route::get('inquiries/export', [InquiryController::class, 'export'])->name('inquiries.export');
    Route::post('inquiries/bulk-delete', [InquiryController::class, 'bulkDelete'])->name('inquiries.bulk-delete');
    Route::delete('inquiries/bulk-delete', [InquiryController::class, 'bulkDelete']);
    Route::patch('inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.status');
    Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'destroy']);

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

    // Legal & Compliance Module (Terms & Privacy)
    Route::get('terms', [TermsController::class, 'index'])->name('terms.index');
    Route::get('terms/edit', [TermsController::class, 'edit'])->name('terms.edit');
    Route::put('terms', [TermsController::class, 'update'])->name('terms.update');

    Route::get('privacy', [PrivacyController::class, 'index'])->name('privacy.index');
    Route::get('privacy/edit', [PrivacyController::class, 'edit'])->name('privacy.edit');
    Route::put('privacy', [PrivacyController::class, 'update'])->name('privacy.update');
});

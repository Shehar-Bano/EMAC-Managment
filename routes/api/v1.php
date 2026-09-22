<?php

use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\Api\V1\Category\SubcategoryController;
use App\Http\Controllers\Api\V1\Role\RoleController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes — EMAC ERP
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Category Endpoints
    Route::delete('categories/bulk-delete', [CategoryController::class, 'bulkDelete']);
    Route::patch('categories/{category}/status', [CategoryController::class, 'toggleStatus']);
    Route::apiResource('categories', CategoryController::class);

    // Subcategory Endpoints
    Route::delete('subcategories/bulk-delete', [SubcategoryController::class, 'bulkDelete']);
    Route::patch('subcategories/{subcategory}/status', [SubcategoryController::class, 'toggleStatus']);
    Route::apiResource('subcategories', SubcategoryController::class);

    // User Management Endpoints
    Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete']);
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus']);
    Route::apiResource('users', UserController::class);

    // Role Management Endpoints
    Route::apiResource('roles', RoleController::class);
});

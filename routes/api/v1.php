<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\Api\V1\Category\SubcategoryController;
use App\Http\Controllers\Api\V1\Legal\LegalDocumentController;
use App\Http\Controllers\Api\V1\Pricing\RegionalServicePriceController;
use App\Http\Controllers\Api\V1\Quote\QuoteController;
use App\Http\Controllers\Api\V1\Region\RegionController;
use App\Http\Controllers\Api\V1\Role\RoleController;
use App\Http\Controllers\Api\V1\ServiceRequest\ServiceRequestController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes — EMAC ERP & Customer Application
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // ---------------------------------------------------------
    // Customer Authentication & User Profile Endpoints
    // ---------------------------------------------------------
    Route::prefix('auth')->group(function () {
        // Public Auth Endpoints
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('social', [AuthController::class, 'social']);

        // OTP Endpoints
        Route::post('otp/send', [OtpController::class, 'send']);
        Route::post('otp/verify', [OtpController::class, 'verify']);

        // Password Reset Endpoints
        Route::post('password/forgot', [PasswordController::class, 'forgotPassword']);
        Route::post('password/reset', [PasswordController::class, 'resetPassword']);

        // Authenticated Auth & Password Endpoints
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('password/change/request-otp', [PasswordController::class, 'requestChangeOtp']);
            Route::post('password/change', [PasswordController::class, 'changePassword']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('profile', [ProfileController::class, 'show']);
            Route::match(['put', 'patch'], 'profile', [ProfileController::class, 'update']);
        });
    });

    // Profile Management Endpoints (GET/PUT/PATCH /api/v1/profile)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::match(['put', 'patch'], 'profile', [ProfileController::class, 'update']);

        // Customer Service Request Endpoints
        Route::get('service-requests', [ServiceRequestController::class, 'index']);
        Route::post('service-requests', [ServiceRequestController::class, 'store']);
        Route::get('service-requests/{id}', [ServiceRequestController::class, 'show']);

        // Customer Quote Endpoints
        Route::get('quotes/{id}', [QuoteController::class, 'show']);
        Route::post('quotes/{id}/respond', [QuoteController::class, 'respond']);
    });

    // ---------------------------------------------------------
    // Public Legal & Compliance Endpoints
    // ---------------------------------------------------------
    Route::get('terms', [LegalDocumentController::class, 'getTerms']);
    Route::get('privacy', [LegalDocumentController::class, 'getPrivacy']);
    Route::get('legal/terms', [LegalDocumentController::class, 'getTerms']);
    Route::get('legal/privacy', [LegalDocumentController::class, 'getPrivacy']);

    // ---------------------------------------------------------
    // Admin / ERP Management Endpoints
    // ---------------------------------------------------------
    // Category Endpoints
    Route::delete('categories/bulk-delete', [CategoryController::class, 'bulkDelete']);
    Route::patch('categories/{category}/status', [CategoryController::class, 'toggleStatus']);
    Route::apiResource('categories', CategoryController::class);

    // Subcategory Endpoints
    Route::delete('subcategories/bulk-delete', [SubcategoryController::class, 'bulkDelete']);
    Route::patch('subcategories/{subcategory}/status', [SubcategoryController::class, 'toggleStatus']);
    Route::apiResource('subcategories', SubcategoryController::class);

    // Region Endpoints
    Route::delete('regions/bulk-delete', [RegionController::class, 'bulkDelete']);
    Route::patch('regions/{region}/status', [RegionController::class, 'toggleStatus']);
    Route::apiResource('regions', RegionController::class);

    // Regional Service Pricing Endpoints
    Route::delete('regional-service-prices/bulk-delete', [RegionalServicePriceController::class, 'bulkDelete']);
    Route::patch('regional-service-prices/{regional_service_price}/status', [RegionalServicePriceController::class, 'toggleStatus']);
    Route::apiResource('regional-service-prices', RegionalServicePriceController::class);

    // User Management Endpoints
    Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete']);
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus']);
    Route::apiResource('users', UserController::class);

    // Role Management Endpoints
    Route::apiResource('roles', RoleController::class);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Auth\PasswordController;
use App\Http\Controllers\API\V1\Auth\SocialiteController;
use App\Http\Controllers\API\V1\Client\UserController;
use App\Http\Controllers\API\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\V1\Admin\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Health Check
Route::get('/', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'version' => 'v1',
        'timestamp' => now()->toIso8601String()
    ]);
});

// API v1 Routes
Route::prefix('v1')->group(function () {
    
    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        // Registration & Login
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        // Password Reset
        Route::post('/forgot-password', [PasswordController::class, 'forgotPassword']);
        Route::post('/reset-password', [PasswordController::class, 'resetPassword']);
        
        // OAuth Authentication
        Route::get('/{provider}/redirect', [SocialiteController::class, 'redirectToProvider']);
        Route::get('/{provider}/callback', [SocialiteController::class, 'handleProviderCallback']);
        Route::post('/{provider}/token', [SocialiteController::class, 'handleProviderToken']);
        
        // Authentication Routes (Protected)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
    });
    
    // User Routes (Protected)
    Route::middleware('auth:sanctum')->group(function () {
        // Profile Management
        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'show']);
            Route::put('/', [UserController::class, 'update']);
            Route::put('/password', [UserController::class, 'updatePassword']);
            Route::post('/profile-picture', [UserController::class, 'uploadProfilePicture']);
            Route::delete('/profile-picture', [UserController::class, 'deleteProfilePicture']);
        });
        
        // Admin Routes
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            // User Management
            Route::apiResource('users', AdminUserController::class);
            
            // Role Management
            Route::apiResource('roles', RoleController::class);
        });
    });
}); 
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('loginwithgoogle', [AuthController::class, 'loginWithGoogle']); // Tương thích cũ
Route::post('google', [AuthController::class, 'loginWithGoogle']);          // RESTful mới

// Protected routes (yêu cầu Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::get('user', [AuthController::class, 'me']); // Tương thích cũ
    Route::post('logout', [AuthController::class, 'logout']);
});

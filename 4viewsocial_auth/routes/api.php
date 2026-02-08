<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('test', [App\Http\Controllers\AuthController::class, 'test']);
Route::post('loginwithgoogle', [App\Http\Controllers\AuthController::class, 'loginWithGoogle']);
Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



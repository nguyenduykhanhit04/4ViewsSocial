<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/hehe', [App\Http\Controllers\TestController::class, 'index']);
// Route::get('/list-post', [App\Http\Controllers\PostController::class, 'listPost']);
// Route::post('/add-post', [App\Http\Controllers\PostController::class, 'addPost']);
// Route::post('/delete-post', [App\Http\Controllers\PostController::class, 'deletePost']);
Route::get('auth/test', [App\Http\Controllers\AuthController::class, 'test']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

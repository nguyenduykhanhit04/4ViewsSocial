<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hehe', [App\Http\Controllers\TestController::class, 'index']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'stats']);
Route::get('/dashboard/chart', [App\Http\Controllers\DashboardController::class, 'chart']);

Route::get('/posts', [App\Http\Controllers\PostController::class, 'listPosts']);
Route::delete('/posts/{id}', [App\Http\Controllers\PostController::class, 'deletePost']);

Route::get('/reports', [App\Http\Controllers\ReportController::class, 'listReports']);


Route::get('/users', [App\Http\Controllers\UserController::class, 'listUsers']);
Route::get('/users/{id}', [App\Http\Controllers\UserController::class, 'getUserById']);

Route::get('/stories', [App\Http\Controllers\StoryController::class, 'listStories']);
Route::delete('/stories/{id}', [App\Http\Controllers\StoryController::class, 'deleteStory']);

Route::get('/comments', [App\Http\Controllers\CommentController::class, 'listComments']);
Route::delete('/comments/{id}', [App\Http\Controllers\CommentController::class, 'deleteComment']);


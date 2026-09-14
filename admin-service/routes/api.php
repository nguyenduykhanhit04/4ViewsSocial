<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Service API Routes
|--------------------------------------------------------------------------
*/

// --- 1. DASHBOARD & STATS ---
Route::get('dashboard', [DashboardController::class, 'stats']);
Route::get('dashboard/stats', [DashboardController::class, 'stats']);
Route::get('dashboard/chart', [DashboardController::class, 'chart']);

// --- 2. USER MANAGEMENT ---
Route::get('users', [UserController::class, 'listUsers']);
Route::get('users/{id}', [UserController::class, 'getUserById']);
Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus']);

// --- 3. POST MANAGEMENT ---
Route::get('posts', [PostController::class, 'listPosts']);
Route::delete('posts/{id}', [PostController::class, 'deletePost']);

// --- 4. STORY MANAGEMENT ---
Route::get('stories', [StoryController::class, 'listStories']);
Route::delete('stories/{id}', [StoryController::class, 'deleteStory']);

// --- 5. COMMENT MANAGEMENT ---
Route::get('comments', [CommentController::class, 'listComments']);
Route::delete('comments/{id}', [CommentController::class, 'deleteComment']);

// --- 6. REPORT MANAGEMENT ---
Route::get('reports', [ReportController::class, 'listReports']);

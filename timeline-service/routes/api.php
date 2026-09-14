<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Timeline / Post Service API Routes
|--------------------------------------------------------------------------
*/

// --- 1. POSTS & FEED ---
Route::get('list-post', [PostController::class, 'listPost']);
Route::post('add-post', [PostController::class, 'addPost']);
Route::post('delete-post', [PostController::class, 'deletePost']);
Route::post('like-post', [PostController::class, 'likePost']);
Route::post('save-post', [PostController::class, 'savePost']);
Route::get('my-post', [PostController::class, 'listPost']);
Route::get('explore-post', [PostController::class, 'explorePost']);
Route::get('explore', [PostController::class, 'explorePost']);

// --- 2. COMMENTS ---
Route::post('comment', [PostController::class, 'comment']);
Route::get('list-comment', [PostController::class, 'listComment']);

// --- 3. STORIES 24H ---
Route::get('list-story', [StoryController::class, 'listStory']);
Route::post('add-story', [StoryController::class, 'addStory']);
Route::post('delete-story', [StoryController::class, 'deleteStory']);
Route::post('like-story', [StoryController::class, 'likeStory']);

// --- 4. USERS & SOCIAL GRAPH ---
Route::get('suggest-friend', [UserController::class, 'suggestFriend']);
Route::post('follow', [UserController::class, 'follow']);
Route::get('get-info', [UserController::class, 'getInfo']);
Route::post('search-user', [UserController::class, 'searchUser']);

// --- 5. PROFILE ---
Route::get('get-profile', [ProfileController::class, 'getProfile']);
Route::get('get-post-user', [ProfileController::class, 'getPosts']);
Route::get('get-post-saved', [ProfileController::class, 'getPostSaved']);
Route::post('update-profile', [ProfileController::class, 'updateProfile']);
Route::post('change-password', [ProfileController::class, 'changePassword']);

// --- 6. NOTIFICATIONS & DEVICE TOKENS ---
Route::get('notifications', [NotificationController::class, 'getNotifications']);
Route::post('notifications/mark-read', [NotificationController::class, 'markAsRead']);
Route::post('set-device-token', [NotificationController::class, 'setDeviceToken']);

<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


Route::post('/register', [UserController::class, 'create']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/test-imagekit', [PostController::class, 'uploadTest']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [UserController::class, 'getUserByToken']);
    Route::post('/user/post', [PostController::class, 'makeUserPost']);
    Route::get('/user/following', [FollowerController::class, 'showFollowing']);
    Route::delete('/user/following', [FollowerController::class, 'UnfollowUser']);
    Route::post('/user/following', [FollowerController::class, 'followUser']);
    Route::get('/user/{username}/posts', [FeedController::class, 'userPosts']);
    Route::get('/feed', [FeedController::class, 'index']);
});

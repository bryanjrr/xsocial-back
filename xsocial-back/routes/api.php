<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/register', [UserController::class, 'create']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {});

<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\UserController;

use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'login']);
Route::post('register', RegisterController::class);

Route::group(['middleware' => 'api.auth'], function () {
    Route::get('user', [LoginController::class, 'details']);
    Route::get('logout', [LoginController::class, 'logout']);
    Route::apiResource('users', UserController::class);
});

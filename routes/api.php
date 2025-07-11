<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::delete('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/login', 'login');
});

Route::apiResource('/tasks', TaskController::class);

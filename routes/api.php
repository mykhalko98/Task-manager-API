<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// get current user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// auth
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// users
Route::get('/users', [\App\Http\Controllers\Api\UsersController::class, 'index'])->middleware('auth:sanctum');

// tasks
Route::get('/tasks/activity-log', [\App\Http\Controllers\Api\TasksController::class, 'activityLog'])->middleware('auth:sanctum');
Route::apiResource('tasks', \App\Http\Controllers\Api\TasksController::class)->middleware('auth:sanctum');

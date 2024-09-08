<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\ProjectController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

# PROJECT RESOURCE -> CONTROLLER
Route::apiResource('project', ProjectController::class);

# AUTHOR CONTROLLER
Route::apiResource('author', AuthorController::class);
// Route::post('author/write', [AuthorController::class, 'write']);

# USER CONTROLLER
Route::apiResource('user', UserController::class);
Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);


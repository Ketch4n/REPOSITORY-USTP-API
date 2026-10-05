<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\FirebaseController;
use App\Http\Controllers\Api\DatabaseBackupController;


# CURRENTLY AUTHENTICATED USER
# Registered as /api/me, not /api/user: apiResource('user') below also maps
# GET /api/user, and Laravel's route lookup is keyed by method + URI, so the
# later registration silently replaced this one.
Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

# PROJECT RESOURCE -> CONTROLLER
Route::apiResource('project', ProjectController::class);

# AUTHOR CONTROLLER
# Limited to the methods AuthorController actually implements. Without only(),
# show and update resolve to undefined methods and throw a 500.
Route::apiResource('author', AuthorController::class)->only(['index', 'store', 'destroy']);
// Route::post('author/write', [AuthorController::class, 'write']);

# USER CONTROLLER
# UserController has no index() or store(); registration and login are the
# explicit routes below.
Route::apiResource('user', UserController::class)->only(['show', 'update', 'destroy']);
Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);
Route::post('user/showStatus', [UserController::class, 'showStatus']);


# EMAIL CONTROLLER
Route::post('sendmail', [EmailController::class, 'sendmail']);
Route::post('sendmailTypeStatus', [EmailController::class, 'sendmailTypeStatus']);

# DATABASE BACKUP CONTROLLER
// Route::middleware('auth:api')->post('/backup-database', [DatabaseBackupController::class, 'backupDatabaseToFirebase']);
Route::post('/backup-database', [DatabaseBackupController::class, 'backupDatabaseToFirebase']);
// Route::post('/firebase-upload', [FirebaseController::class, 'uploadFile']);

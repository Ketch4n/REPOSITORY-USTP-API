<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\ViewedController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\FirebaseController;
use App\Http\Controllers\Api\LikeCommentController;
use App\Http\Controllers\Api\DatabaseBackupController;



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
Route::post('user/showStatus', [UserController::class, 'showStatus']);

# EMAIL CONTROLLER
Route::post('sendmail', [EmailController::class, 'sendmail']);
Route::post('sendmailTypeStatus', [EmailController::class, 'sendmailTypeStatus']);

# DATABASE BACKUP CONTROLLER
// Route::middleware('auth:api')->post('/backup-database', [DatabaseBackupController::class, 'backupDatabaseToFirebase']);
Route::post('/backup-database', [DatabaseBackupController::class, 'backupDatabaseToFirebase']);
// Route::post('/firebase-upload', [FirebaseController::class, 'uploadFile']);

# VIEWED CONTROLLER
Route::apiResource('viewed', ViewedController::class);
Route::post('downloads', [ViewedController::class, 'getDownloadsByProjectId']);
Route::post('countDownloads',[ViewedController::class,'countDownloads']);

# LIKE AND COMMENT CONTROLLER
Route::apiResource('likecomment', LikeCommentController::class);
Route::post('likecomment/rating',[LikeCommentController::class,'projectRatingComment']);
Route::post('likecomment/{id}/update',[LikeCommentController::class,'updateRatingComment']);



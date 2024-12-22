<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Notifications\SmsNotification;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\ViewedController;
use App\Http\Controllers\Api\VonageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SmsChefController;
use App\Http\Controllers\Api\FirebaseController;
use App\Http\Controllers\api\CollectionController;
use App\Http\Controllers\Api\LikeCommentController;
use App\Http\Controllers\Api\ResetPasswordController;
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

# COLLECTION CONTROLLER
Route::apiResource('collection', CollectionController::class);
Route::post('collection-null/{id}/update', [CollectionController::class, 'updateFileNULL']);


# EMAIL CONTROLLER
Route::post('sendmail', [EmailController::class, 'sendmail']);
Route::post('sendmailTypeStatus', [EmailController::class, 'sendmailTypeStatus']);

# DATABASE BACKUP CONTROLLER
// Route::middleware('auth:api')->post('/backup-database', [DatabaseBackupController::class, 'backupDatabaseToFirebase']);
Route::post('/backup-database', [DatabaseBackupController::class, 'backupDatabase']);
Route::get('/list-backups', [DatabaseBackupController::class, 'listBackups']);
Route::delete('/delete-backup/{fileName}', [DatabaseBackupController::class, 'deleteBackup']);
Route::post('/restore-backup/{fileName}', [DatabaseBackupController::class, 'restoreBackup']);

// Route::post('/firebase-upload', [FirebaseController::class, 'uploadFile']);

# VIEWED CONTROLLER
Route::apiResource('viewed', ViewedController::class);
Route::post('downloads', [ViewedController::class, 'getDownloadsByProjectId']);
Route::post('countDownloads',[ViewedController::class,'countDownloads']);

# LIKE AND COMMENT CONTROLLER
Route::apiResource('likecomment', LikeCommentController::class);
Route::post('likecomment/rating',[LikeCommentController::class,'projectRatingComment']);
Route::post('likecomment/{id}/update',[LikeCommentController::class,'updateRatingComment']);

# SMS TWILIO
Route::post('sendsms',[SmsController::class,'sendSms']);
# SMS VONAGE
Route::post('vonage',[VonageController::class,'sendSMS']);
Route::post('vonage-reset',[VonageController::class,'sendPasswordResetToken']);
# SMS CHEF
Route::post('send-sms', [SmsController::class, 'sendBulkSms']);



# PASSWORD RESET
Route::post('password-request', [ResetPasswordController::class, 'requestPasswordReset']);
Route::post('password-reset', [ResetPasswordController::class, 'resetPassword']);






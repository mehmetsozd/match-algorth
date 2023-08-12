<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\UploadPhotoController;
use App\Http\Controllers\MatchController;


use Illuminate\Support\Facades\Broadcast;


Broadcast::routes(['middleware' => ['auth:api']]);


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')
    ->as('auth.')
    ->group(function () {

        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login_with_token', [AuthController::class, 'loginWithToken'])
            ->middleware('auth:api')
            ->name('login_with_token');
        Route::get('logout', [AuthController::class, 'logout'])
            ->middleware('auth:api')
            ->name('logout');
        Route::delete('delete_account', [AuthController::class, 'deleteAccount'])
            ->middleware('auth:api')
            ->name('delete_account');
    });

Route::prefix('check')
    ->as('check.')
    ->group(function () {
        Route::post('version',[VersionController::class, 'versionCheck'])->name('versionCheck');
    });

Route::middleware('auth:api')->group(function (){


    //User Data
    Route::get('user', [UserController::class,'userData']);

    //User Photo
    Route::get('userPhoto',[UserPhotoController::class,'getPhoto']);
    Route::post('uploadPhoto',[UploadPhotoController::class , 'uploadPhoto']);

    //Votes
    Route::get('matelist',[MatchController::class,'getUserList']);
    Route::post('match',[MatchController::class,'matchUsers']);

});



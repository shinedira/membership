<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Membership\FcmController;
use App\Http\Controllers\Api\Membership\LoginController;
use App\Http\Controllers\Api\Membership\OtpController;
use App\Http\Controllers\Api\Membership\RegisterController;

Route::prefix('api')->group(function () {
    Route::post('register', [RegisterController::class, 'store']);
    Route::post('login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('fcm', [FcmController::class, 'store']);
        Route::get('user', function (Request $request) {
            return $request->user();
        });
    });

    Route::prefix('otp')->group(function () {
        Route::post('request', [OtpController::class, 'store'])->middleware('is-reached-max');
        Route::post('check', [OtpController::class, 'check']);
    });
});

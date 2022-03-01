<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Membership\FcmController;
use App\Http\Controllers\Api\V1\Membership\ForgotPasswordController;
use App\Http\Controllers\Api\Membership\LoginController;
use App\Http\Controllers\Api\Membership\OtpController;
use App\Http\Controllers\Api\Membership\RegisterController;
use App\Http\Controllers\Api\V1\Membership\ResetPasswordController;

Route::prefix('api')->group(function () {
    Route::post('register', [RegisterController::class, 'store']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('password-reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
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

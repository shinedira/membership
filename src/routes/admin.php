<?php

use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')
        ->as('admin.')
        ->middleware('web')
        ->group(function () {
   Route::get('dashboard', [DashboardController::class, 'index']);
   Route::resource('user', UserController::class)->only('index');
});
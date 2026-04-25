<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Middleware\TenantHandler;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');

    Route::get('authenticate', [AuthController::class, 'authenticate'])
        ->middleware([TenantHandler::class])
        ->name('authenticate');
});

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware(['auth'])->name('logout');

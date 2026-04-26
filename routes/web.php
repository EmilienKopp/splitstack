<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', ValidateSessionWithWorkOS::class, EnsureTeamMembership::class])
    ->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    });

Route::prefix('/registration')
    ->middleware(['web'])
    ->group(function () {
        Route::get('/on-the-fly', [RegistrationController::class, 'showOnTheFlyForm'])->name('register-on-the-fly');
        Route::post('/on-the-fly', [RegistrationController::class, 'registerOnTheFly'])->name('register-on-the-fly.submit');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

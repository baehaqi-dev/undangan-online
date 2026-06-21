<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\RsvpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Public RSVP routes (akses pakai token, bukan login)
    Route::get('/rsvp/{token}', [RsvpController::class, 'showByToken']);
    Route::post('/rsvp/{token}', [RsvpController::class, 'storeByToken'])
        ->middleware('throttle:5,1');

    // Protected routes (butuh token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });

        Route::apiResource('invitations', InvitationController::class);
        Route::get('/invitations/{invitation}/statistics', [InvitationController::class, 'statistics']);
        Route::get('/invitations/{invitation}/export-guests', [InvitationController::class, 'exportGuests']);
        Route::apiResource('invitations.guests', GuestController::class)->shallow();
        Route::get('/invitations/{invitation}/rsvps', [RsvpController::class, 'index']);
    });

});
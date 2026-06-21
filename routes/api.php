<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (butuh token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });

        Route::apiResource('invitations', InvitationController::class);
        Route::apiResource('invitations.guests', GuestController::class)->shallow();
    });

});
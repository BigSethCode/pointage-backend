<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\RapportController;

Route::post('/organisations', [OrganisationController::class, 'register']);

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/collaborateurs', [CollaborateurController::class, 'index']);
    Route::post('/collaborateurs', [CollaborateurController::class, 'store']);
    Route::patch('/collaborateurs/{id}', [CollaborateurController::class, 'update']);

    Route::get('/rapport/aujourdhui', [RapportController::class, 'aujourdhui']);
});

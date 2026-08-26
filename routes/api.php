<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\PointageController;

Route::post('/organisations', [OrganisationController::class, 'register']);

Route::get('/pointage/{slug}', [PointageController::class, 'show']);
Route::post('/pointage/{slug}', [PointageController::class, 'requestOtp']);
Route::post('/pointage/{slug}/verify', [PointageController::class, 'verifyOtp']);
Route::post('/pointage/{slug}/rapport', [PointageController::class, 'storeRapport']);

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/collaborateurs', [CollaborateurController::class, 'index']);
    Route::post('/collaborateurs', [CollaborateurController::class, 'store']);
    Route::patch('/collaborateurs/{id}', [CollaborateurController::class, 'update']);

    Route::get('/rapport/aujourdhui', [RapportController::class, 'aujourdhui']);
});

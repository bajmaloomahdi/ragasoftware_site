<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel (Inertia + React + Ant Design)
|--------------------------------------------------------------------------
| Registered from bootstrap/app.php with:
|   prefix     "admin"
|   name       "admin."
|   middleware "admin"  (= web + NoIndex + HandleInertiaRequests)
| Every data route additionally requires auth; fine-grained gates use the
| "can:" middleware backed by spatie permissions.
*/

// --- Guest ---
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// --- Authenticated ---
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('can:dashboard.view')
        ->name('dashboard');

    // CMS resource routes are added in Phase 5.
});

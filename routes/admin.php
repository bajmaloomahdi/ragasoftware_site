<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel (Inertia + React + Ant Design)
|--------------------------------------------------------------------------
| Registered with prefix "admin" and name prefix "admin." from bootstrap/app.php,
| behind the "admin" middleware group. Every route additionally requires auth;
| fine-grained gates use the "permission:" middleware (spatie).
*/

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CMS resource routes are added in Phase 5.
});

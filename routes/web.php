<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website (server-rendered Blade)
|--------------------------------------------------------------------------
| Real routes are wired up in Phase 6. The dynamic `/{page:slug}` catch-all
| must always be registered LAST so it does not shadow the fixed routes.
| Admin + auth routes live in routes/admin.php (see bootstrap/app.php).
*/

Route::get('/', fn () => view('welcome'))->name('home');

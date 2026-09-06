<?php

use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dynamic CMS pages — MUST be the very last route registered
|--------------------------------------------------------------------------
| Loaded from bootstrap/app.php after web.php AND admin.php so it can never
| shadow a fixed route (e.g. /admin, /blog, /contact).
*/

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-\_\x{0600}-\x{06FF}]+')
    ->name('page');

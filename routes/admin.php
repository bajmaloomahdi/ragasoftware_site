<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel — prefix "admin", name "admin.", middleware "admin"
| (see bootstrap/app.php). Every data route also requires auth + a gate.
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware('can:dashboard.view')->name('dashboard');

    /* --- Site settings --- */
    Route::middleware('can:settings.manage')->group(function () {
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('social-links', [SocialLinkController::class, 'index'])->name('social-links.index');
        Route::post('social-links', [SocialLinkController::class, 'store'])->name('social-links.store');
        Route::put('social-links/{id}', [SocialLinkController::class, 'update'])->name('social-links.update');
        Route::delete('social-links/{id}', [SocialLinkController::class, 'destroy'])->name('social-links.destroy');
        Route::post('social-links/reorder', [SocialLinkController::class, 'reorder'])->name('social-links.reorder');
    });

    /* --- Homepage --- */
    Route::get('homepage', [HomepageController::class, 'edit'])->middleware('can:pages.manage')->name('homepage.edit');

    /* --- Pages --- */
    Route::middleware('can:pages.view')->group(function () {
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::middleware('can:pages.manage')->group(function () {
            Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
            Route::post('pages', [PageController::class, 'store'])->name('pages.store');
            Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
            Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
        });
        Route::delete('pages/{page}', [PageController::class, 'destroy'])->middleware('can:pages.delete')->name('pages.destroy');
    });

    /* --- Products --- */
    Route::middleware('can:products.view')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::middleware('can:products.manage')->group(function () {
            Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('products', [ProductController::class, 'store'])->name('products.store');
            Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        });
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('can:products.delete')->name('products.destroy');

        Route::get('product-categories', [ProductCategoryController::class, 'index'])->name('product-categories.index');
        Route::middleware('can:products.manage')->group(function () {
            Route::post('product-categories', [ProductCategoryController::class, 'store'])->name('product-categories.store');
            Route::put('product-categories/{id}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
            Route::delete('product-categories/{id}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');
        });
    });

    /* --- Services --- */
    Route::middleware('can:services.view')->group(function () {
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::middleware('can:services.manage')->group(function () {
            Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
            Route::post('services', [ServiceController::class, 'store'])->name('services.store');
            Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
            Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        });
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->middleware('can:services.delete')->name('services.destroy');
    });

    /* --- Projects --- */
    Route::middleware('can:projects.view')->group(function () {
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::middleware('can:projects.manage')->group(function () {
            Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
            Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        });
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->middleware('can:projects.delete')->name('projects.destroy');
    });

    /* --- Customers, testimonials, team --- */
    Route::middleware('can:customers.manage')->group(function () {
        foreach (['customers' => CustomerController::class, 'testimonials' => TestimonialController::class] as $key => $ctrl) {
            Route::get($key, [$ctrl, 'index'])->name("{$key}.index");
            Route::post($key, [$ctrl, 'store'])->name("{$key}.store");
            Route::put("{$key}/{id}", [$ctrl, 'update'])->name("{$key}.update");
            Route::delete("{$key}/{id}", [$ctrl, 'destroy'])->name("{$key}.destroy");
            Route::post("{$key}/{id}/toggle", [$ctrl, 'toggle'])->name("{$key}.toggle");
            Route::post("{$key}/reorder", [$ctrl, 'reorder'])->name("{$key}.reorder");
        }
    });
    Route::middleware('can:team.manage')->group(function () {
        Route::get('team', [TeamMemberController::class, 'index'])->name('team.index');
        Route::post('team', [TeamMemberController::class, 'store'])->name('team.store');
        Route::put('team/{id}', [TeamMemberController::class, 'update'])->name('team.update');
        Route::delete('team/{id}', [TeamMemberController::class, 'destroy'])->name('team.destroy');
        Route::post('team/{id}/toggle', [TeamMemberController::class, 'toggle'])->name('team.toggle');
        Route::post('team/reorder', [TeamMemberController::class, 'reorder'])->name('team.reorder');
    });

    /* --- Blog --- */
    Route::middleware('can:blog.view')->group(function () {
        Route::get('posts', [BlogPostController::class, 'index'])->name('posts.index');
        Route::middleware('can:blog.manage')->group(function () {
            Route::get('posts/create', [BlogPostController::class, 'create'])->name('posts.create');
            Route::post('posts', [BlogPostController::class, 'store'])->name('posts.store');
            Route::get('posts/{post}/edit', [BlogPostController::class, 'edit'])->name('posts.edit');
            Route::put('posts/{post}', [BlogPostController::class, 'update'])->name('posts.update');

            foreach (['blog-categories' => BlogCategoryController::class, 'blog-tags' => BlogTagController::class] as $key => $ctrl) {
                Route::get($key, [$ctrl, 'index'])->name("{$key}.index");
                Route::post($key, [$ctrl, 'store'])->name("{$key}.store");
                Route::put("{$key}/{id}", [$ctrl, 'update'])->name("{$key}.update");
                Route::delete("{$key}/{id}", [$ctrl, 'destroy'])->name("{$key}.destroy");
            }
        });
        Route::delete('posts/{post}', [BlogPostController::class, 'destroy'])->middleware('can:blog.delete')->name('posts.destroy');
    });

    /* --- FAQ --- */
    Route::middleware('can:faq.manage')->group(function () {
        foreach (['faqs' => FaqController::class, 'faq-categories' => FaqCategoryController::class] as $key => $ctrl) {
            Route::get($key, [$ctrl, 'index'])->name("{$key}.index");
            Route::post($key, [$ctrl, 'store'])->name("{$key}.store");
            Route::put("{$key}/{id}", [$ctrl, 'update'])->name("{$key}.update");
            Route::delete("{$key}/{id}", [$ctrl, 'destroy'])->name("{$key}.destroy");
            Route::post("{$key}/{id}/toggle", [$ctrl, 'toggle'])->name("{$key}.toggle");
            Route::post("{$key}/reorder", [$ctrl, 'reorder'])->name("{$key}.reorder");
        }
    });

    /* --- Menus --- */
    Route::middleware('can:menus.manage')->group(function () {
        Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [MenuController::class, 'storeMenu'])->name('menus.store');
        Route::put('menus/{menu}/sync', [MenuController::class, 'sync'])->name('menus.sync');
        Route::delete('menus/{menu}', [MenuController::class, 'destroyMenu'])->name('menus.destroy');
    });

    /* --- Media --- */
    Route::middleware('can:media.view')->group(function () {
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->middleware('can:media.upload')->name('media.store');
        Route::put('media/{medium}', [MediaController::class, 'update'])->middleware('can:media.upload')->name('media.update');
        Route::delete('media/{medium}', [MediaController::class, 'destroy'])->middleware('can:media.delete')->name('media.destroy');
        Route::post('media-folders', [MediaController::class, 'storeFolder'])->middleware('can:media.upload')->name('media-folders.store');
        Route::delete('media-folders/{folder}', [MediaController::class, 'destroyFolder'])->middleware('can:media.delete')->name('media-folders.destroy');
    });

    /* --- SEO --- */
    Route::middleware('can:seo.manage')->group(function () {
        Route::get('seo', [SeoController::class, 'edit'])->name('seo.edit');
        Route::post('seo/redirects', [SeoController::class, 'storeRedirect'])->name('seo.redirects.store');
        Route::put('seo/redirects/{redirect}', [SeoController::class, 'updateRedirect'])->name('seo.redirects.update');
        Route::delete('seo/redirects/{redirect}', [SeoController::class, 'destroyRedirect'])->name('seo.redirects.destroy');
        Route::post('seo/sitemap/rebuild', [SeoController::class, 'rebuildSitemap'])->name('seo.sitemap.rebuild');
    });

    /* --- Contact messages --- */
    Route::middleware('can:messages.view')->group(function () {
        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::middleware('can:messages.manage')->group(function () {
            Route::put('messages/{message}', [ContactMessageController::class, 'update'])->name('messages.update');
            Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
        });
    });

    /* --- Users & roles --- */
    Route::middleware('can:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('roles', [UserController::class, 'roles'])->name('roles.index');
        Route::middleware('can:users.manage')->group(function () {
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::post('roles', [UserController::class, 'storeRole'])->name('roles.store');
            Route::put('roles/{role}', [UserController::class, 'updateRole'])->name('roles.update');
            Route::delete('roles/{role}', [UserController::class, 'destroyRole'])->name('roles.destroy');
        });
    });
});

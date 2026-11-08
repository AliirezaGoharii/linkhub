<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinkClickController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

// Fire-and-forget click tracking from the public page. Public, but
// rate limited and side-effect-limited to a counter increment.
Route::post('/links/{link}/click', LinkClickController::class)
    ->middleware('throttle:120,1')
    ->name('links.click');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/links', [LinkController::class, 'store'])->name('links.store');

    // Static path must be registered before the /links/{link} wildcard,
    // otherwise "order" is treated as a link id and the request 404s.
    Route::put('/links/order', [LinkController::class, 'updateOrder'])->name('links.order.update');

    Route::put('/links/{link}', [LinkController::class, 'update'])->name('links.update');
    Route::patch('/links/{link}/toggle', [LinkController::class, 'toggle'])->name('links.toggle');
    Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');

    Route::put('/page-settings', [PageSettingsController::class, 'update'])->name('page-settings.update');
    Route::post('/page-settings/photo', [PageSettingsController::class, 'updatePhoto'])->name('page-settings.photo.update');
    Route::delete('/page-settings/photo', [PageSettingsController::class, 'deletePhoto'])->name('page-settings.photo.delete');
    Route::post('/page-settings/banner', [PageSettingsController::class, 'updateBanner'])->name('page-settings.banner.update');
    Route::delete('/page-settings/banner', [PageSettingsController::class, 'deleteBanner'])->name('page-settings.banner.delete');
});

require __DIR__.'/auth.php';

Route::get('/{user:username}', PublicPageController::class)
    ->where('username', '[a-z0-9_-]+')
    ->name('public.show');

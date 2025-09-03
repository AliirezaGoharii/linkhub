<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageSettingsController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () { return Inertia::render('Welcome', ['canLogin' => Route::has('login'), 'canRegister' => Route::has('register')]); });
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Dashboard'); })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
    Route::put('/links/{link}', [LinkController::class, 'update'])->name('links.update');
    Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
    Route::put('/links/order', [LinkController::class, 'updateOrder'])->name('links.order.update');
    Route::put('/page-settings', [PageSettingsController::class, 'update'])->name('page-settings.update');
    Route::post('/page-settings/photo', [PageSettingsController::class, 'updatePhoto'])->name('page-settings.photo.update');
    Route::delete('/page-settings/photo', [PageSettingsController::class, 'deletePhoto'])->name('page-settings.photo.delete');
    Route::post('/page-settings/banner', [PageSettingsController::class, 'updateBanner'])->name('page-settings.banner.update');
    Route::delete('/page-settings/banner', [PageSettingsController::class, 'deleteBanner'])->name('page-settings.banner.delete');
});

require __DIR__.'/auth.php';
Route::get('/{user:username}', PublicPageController::class)->name('public.show');

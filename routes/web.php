<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ReleaseController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MediaItemController;
use App\Http\Controllers\Admin\ArtistProfileController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PressKitController;
use App\Http\Controllers\Admin\PressAssetController;
use App\Http\Controllers\Admin\PressKitController as AdminPressKitController;
use App\Http\Controllers\Admin\BookingEnquiryController;

Route::get('/', HomeController::class)->name('home');

Route::get('/music', [MusicController::class, 'index'])
    ->name('music.index');

Route::get('/music/{slug}', [MusicController::class, 'show'])
    ->name('music.show');

    Route::post('/booking', [BookingController::class, 'store'])
    ->name('booking.store')->middleware('throttle:5,10,booking');

// Private admin area. There is intentionally no registration route.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('releases', ReleaseController::class)->except('show');
    });

    Route::resource('media', MediaItemController::class)
    ->parameters(['media' => 'mediaItem'])
    ->except('show');

    Route::get('profile', [ArtistProfileController::class, 'edit'])
    ->name('profile.edit');

Route::put('profile', [ArtistProfileController::class, 'update'])
    ->name('profile.update');

    Route::resource('photos', PhotoController::class)->except('show');

    Route::get('press', [AdminPressKitController::class, 'edit'])->name('press.edit');
Route::put('press', [AdminPressKitController::class, 'update'])->name('press.update');
Route::post('press/assets', [PressAssetController::class, 'store'])->name('press.assets.store');
Route::put('press/assets/{asset}', [PressAssetController::class, 'update'])->name('press.assets.update');
Route::delete('press/assets/{asset}', [PressAssetController::class, 'destroy'])->name('press.assets.destroy');
Route::get('enquiries', [BookingEnquiryController::class, 'index'])
    ->name('enquiries.index');

Route::get('enquiries/{enquiry}', [BookingEnquiryController::class, 'show'])
    ->name('enquiries.show');

Route::patch('enquiries/{enquiry}', [BookingEnquiryController::class, 'update'])
    ->name('enquiries.update');
});

Route::get('/press', PressKitController::class)->name('press');
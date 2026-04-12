<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BroadcastMessageController;
use App\Http\Controllers\OfficeMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PointController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Main application routes for HouseHub
|
*/

// =============================
// ROOT REDIRECT
// =============================
Route::get('/', function () {
    return redirect('/points');
});

// =============================
// AUTHENTICATED ROUTES
// =============================
Route::middleware(['auth'])->group(function () {

    // =============================
    // CORE SYSTEM
    // =============================
    Route::get('/points', [PointController::class, 'index'])->name('points.index');
    Route::post('/points', [PointController::class, 'store'])->name('points.store');

    // =============================
    // OFFICE MESSAGE MODE (OMM)
    // =============================
    Route::post('/office-messages', [OfficeMessageController::class, 'store'])->name('office-messages.store');
    Route::patch('/office-messages/{id}', [OfficeMessageController::class, 'update'])->name('office-messages.update');

    // =============================
    // BROADCAST MESSAGE MODE
    // =============================
    Route::post('/broadcast-messages', [BroadcastMessageController::class, 'store'])->name('broadcast-messages.store');
    Route::get('/broadcast-messages/latest', [BroadcastMessageController::class, 'latest'])->name('broadcast-messages.latest');

    // =============================
    // STUDENT PROFILE
    // =============================
    Route::get('/students/{id}', [PointController::class, 'showStudent'])->name('students.show');

    // =============================
    // CERTIFICATES
    // =============================
    Route::get('/certificate/{id}', [PointController::class, 'certificate'])->name('certificate.show');

    // =============================
    // TV MODE (DISPLAY SCREENS)
    // =============================
    Route::get('/tv', [PointController::class, 'tv'])->name('tv');

    // =============================
    // DASHBOARD (OPTIONAL / FUTURE)
    // =============================
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // =============================
    // USER PROFILE (LARAVEL BREEZE)
    // =============================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =============================
    // ADMIN CONTROL PANEL
    // =============================
    Route::get('/admin', function () {
        return view('admin.index');
    })->name('admin.index');

});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
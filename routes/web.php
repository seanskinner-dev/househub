<?php

use Illuminate\Support\Facades\Route;
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

});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
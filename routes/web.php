<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PointController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Default landing (optional – can change later)
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // 🔹 Points system (CORE APP)
    Route::get('/points', [PointController::class, 'index'])->name('points.index');
    Route::post('/points', [PointController::class, 'store'])->name('points.store');

    // 🔹 Dashboard (keep for now)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 🔹 Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Auth Routes (login, register, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
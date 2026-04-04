<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home (temporary)
Route::get('/', function () {
    return redirect()->route('points.index');
});

// Points system
Route::get('/points', [PointController::class, 'index'])->name('points.index');
Route::post('/points', [PointController::class, 'store'])->name('points.store');
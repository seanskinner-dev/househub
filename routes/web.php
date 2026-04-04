<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointController;
use App\Http\Controllers\StudentController;

Route::get('/points', [PointController::class, 'index'])->name('points.index');
Route::post('/points', [PointController::class, 'store'])->name('points.store');

Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
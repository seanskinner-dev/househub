<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BroadcastMessageController;
use App\Http\Controllers\OfficeMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =============================
// ROOT REDIRECT
// =============================
Route::get('/', function () {
    return redirect('/points');
});

// =============================
// PUBLIC (TV + BROADCAST)
// =============================
Route::get('/broadcast-messages/latest', [BroadcastMessageController::class, 'latest'])
    ->name('broadcast-messages.latest');

Route::get('/test123', function () {
    return 'TEST123';
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

    // =============================
    // STUDENT PROFILE
    // =============================
    Route::get('/students/{id}', [PointController::class, 'showStudent'])->name('students.show');

    // =============================
    // CERTIFICATES
    // =============================
    Route::get('/certificate/{id}', [PointController::class, 'certificate'])->name('certificate.show');

    // =============================
    // TV MODE
    // =============================
    Route::get('/tv', [PointController::class, 'tv'])->name('tv');

    // =============================
    // REPORTS
    // =============================
    Route::get('/reports/house-performance', [App\Http\Controllers\ReportController::class, 'housePerformance'])
        ->name('reports.house');

    Route::get('/reports/pc', [App\Http\Controllers\ReportController::class, 'atRiskStudents'])
        ->name('reports.pc');

    Route::get('/reports/leadership', [ReportController::class, 'leadership'])
        ->name('reports.leadership');

    Route::get('/reports/data', [App\Http\Controllers\ReportController::class, 'reportChartData'])
        ->name('reports.data');

    Route::get('/reports/drilldown', [App\Http\Controllers\ReportController::class, 'reportDrilldown'])
        ->name('reports.drilldown');

    // =============================
    // DASHBOARD
    // =============================
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // =============================
    // USER PROFILE
    // =============================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =============================
    // ADMIN
    // =============================
    Route::get('/admin', function () {
        return view('admin.index');
    })->name('admin.index');

});

// =============================
// AUTH ROUTES
// =============================
require __DIR__.'/auth.php';
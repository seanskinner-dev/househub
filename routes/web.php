<?php
// TEMP DEMO MODE - authentication disabled
// MUST be re-enabled after demo/testing

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BroadcastMessageController;
use App\Http\Controllers\OfficeMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;

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
Route::get('/broadcast', [BroadcastMessageController::class, 'latest'])
    ->name('broadcast.latest');

// =============================
// PUBLIC — Points (no login; guest actions attributed to demo teachers)
// =============================
Route::get('/points', [PointController::class, 'index'])->name('points.index');
Route::get('/points/recent', [PointController::class, 'recent'])->name('points.recent');
Route::post('/points', [PointController::class, 'store'])->name('points.store');
Route::post('/points/commendation', [PointController::class, 'storeCommendation'])->name('points.commendation');
Route::post('/points/award', [PointController::class, 'storeAward'])->name('points.award');

// =============================
// CORE SYSTEM
// =============================
// =============================
// OFFICE MESSAGE MODE (OMM)
// =============================
Route::post('/office-messages', [OfficeMessageController::class, 'store'])->name('office-messages.store');
Route::patch('/office-messages/{id}', [OfficeMessageController::class, 'update'])->name('office-messages.update');

// =============================
// OFFICE MESSAGE MODE
// =============================
Route::post('/broadcast-messages', [BroadcastMessageController::class, 'store'])->name('broadcast-messages.store');
Route::post('/broadcast/clear', [BroadcastMessageController::class, 'clear'])->name('broadcast.clear');
Route::post('/omm/clear', [BroadcastMessageController::class, 'clear'])->name('omm.clear');
Route::post('/emergency-mode', [BroadcastMessageController::class, 'storeEmergency'])->name('emergency.store');
Route::post('/emergency-mode/clear', [BroadcastMessageController::class, 'clearEmergency'])->name('emergency.clear');

// =============================
// STUDENT PROFILE (AUTH ONLY)
// =============================
Route::middleware(['auth'])->group(function () {
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
});

// =============================
// CERTIFICATES
// =============================
Route::get('/certificate/{id}', [PointController::class, 'certificate'])->name('certificate.show');

// =============================
// TV MODE
// =============================
Route::get('/tv', [PointController::class, 'tv'])->name('tv');

// =============================
// PRIVACY
// =============================
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
});

Route::get('/security', function () {
    return view('security');
});

Route::get('/contact', function () {
    return view('contact');
});

// =============================
// REPORTS
// =============================
Route::get('/reports/house-performance', [App\Http\Controllers\ReportController::class, 'housePerformance'])
    ->name('reports.house');

Route::get('/reports/pc', [App\Http\Controllers\ReportController::class, 'atRiskStudents'])
    ->name('reports.pc');

Route::get('/reports/leadership', [ReportController::class, 'leadership'])
    ->name('reports.leadership');

Route::get('/reports/teachers', [ReportController::class, 'teachers'])
    ->name('reports.teachers');

Route::get('/reports/houses', [ReportController::class, 'houses'])
    ->name('reports.houses');

Route::get('/reports/data', [App\Http\Controllers\ReportController::class, 'reportChartData'])
    ->name('reports.data');

Route::match(['get', 'post'], '/reports/drilldown', [App\Http\Controllers\ReportController::class, 'reportDrilldown'])
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
    $houses = DB::table('houses')
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

    return view('admin.index', ['houses' => $houses]);
})->name('admin.index');

// =============================
// AUTH ROUTES
// =============================
require __DIR__.'/auth.php';
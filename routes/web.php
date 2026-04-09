<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PointController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root → redirect to points
Route::get('/', function () {
    return redirect('/points');
});

/*
|--------------------------------------------------------------------------
| PUBLIC TV ROUTES (NO AUTH) 🔥
|--------------------------------------------------------------------------
*/

// 🔹 MAIN TV DASHBOARD
Route::get('/tv', function () {

    $houses = DB::table('houses')
        ->select('id', 'name', 'colour_hex', 'points')
        ->orderByDesc('points')
        ->get();

    $recent = DB::table('point_transactions')
        ->leftJoin('students', 'point_transactions.student_id', '=', 'students.id')
        ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
        ->select(
            'students.first_name',
            'students.last_name',
            'houses.name as house_name',
            'point_transactions.amount',
            'point_transactions.description',
            'point_transactions.created_at'
        )
        ->orderByDesc('point_transactions.created_at')
        ->limit(10)
        ->get();

    $topTeacher = DB::table('point_transactions')
        ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
        ->select('users.name', DB::raw('SUM(point_transactions.amount) as total_points'))
        ->groupBy('users.name')
        ->orderByDesc('total_points')
        ->first();

    return view('tv', compact('houses', 'recent', 'topTeacher'));

})->name('tv');

// 🔥 TV SCREENS
Route::get('/tv/house-trends', [PointController::class, 'houseTrends'])->name('tv.house.trends');
Route::get('/tv/house-month', [PointController::class, 'housePointsMonth'])->name('tv.house.month');
Route::get('/tv/house-year', [PointController::class, 'housePointsYear'])->name('tv.house.year');
Route::get('/tv/teachers', [PointController::class, 'teacherHighlights'])->name('tv.teachers');
Route::get('/tv/teachers-month', [PointController::class, 'teacherHighlightsMonth'])->name('tv.teachers.month');
Route::get('/tv/top-students', [PointController::class, 'topStudents'])->name('tv.top.students');

/* 🔥 NEW: WEATHER SCREEN */
Route::get('/tv/weather', function () {
    return view('tv.weather');
})->name('tv.weather');

// 🆕 🔥 HOUSE RACE (PREMIUM)
Route::get('/tv/house-race', function () {

    $houses = DB::table('houses')
        ->select('name', 'points', 'colour_hex')
        ->orderByDesc('points')
        ->get();

    return view('tv.house_race', compact('houses'));

})->name('tv.house.race');

// 🆕 🔥 HOUSE MOMENTUM (NEW)
Route::get('/tv/house-momentum', [PointController::class, 'houseMomentum'])
    ->name('tv.house.momentum');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // 🔹 Points system
    Route::get('/points', [PointController::class, 'index'])->name('points.index');
    Route::post('/points', [PointController::class, 'store'])->name('points.store');

    // 🔹 Student profile
    Route::get('/students/{id}', [PointController::class, 'showStudent'])->name('students.show');

    // 🔹 Dashboard
    Route::get('/dashboard', [PointController::class, 'dashboard'])->name('dashboard');

    // 🔹 House Cup (optional)
    Route::get('/house-cup', function () {

        $houseTotals = DB::table('houses')
            ->select('name', 'points', 'colour_hex')
            ->orderByDesc('points')
            ->get();

        return view('house-cup', compact('houseTotals'));

    })->name('house.cup');

    // 🔹 Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
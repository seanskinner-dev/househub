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
| PUBLIC TV ROUTES (NO AUTH)
|--------------------------------------------------------------------------
*/

// 🎯 MAIN TV ROTATOR
Route::get('/tv', function () {
    return view('tv.tv');
})->name('tv');

// 🔥 HOUSE TOTAL HERO (NEW)
Route::get('/tv/house-total', function () {

    $houses = DB::table('houses')
        ->select('name', 'points', 'colour_hex')
        ->orderByDesc('points')
        ->get();

    return view('tv.house_total', compact('houses'));

})->name('tv.house.total');

// 🔥 LEADERBOARD
Route::get('/tv/leaderboard', function () {

    $houses = DB::table('houses')
        ->select('id', 'name', 'colour_hex', 'points')
        ->orderByDesc('points')
        ->get();

    $students = DB::table('students')
        ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
        ->select(
            'students.id',
            'students.first_name',
            'students.last_name',
            'students.year_level',
            'houses.name as house_name',
            'houses.colour_hex',
            DB::raw('COALESCE(students.house_points, 0) as points')
        )
        ->orderByDesc('points')
        ->limit(30)
        ->get();

    return view('tv.leaderboard', [
        'houses' => $houses,
        'students' => $students
    ]);

})->name('tv.leaderboard');

// 🔥 WEATHER
Route::get('/tv/weather', function () {
    return view('tv.weather');
})->name('tv.weather');

// 🔥 TOP STUDENTS
Route::get('/tv/top-students', [PointController::class, 'topStudents'])
    ->name('tv.top.students');

// 🔥 HOUSE RACE
Route::get('/tv/house-race', function () {

    $houses = DB::table('houses')
        ->select('name', 'points', 'colour_hex')
        ->orderByDesc('points')
        ->get();

    return view('tv.house_race', compact('houses'));

})->name('tv.house.race');

// 🔥 HOUSE TRENDS
Route::get('/tv/house-trends', [PointController::class, 'houseTrends'])
    ->name('tv.house.trends');

// 🔥 TEACHERS
Route::get('/tv/teachers', [PointController::class, 'teacherHighlights'])
    ->name('tv.teachers');

// 🔥 OPTIONAL EXTRA SCREENS
Route::get('/tv/house-month', [PointController::class, 'housePointsMonth'])->name('tv.house.month');
Route::get('/tv/house-year', [PointController::class, 'housePointsYear'])->name('tv.house.year');
Route::get('/tv/teachers-month', [PointController::class, 'teacherHighlightsMonth'])->name('tv.teachers.month');
Route::get('/tv/house-momentum', [PointController::class, 'houseMomentum'])->name('tv.house.momentum');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/points', [PointController::class, 'index'])->name('points.index');
    Route::post('/points', [PointController::class, 'store'])->name('points.store');

    Route::get('/students/{id}', [PointController::class, 'showStudent'])->name('students.show');

    Route::get('/dashboard', [PointController::class, 'dashboard'])->name('dashboard');

    Route::get('/house-cup', function () {

        $houseTotals = DB::table('houses')
            ->select('name', 'points', 'colour_hex')
            ->orderByDesc('points')
            ->get();

        return view('house-cup', compact('houseTotals'));

    })->name('house.cup');

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
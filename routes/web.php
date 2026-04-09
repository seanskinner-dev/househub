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
| PUBLIC TV ROUTES (PRODUCT)
|--------------------------------------------------------------------------
*/

// 🎯 TV ROTATOR
Route::get('/tv', function () {
    return view('tv.tv');
})->name('tv');

// 🏁 HOUSE RACE (PRIMARY)
Route::get('/tv/house-race-live', function () {

    $houses = DB::table('houses')
        ->select('name', 'points', 'colour_hex')
        ->orderByDesc('points')
        ->get();

    return view('tv.house_race_live', compact('houses'));

})->name('tv.house.race.live');

// ⚡ LIVE ACTIVITY
Route::get('/tv/live-activity', function () {

    $activities = DB::table('point_transactions')
        ->leftJoin('students', 'point_transactions.student_id', '=', 'students.id')
        ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
        ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
        ->select(
            'students.first_name',
            'students.last_name',
            'houses.name as house_name',
            'houses.colour_hex',
            'point_transactions.amount',
            'users.name as teacher',
            'point_transactions.created_at'
        )
        ->orderByDesc('point_transactions.created_at')
        ->limit(8)
        ->get();

    return view('tv.live_activity', compact('activities'));

})->name('tv.live.activity');

// 🏆 DAILY LEADER
Route::get('/tv/daily-winner', function () {

    $winner = DB::table('point_transactions')
        ->join('students', 'point_transactions.student_id', '=', 'students.id')
        ->join('houses', 'students.house_id', '=', 'houses.id')
        ->select(
            'students.first_name',
            'students.last_name',
            'houses.name as house_name',
            'houses.colour_hex',
            DB::raw('SUM(point_transactions.amount) as total')
        )
        ->whereDate('point_transactions.created_at', today())
        ->groupBy(
            'students.id',
            'students.first_name',
            'students.last_name',
            'houses.name',
            'houses.colour_hex'
        )
        ->orderByDesc('total')
        ->first();

    return view('tv.daily_winner', compact('winner'));

})->name('tv.daily.winner');

// 🔥 HOT STREAK
Route::get('/tv/hot-streak', function () {

    $streak = DB::table('point_transactions')
        ->join('students', 'point_transactions.student_id', '=', 'students.id')
        ->join('houses', 'students.house_id', '=', 'houses.id')
        ->select(
            'students.first_name',
            'students.last_name',
            'houses.name as house_name',
            'houses.colour_hex',
            DB::raw('SUM(point_transactions.amount) as total')
        )
        ->where('point_transactions.created_at', '>=', now()->subMinutes(60))
        ->groupBy(
            'students.id',
            'students.first_name',
            'students.last_name',
            'houses.name',
            'houses.colour_hex'
        )
        ->orderByDesc('total')
        ->first();

    return view('tv.hot_streak', compact('streak'));

})->name('tv.hot.streak');

// 👑 WEEKLY LEADER
Route::get('/tv/weekly-winner', function () {

    $winner = DB::table('point_transactions')
        ->join('students', 'point_transactions.student_id', '=', 'students.id')
        ->join('houses', 'students.house_id', '=', 'houses.id')
        ->select(
            'students.first_name',
            'students.last_name',
            'houses.name as house_name',
            'houses.colour_hex',
            DB::raw('SUM(point_transactions.amount) as total')
        )
        ->whereBetween('point_transactions.created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])
        ->groupBy(
            'students.id',
            'students.first_name',
            'students.last_name',
            'houses.name',
            'houses.colour_hex'
        )
        ->orderByDesc('total')
        ->first();

    return view('tv.weekly_winner', compact('winner'));

})->name('tv.weekly.winner');

// 👨‍🏫 TEACHER TOP 3
Route::get('/tv/teachers-top', function () {

    $teachers = DB::table('point_transactions')
        ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
        ->select(
            'users.name',
            DB::raw('COUNT(point_transactions.id) as actions'),
            DB::raw('SUM(point_transactions.amount) as total_points')
        )
        ->whereNotNull('point_transactions.awarded_by')
        ->groupBy('users.name')
        ->orderByDesc('actions')
        ->limit(3)
        ->get();

    return view('tv.teachers_top', compact('teachers'));

})->name('tv.teachers.top');

// 🎖 TOP STUDENTS
Route::get('/tv/top-students', [PointController::class, 'topStudents'])
    ->name('tv.top.students');

// 📊 HOUSE TOTAL
Route::get('/tv/house-total', function () {

    $houses = DB::table('houses')
        ->select('name', 'points', 'colour_hex')
        ->orderByDesc('points')
        ->get();

    return view('tv.house_total', compact('houses'));

})->name('tv.house.total');

// 🌤 WEATHER
Route::get('/tv/weather', function () {
    return view('tv.weather');
})->name('tv.weather');


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

require __DIR__.'/auth.php';
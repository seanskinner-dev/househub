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
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // 🔹 Points system
    Route::get('/points', [PointController::class, 'index'])->name('points.index');
    Route::post('/points', [PointController::class, 'store'])->name('points.store');

    // 🔹 Student profile
    Route::get('/students/{id}', [PointController::class, 'showStudent'])->name('students.show');

    // 🔹 Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 🔹 TV (FULLY FIXED)
    Route::get('/tv', function () {

        // HOUSES
        $houses = DB::table('houses')
            ->select('id', 'name', 'colour_hex', 'points')
            ->orderByDesc('points')
            ->get();

        // RECENT ACTIVITY
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

        // ✅ TOP TEACHER (FIXED)
        $topTeacher = DB::table('point_transactions')
            ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(point_transactions.amount) as total_points'))
            ->groupBy('users.name')
            ->orderByDesc('total_points')
            ->first();

        return view('tv', compact('houses', 'recent', 'topTeacher'));

    })->name('tv');

    // 🔹 House Cup
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
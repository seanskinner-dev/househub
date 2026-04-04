<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function index()
    {
        $students = DB::table('students')
            ->orderBy('id')
            ->get();

        $recent = DB::table('point_transactions')
            ->leftJoin('students', 'point_transactions.student_id', '=', 'students.id')
            ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select(
                'students.first_name',
                'students.last_name',
                'houses.name as house_name',
                'point_transactions.amount',
                'point_transactions.category',
                'point_transactions.created_at',
                'users.name as teacher'
            )
            ->orderByDesc('point_transactions.created_at')
            ->limit(10)
            ->get();

        return view('points.index', [
            'students' => $students,
            'recent'   => $recent,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|integer',
            'house_name' => 'nullable|string',
            'amount'     => 'nullable|integer',
        ]);

        $amount = (int) $request->input('amount', 1);
        $userId = auth()->id() ?? 1;

        // =========================
        // ✅ HOUSE POINTS
        // =========================
        if ($request->filled('house_name')) {

            $house = DB::table('houses')
                ->where('name', $request->house_name)
                ->first();

            if (!$house) {
                return back();
            }

            // Update ALL students in house
            DB::table('students')
                ->where('house_name', $request->house_name)
                ->increment('house_points', $amount);

            // 🔥 CRITICAL FIX: get ONE student (cannot be null)
            $student = DB::table('students')
                ->where('house_name', $request->house_name)
                ->first();

            DB::table('point_transactions')->insert([
                'student_id'  => $student->id, // ✅ FIX
                'house_id'    => $house->id,
                'amount'      => $amount,
                'category'    => 'house',
                'description' => $request->house_name . ' +' . $amount,
                'awarded_by'  => $userId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                ]);
            }

            return back();
        }

        // =========================
        // ✅ STUDENT POINTS
        // =========================
        if ($request->filled('student_id')) {

            DB::table('students')
                ->where('id', $request->student_id)
                ->increment('house_points', $amount);

            DB::table('point_transactions')->insert([
                'student_id'  => $request->student_id,
                'house_id'    => null,
                'amount'      => $amount,
                'category'    => $request->input('category', 'manual'),
                'description' => $request->input('description', 'Manual adjustment'),
                'awarded_by'  => $userId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $newPoints = DB::table('students')
                ->where('id', $request->student_id)
                ->value('house_points');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'points'  => $newPoints,
                    'amount'  => $amount,
                ]);
            }

            return back();
        }

        return back();
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function index()
    {
        $students = DB::table('students')->orderBy('id')->get();

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

        $houses = DB::table('houses')->get();

        return view('points.index', compact('students','recent','houses'));
    }

    public function store(Request $request)
    {
        $amount = (int) $request->input('amount');
        $userId = auth()->id() ?? 1;
        $teacherName = auth()->user()->name ?? 'System';

        return DB::transaction(function () use ($request, $amount, $userId, $teacherName) {

            // =====================
            // HOUSE
            // =====================
            if ($request->filled('house_name')) {

                $house = DB::table('houses')
                    ->where('name', $request->house_name)
                    ->first();

                if ($house) {

                    DB::table('houses')
                        ->where('id', $house->id)
                        ->increment('points', $amount);

                    DB::table('point_transactions')->insert([
                        'student_id' => null,
                        'house_id' => $house->id,
                        'amount' => $amount,
                        'category' => 'house',
                        'description' => 'House points',
                        'awarded_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    return response()->json([
                        'success' => true,
                        'amount' => $amount,
                        'student' => null,
                        'house' => $house->name,
                        'teacher' => $teacherName
                    ]);
                }
            }

            // =====================
            // STUDENT
            // =====================
            if ($request->filled('student_id')) {

                $student = DB::table('students')
                    ->where('id', $request->student_id)
                    ->first();

                if ($student) {

                    DB::table('students')
                        ->where('id', $student->id)
                        ->increment('house_points', $amount);

                    $house = DB::table('houses')
                        ->where('name', $student->house_name)
                        ->first();

                    if ($house) {
                        DB::table('houses')
                            ->where('id', $house->id)
                            ->increment('points', $amount);
                    }

                    DB::table('point_transactions')->insert([
                        'student_id' => $student->id,
                        'house_id' => $house->id ?? null,
                        'amount' => $amount,
                        'category' => $request->input('category', 'manual'),
                        'description' => $request->input('description', ''),
                        'awarded_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    return response()->json([
                        'success' => true,
                        'amount' => $amount,
                        'student' => $student->first_name . ' ' . $student->last_name,
                        'house' => $student->house_name,
                        'teacher' => $teacherName
                    ]);
                }
            }

            return response()->json(['success' => false]);
        });
    }

    // ✅ NEW METHOD — STUDENT PROFILE
    public function showStudent($id)
    {
        $student = DB::table('students')->where('id', $id)->first();

        if (!$student) {
            abort(404);
        }

        return view('students.show', compact('student'));
    }
}
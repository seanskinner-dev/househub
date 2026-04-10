<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function index()
    {
        $students = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.*',
                'houses.name as house_name',
                'houses.colour_hex'
            )
            ->orderBy('students.id')
            ->get();

        // ✅ FIXED: supports BOTH student + house events
        $recent = DB::table('point_transactions')
            ->leftJoin('students', 'point_transactions.student_id', '=', 'students.id')
            ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select(
                DB::raw("
                    CASE 
                        WHEN point_transactions.student_id IS NULL 
                        THEN houses.name 
                        ELSE CONCAT(students.first_name, ' ', students.last_name)
                    END as name
                "),
                'students.first_name',
                'students.last_name',
                'houses.name as house_name',
                'point_transactions.amount',
                'point_transactions.category',
                'point_transactions.description',
                'point_transactions.created_at',
                'users.name as teacher'
            )
            ->orderByDesc('point_transactions.created_at')
            ->limit(10)
            ->get();

        $houses = cache()->remember('houses', 60, function () {
            return DB::table('houses')->get();
        });

        return view('points.index', compact('students', 'recent', 'houses'));
    }

    public function dashboard()
    {
        $houses = DB::table('houses')
            ->select('id', 'name', 'colour_hex', 'points')
            ->orderByDesc('points')
            ->get();

        $topStudents = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.*',
                'houses.name as house_name',
                'houses.colour_hex'
            )
            ->orderByDesc('students.house_points')
            ->limit(5)
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
            ->limit(6)
            ->get();

        $pointsToday = DB::table('point_transactions')
            ->whereDate('created_at', today())
            ->sum('amount');

        $pointsWeek = DB::table('point_transactions')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('amount');

        $topTeacher = DB::table('point_transactions')
            ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(point_transactions.amount) as total_points'))
            ->groupBy('users.name')
            ->orderByDesc('total_points')
            ->first();

        return view('dashboard', compact(
            'houses',
            'topStudents',
            'recent',
            'pointsToday',
            'pointsWeek',
            'topTeacher'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer',
            'student_id' => 'nullable|exists:students,id',
            'house_name' => 'nullable|exists:houses,name',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $amount = (int) $request->input('amount');
        $userId = auth()->id() ?? 1;

        return DB::transaction(function () use ($request, $amount, $userId) {

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
                        'teacher' => auth()->user()->name ?? 'System',
                        'category' => 'house'
                    ]);
                }
            }

            if ($request->filled('student_id')) {

                $student = DB::table('students')
                    ->where('id', $request->student_id)
                    ->first();

                if ($student) {

                    DB::table('students')
                        ->where('id', $student->id)
                        ->increment('house_points', $amount);

                    DB::table('houses')
                        ->where('id', $student->house_id)
                        ->increment('points', $amount);

                    DB::table('point_transactions')->insert([
                        'student_id' => $student->id,
                        'house_id' => $student->house_id,
                        'amount' => $amount,
                        'category' => $request->input('category', 'manual'),
                        'description' => $request->input('description', ''),
                        'awarded_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $house = DB::table('houses')->where('id', $student->house_id)->first();

                    return response()->json([
                        'success' => true,
                        'amount' => $amount,
                        'student' => $student->first_name . ' ' . $student->last_name,
                        'house' => $house->name ?? null,
                        'teacher' => auth()->user()->name ?? 'System',
                        'category' => 'student'
                    ]);
                }
            }

            return response()->json(['success' => false]);
        });
    }

    public function showStudent($id)
    {
        $student = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.*',
                'houses.name as house_name',
                'houses.colour_hex'
            )
            ->where('students.id', $id)
            ->first();

        if (!$student) abort(404);

        return view('students.show', compact('student'));
    }

    public function houseTrends()
    {
        $raw = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                DB::raw("EXTRACT(DOW FROM point_transactions.created_at) as dow"),
                'houses.name',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->where('point_transactions.created_at', '>=', now()->subDays(10))
            ->whereRaw("EXTRACT(DOW FROM point_transactions.created_at) BETWEEN 1 AND 5")
            ->groupBy('dow', 'houses.name')
            ->orderBy('dow')
            ->get();

        $houses = [
            'Slytherin' => array_fill(0, 5, 0),
            'Hufflepuff' => array_fill(0, 5, 0),
            'Ravenclaw' => array_fill(0, 5, 0),
            'Gryffindor' => array_fill(0, 5, 0),
        ];

        foreach ($raw as $row) {
            $index = (int)$row->dow - 1;
            if (isset($houses[$row->name])) {
                $houses[$row->name][$index] = (int)$row->total;
            }
        }

        return view('tv.house_trends', [
            'slytherin' => $houses['Slytherin'],
            'hufflepuff' => $houses['Hufflepuff'],
            'ravenclaw' => $houses['Ravenclaw'],
            'gryffindor' => $houses['Gryffindor'],
        ]);
    }

    public function housePointsMonth()
    {
        $data = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                DB::raw("DATE(point_transactions.created_at) as date"),
                'houses.name',
                'houses.colour_hex',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->groupBy('date', 'houses.name', 'houses.colour_hex')
            ->orderBy('date')
            ->get();

        return view('tv.house_month', compact('data'));
    }

    public function housePointsYear()
    {
        $data = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                DB::raw("EXTRACT(YEAR FROM point_transactions.created_at) as year"),
                'houses.name',
                'houses.colour_hex',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->groupBy('year', 'houses.name', 'houses.colour_hex')
            ->orderBy('year')
            ->get();

        return view('tv.house_year', compact('data'));
    }

    public function teacherHighlights()
    {
        $teachers = DB::table('point_transactions')
            ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(point_transactions.amount) as total_points'))
            ->groupBy('users.name')
            ->orderByDesc('total_points')
            ->limit(5)
            ->get();

        return view('tv.teacher_highlights', compact('teachers'));
    }

    public function teacherHighlightsMonth()
    {
        $teachers = DB::table('point_transactions')
            ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(point_transactions.amount) as total_points'))
            ->groupBy('users.name')
            ->orderByDesc('total_points')
            ->limit(5)
            ->get();

        return view('tv.teacher_highlights_month', compact('teachers'));
    }

    public function topStudents()
    {
        $students = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.id',
                'students.first_name',
                'students.last_name',
                DB::raw('COALESCE(students.house_points, 0) as points'),
                'students.year_level',
                'houses.name as house_name',
                'houses.colour_hex'
            )
            ->orderByDesc('points')
            ->limit(12)
            ->get();

        return view('top_students', compact('students'));
    }

    public function houseMomentum()
    {
        $data = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                'houses.name',
                'houses.colour_hex',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->whereBetween('point_transactions.created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->groupBy('houses.name', 'houses.colour_hex')
            ->orderByDesc('total')
            ->get();

        return view('tv.house_momentum', compact('data'));
    }
}
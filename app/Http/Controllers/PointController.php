<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PointController extends Controller
{
    public function index()
    {
        // 🔥 ONLY CHANGE: JOIN houses to get house_name
        $students = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.*',
                'houses.name as house_name'
            )
            ->orderBy('students.id')
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

        $houses = DB::table('houses')->get();

        return view('points.index', compact('students','recent','houses'));
    }

    public function store(Request $request)
    {
        // 🔧 FIX: default amount
        $amount = (int) $request->input('amount', 1);

        $userId = auth()->id() ?? 1;
        $teacherName = auth()->user()->name ?? 'System';

        return DB::transaction(function () use ($request, $amount, $userId, $teacherName) {

            $student = null;
            $house = null;

            // 🔧 FIX: handle house click WITHOUT early return
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

                    $house = null;
                    if (! empty($student->house_id)) {
                        $house = DB::table('houses')->where('id', $student->house_id)->first();
                    }
                    if (! $house && ! empty($student->house_name)) {
                        $house = DB::table('houses')->where('name', $student->house_name)->first();
                    }

                    if ($house) {
                        DB::table('houses')
                            ->where('id', $house->id)
                            ->increment('points', $amount);
                    }

                    DB::table('point_transactions')->insert([
                        'student_id' => $student->id,
                        'house_id' => $house->id ?? null,
                        'amount' => $amount,
                        // 🔧 FIX: use type instead of category
                        'category' => $request->input('type', 'manual'),
                        'description' => $request->input('description', ''),
                        'awarded_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 🔧 FIX: ALWAYS return valid response
            return response()->json([
                'success' => true,
                'amount' => $amount,
                'student' => $student ? $student->first_name . ' ' . $student->last_name : null,
                'house' => $house ? $house->name : null,
                'teacher' => $teacherName
            ]);
        });
    }

    public function showStudent($id)
    {
        $student = DB::table('students')->where('id', $id)->first();

        if (!$student) abort(404);

        $awards = DB::table('awards')
            ->where('student_id', $id)
            ->orderByDesc('awarded_at')
            ->get();

        $commendations = DB::table('point_transactions')
            ->where('student_id', $id)
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->orderByDesc('created_at')
            ->get();

        return view('students.show', compact(
            'student',
            'awards',
            'commendations'
        ));
    }

    public function certificate($id)
    {
        $award = DB::table('awards')
            ->leftJoin('students', 'awards.student_id', '=', 'students.id')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'awards.*',
                'students.first_name',
                'students.last_name',
                'houses.name as house_name'
            )
            ->where('awards.id', $id)
            ->first();

        if (!$award) abort(404);

        return view('certificates.show', compact('award'));
    }

    public function tv()
    {
        $raw = DB::table('point_transactions')
            ->selectRaw('DATE(point_transactions.created_at) as date, houses.name as house, SUM(point_transactions.amount) as total')
            ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->where('point_transactions.created_at', '>=', now()->subDays(7))
            ->groupByRaw('DATE(point_transactions.created_at), houses.name')
            ->orderBy('date')
            ->get();

        $dates = $raw->pluck('date')->unique()->values();

        $houseMap = [
            'Gryffindor' => '#740001',
            'Slytherin'  => '#1a472a',
            'Ravenclaw'  => '#0e1a40',
            'Hufflepuff' => '#ffcc00',
        ];

        $series = [];

        foreach ($houseMap as $house => $colour) {
            $series[$house] = array_fill(0, count($dates), 0);
        }

        foreach ($raw as $row) {
            $formattedDate = Carbon::parse($row->date)->format('Y-m-d');
            $index = $dates->search($formattedDate);

            if ($index !== false && isset($series[$row->house])) {
                $series[$row->house][$index] = (int) $row->total;
            }
        }

        $apexSeries = [];

        foreach ($houseMap as $house => $colour) {
            $apexSeries[] = [
                'name' => $house,
                'data' => $series[$house],
                'color' => $colour,
            ];
        }

        $labels = $dates->map(function ($d) {
            return Carbon::parse($d)->format('D');
        });

        $topStudents = DB::table('point_transactions')
            ->join('students', 'point_transactions.student_id', '=', 'students.id')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.id',
                'students.first_name',
                'students.last_name',
                DB::raw('MAX(houses.name) as house_name'),
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->where('point_transactions.created_at', '>=', now()->subDays(7))
            ->whereNotNull('point_transactions.student_id')
            ->groupBy('students.id', 'students.first_name', 'students.last_name')
            ->orderByDesc('total')
            ->limit(30)
            ->get();

        $topTeachers = DB::table('point_transactions')
            ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->where('point_transactions.created_at', '>=', now()->subDays(7))
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('tv.index', [
            'series' => $apexSeries,
            'dates' => $labels,
            'topStudents' => $topStudents,
            'topTeachers' => $topTeachers,
        ]);
    }
}
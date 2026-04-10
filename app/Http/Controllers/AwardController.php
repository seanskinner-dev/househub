<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\House;
use App\Models\Award;
use App\Models\Commendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{
    /**
     * Display the teacher's dashboard
     */
    public function index()
    {
        $houses = House::query()
            ->orderBy('name')
            ->get();

        $students = Student::query()
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select('students.*', 'houses.name as house_name', 'houses.colour_hex')
            ->orderBy('students.first_name')
            ->get();

        return view('awards.index', compact('houses', 'students'));
    }

    /**
     * Add +1 point to a student or a whole house
     */
    public function storePoint(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'house_name' => 'nullable|exists:houses,name',
            'amount' => 'nullable|integer',
        ]);

        $amount = (int) $request->input('amount', 1);
        $userId = auth()->id() ?? 1;

        DB::transaction(function () use ($request, $amount, $userId) {
            if ($request->filled('student_id')) {
                $student = Student::query()->findOrFail($request->student_id);

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
                    'category' => 'manual',
                    'description' => '',
                    'awarded_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            if ($request->filled('house_name')) {
                $house = House::query()
                    ->where('name', $request->house_name)
                    ->firstOrFail();

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
        });

        if ($request->filled('student_id')) {
            return back()->with('success', 'Points updated for student!');
        }

        if ($request->filled('house_name')) {
            return back()->with('success', "Points updated for House {$request->house_name}!");
        }

        return back()->with('error', 'No student or house selected.');
    }

    /**
     * Save a Commendation (Star)
     */
    public function storeCommendation(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        Commendation::create([
            'student_id' => $request->student_id,
            'awarded_by' => auth()->id() ?? 1, // Fallback to user 1 if not logged in
        ]);

        return back()->with('success', 'Commendation recorded!');
    }

    /**
     * Save a formal Award (Trophy) via AJAX/Modal
     */
    public function storeAward(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'award_name' => 'required|string|max:255',
            'award_description' => 'required|string',
        ]);

        Award::create([
            'student_id' => $request->student_id,
            'awarded_by' => auth()->id() ?? 1,
            'name' => $request->award_name,
            'description' => $request->award_description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Award successfully issued!'
        ]);
    }
}

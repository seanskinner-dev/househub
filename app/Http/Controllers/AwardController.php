<?php

namespace App\Http\Controllers;

use App\Models\Student;
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
        // Get unique houses for the quick-add buttons
        $houses = Student::select('house_name')
            ->distinct()
            ->orderBy('house_name')
            ->get();

        // Get all students for the search list
        $students = Student::orderBy('first_name')->get();

        return view('awards.index', compact('houses', 'students'));
    }

    /**
     * Add +1 point to a student or a whole house
     */
    public function storePoint(Request $request)
    {
        // 1. Award to specific student
        if ($request->filled('student_id')) {
            Student::where('id', $request->student_id)
                ->increment('house_points');
            
            return back()->with('success', 'Point added to student!');
        } 
        
        // 2. Award to everyone in a house (Bulk Add)
        if ($request->filled('house_name')) {
            Student::where('house_name', $request->house_name)
                ->increment('house_points');
                
            return back()->with('success', "Point added to all of House {$request->house_name}!");
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
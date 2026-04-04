<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Student;
use Illuminate\Http\Request;

class HouseController extends Controller {
    public function index() {
        // ID order keeps the 4 big house buttons locked in place
        $houses = House::orderBy('id', 'asc')->get();
        // Last name order keeps the student list from jumping when points are added
        $students = Student::orderBy('last_name', 'asc')->get();

        return view('leaderboard', compact('houses', 'students'));
    }

    public function addPoints(Request $request) {
        if ($request->has('house_id')) {
            House::findOrFail($request->house_id)->increment('points', 1);
        }

        if ($request->has('student_id')) {
            $student = Student::findOrFail($request->student_id);
            $student->increment('points', 1);
            House::where('name', $student->house_name)->increment('points', 1);
        }

        return redirect()->back();
    }
}
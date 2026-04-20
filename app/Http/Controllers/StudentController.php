<?php

namespace App\Http\Controllers;

use App\Models\Student;

class StudentController extends Controller
{
    public function show($id)
    {
        // TODO: When multi-school support is introduced, restrict access by school_id
        $student = Student::where('id', $id)->firstOrFail();

        return view('students.show', compact('student'));
    }
}
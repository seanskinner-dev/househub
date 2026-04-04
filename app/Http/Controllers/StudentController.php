\<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function show($id)
    {
        $student = DB::table('students')->where('id', $id)->first();

        if (!$student) {
            abort(404);
        }

        return view('students.show', compact('student'));
    }
}
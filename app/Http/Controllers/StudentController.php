<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentController extends Controller
{
    private function pointTransactionsHasTeacherName(): bool
    {
        static $hasTeacherName = null;

        if ($hasTeacherName !== null) {
            return $hasTeacherName;
        }

        $hasTeacherName = Schema::hasColumn('point_transactions', 'teacher_name');

        return $hasTeacherName;
    }

    public function show($id)
    {
        // TODO: When multi-school support is introduced, restrict access by school_id
        $student = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select('students.*', 'houses.name as house_name')
            ->where('students.id', $id)
            ->first();

        if (! $student) {
            abort(404);
        }

        $awards = DB::table('awards')
            ->leftJoin('users', 'awards.awarded_by', '=', 'users.id')
            ->select('awards.*', 'users.name as teacher_name')
            ->where('awards.student_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $hasTeacherName = $this->pointTransactionsHasTeacherName();

        $teacherNameSelect = $hasTeacherName
            ? DB::raw("COALESCE(point_transactions.teacher_name, users.name, 'Unknown') as teacher_name")
            : DB::raw("COALESCE(users.name, 'Unknown') as teacher_name");

        $pointTransactions = DB::table('point_transactions')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('point_transactions.*', $teacherNameSelect)
            ->where('point_transactions.student_id', $id)
            ->where('point_transactions.amount', '!=', 0)
            ->orderByDesc('point_transactions.created_at')
            ->get();

        $commendations = DB::table('point_transactions')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('point_transactions.*', $teacherNameSelect)
            ->where('point_transactions.student_id', $id)
            ->where('point_transactions.category', 'commendation')
            ->whereNotNull('point_transactions.description')
            ->where('point_transactions.description', '!=', '')
            ->orderByDesc('created_at')
            ->get();

        $awardCount = $awards->count();
        $commendationCount = $commendations->count();

        return view('students.show', compact(
            'student',
            'pointTransactions',
            'awards',
            'commendations',
            'awardCount',
            'commendationCount'
        ));
    }
}

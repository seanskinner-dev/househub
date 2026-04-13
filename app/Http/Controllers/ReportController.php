<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function housePerformance()
    {
        $now = Carbon::now();
        $year = (int) $now->year;
        $month = (int) $now->month;

        $yearStart = Carbon::create($year, 1, 1)->startOfDay();

        $currentTerm = $this->termNumberFromMonth($month);
        [$currStart, $currEnd] = $this->termDateRange($year, $currentTerm);

        if ($currentTerm === 1) {
            $prevYear = $year - 1;
            $prevTerm = 4;
        } else {
            $prevYear = $year;
            $prevTerm = $currentTerm - 1;
        }
        [$prevStart, $prevEnd] = $this->termDateRange($prevYear, $prevTerm);

        $rows = DB::table('houses')
            ->leftJoin('point_transactions', 'houses.id', '=', 'point_transactions.house_id')
            ->selectRaw(
                'houses.name as house, '.
                'COALESCE(SUM(CASE WHEN point_transactions.created_at >= ? AND point_transactions.created_at <= ? '.
                'THEN point_transactions.amount ELSE 0 END), 0) as year_total, '.
                'COALESCE(SUM(CASE WHEN point_transactions.created_at >= ? AND point_transactions.created_at <= ? '.
                'THEN point_transactions.amount ELSE 0 END), 0) as term_total, '.
                'COALESCE(SUM(CASE WHEN point_transactions.created_at >= ? AND point_transactions.created_at <= ? '.
                'THEN point_transactions.amount ELSE 0 END), 0) as last_term_total',
                [
                    $yearStart, $now,
                    $currStart, $currEnd,
                    $prevStart, $prevEnd,
                ]
            )
            ->groupBy('houses.id', 'houses.name', 'houses.colour_hex')
            ->orderByDesc(DB::raw('year_total'))
            ->get();

        $housePerformance = $rows->map(function ($row) {
            return [
                'house' => $row->house,
                'year_total' => (int) $row->year_total,
                'term_total' => (int) $row->term_total,
                'last_term_total' => (int) $row->last_term_total,
            ];
        })->values()->all();

        return view('reports.house', [
            'housePerformance' => $housePerformance,
        ]);
    }

    public function atRiskStudents()
    {
        $since = Carbon::now()->subDays(30);

        $rows = DB::table('students')
            ->leftJoin('point_transactions', 'students.id', '=', 'point_transactions.student_id')
            ->selectRaw(
                'TRIM(CONCAT(students.first_name, \' \', students.last_name)) as name, '.
                'students.house_name as house, '.
                'COALESCE(SUM(CASE WHEN point_transactions.created_at >= ? THEN point_transactions.amount ELSE 0 END), 0) as points_last_30_days, '.
                'MAX(point_transactions.created_at) as last_activity',
                [$since]
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.house_name')
            ->havingRaw(
                'COALESCE(SUM(CASE WHEN point_transactions.created_at >= ? THEN point_transactions.amount ELSE 0 END), 0) = 0',
                [$since]
            )
            ->orderByRaw('MAX(point_transactions.created_at) ASC NULLS FIRST')
            ->orderBy('name')
            ->get();

        $students = $rows->map(function ($row) {
            return [
                'name' => $row->name,
                'house' => $row->house,
                'points_last_30_days' => (int) $row->points_last_30_days,
                'last_activity' => $row->last_activity
                    ? Carbon::parse($row->last_activity)->format('Y-m-d H:i')
                    : null,
            ];
        })->values()->all();

        return view('reports.pc', [
            'students' => $students,
        ]);
    }

    private function termNumberFromMonth(int $month): int
    {
        if ($month >= 1 && $month <= 3) {
            return 1;
        }
        if ($month >= 4 && $month <= 6) {
            return 2;
        }
        if ($month >= 7 && $month <= 9) {
            return 3;
        }

        return 4;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function termDateRange(int $year, int $term): array
    {
        return match ($term) {
            1 => [
                Carbon::create($year, 1, 1)->startOfDay(),
                Carbon::create($year, 3, 31)->endOfDay(),
            ],
            2 => [
                Carbon::create($year, 4, 1)->startOfDay(),
                Carbon::create($year, 6, 30)->endOfDay(),
            ],
            3 => [
                Carbon::create($year, 7, 1)->startOfDay(),
                Carbon::create($year, 9, 30)->endOfDay(),
            ],
            default => [
                Carbon::create($year, 10, 1)->startOfDay(),
                Carbon::create($year, 12, 31)->endOfDay(),
            ],
        };
    }
}

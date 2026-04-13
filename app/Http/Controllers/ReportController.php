<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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

        $filter = request('filter');
        $filter = in_array($filter, ['high', 'medium', 'active'], true) ? $filter : null;

        $selectedDate = $this->pcValidatedDate(request('date'));
        $selectedHouse = $this->pcValidatedHouseName(request('house'));

        $counts = $this->pcRiskDistributionCounts($since);
        $trendData = $this->pcEngagementTrend($filter, $selectedHouse);
        $houseRiskCounts = $this->pcHouseRiskBreakdown($since, $filter);

        return view('reports.pc', [
            'counts' => $counts,
            'trendData' => $trendData,
            'houseRiskCounts' => $houseRiskCounts,
            'filter' => $filter,
            'selectedDate' => $selectedDate,
            'selectedHouse' => $selectedHouse,
        ]);
    }

    /**
     * @return array{high: int, medium: int, active: int}
     */
    private function pcRiskDistributionCounts(Carbon $since): array
    {
        $perStudent = DB::table('students')
            ->leftJoin('point_transactions', 'students.id', '=', 'point_transactions.student_id')
            ->select('students.id')
            ->selectRaw('COUNT(point_transactions.id) as txn_count')
            ->selectRaw('BOOL_OR(point_transactions.created_at >= ?) as has_recent', [$since])
            ->groupBy('students.id');

        $countRow = DB::query()->fromSub($perStudent, 'per_student')
            ->selectRaw('SUM(CASE WHEN txn_count = 0 THEN 1 ELSE 0 END) as high')
            ->selectRaw('SUM(CASE WHEN txn_count > 0 AND NOT COALESCE(has_recent, FALSE) THEN 1 ELSE 0 END) as medium')
            ->selectRaw('SUM(CASE WHEN COALESCE(has_recent, FALSE) THEN 1 ELSE 0 END) as active')
            ->first();

        return [
            'high' => (int) ($countRow->high ?? 0),
            'medium' => (int) ($countRow->medium ?? 0),
            'active' => (int) ($countRow->active ?? 0),
        ];
    }

    private function pcValidatedDate(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function pcValidatedHouseName(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $exists = DB::table('houses')->where('name', $value)->exists();

        return $exists ? $value : null;
    }

    /**
     * Student IDs matching a risk segment (high / medium / active).
     */
    private function pcStudentIdsForRiskFilter(Carbon $since, string $filter): Collection
    {
        $query = DB::table('students')
            ->leftJoin('point_transactions', 'students.id', '=', 'point_transactions.student_id')
            ->select('students.id')
            ->groupBy('students.id');

        match ($filter) {
            'high' => $query->havingRaw('COUNT(point_transactions.id) = 0'),
            'medium' => $query->havingRaw(
                'COUNT(point_transactions.id) > 0 AND NOT COALESCE(BOOL_OR(point_transactions.created_at >= ?), FALSE)',
                [$since]
            ),
            'active' => $query->havingRaw('COALESCE(BOOL_OR(point_transactions.created_at >= ?), FALSE)', [$since]),
            default => $query->whereRaw('1 = 0'),
        };

        return $query->pluck('id');
    }

    /**
     * Daily total points for the last 30 calendar days (inclusive of today).
     * Optional risk filter and/or house narrow the transaction set.
     *
     * @return list<array{date: string, points: int}>
     */
    private function pcEngagementTrend(?string $filter, ?string $house): array
    {
        $endDay = Carbon::today();
        $startDay = $endDay->copy()->subDays(29);

        $since = Carbon::now()->subDays(30);
        $query = DB::table('point_transactions as pt')
            ->where('pt.created_at', '>=', $startDay->copy()->startOfDay())
            ->where('pt.created_at', '<=', Carbon::now());

        if ($house !== null) {
            $query->join('students as s', 'pt.student_id', '=', 's.id')
                ->join('houses as h', 's.house_id', '=', 'h.id')
                ->where('h.name', $house);
        }

        if ($filter !== null) {
            $ids = $this->pcStudentIdsForRiskFilter($since, $filter);
            if ($ids->isEmpty()) {
                return $this->pcEmptyTrendSeries($startDay, $endDay);
            }
            $query->whereIn('pt.student_id', $ids);
        }

        $daily = $query
            ->selectRaw('(pt.created_at::date) as day')
            ->selectRaw('SUM(pt.amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy(function ($row) {
                return Carbon::parse($row->day)->format('Y-m-d');
            });

        $out = [];
        for ($d = $startDay->copy(); $d->lte($endDay); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $out[] = [
                'date' => $key,
                'points' => (int) ($daily->get($key)->total ?? 0),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{date: string, points: int}>
     */
    private function pcEmptyTrendSeries(Carbon $startDay, Carbon $endDay): array
    {
        $out = [];
        for ($d = $startDay->copy(); $d->lte($endDay); $d->addDay()) {
            $out[] = ['date' => $d->format('Y-m-d'), 'points' => 0];
        }

        return $out;
    }

    /**
     * Count students per house for the current risk lens.
     * No filter: at-risk only (high + medium — no activity in last 30 days).
     * With filter: students in that segment per house.
     *
     * @return list<array{house: string, count: int}>
     */
    private function pcHouseRiskBreakdown(Carbon $since, ?string $filter): array
    {
        $query = DB::table('students')
            ->join('houses', 'students.house_id', '=', 'houses.id')
            ->leftJoin('point_transactions', 'students.id', '=', 'point_transactions.student_id')
            ->select('houses.name as house')
            ->groupBy('houses.id', 'houses.name', 'students.id');

        if ($filter === 'high') {
            $query->havingRaw('COUNT(point_transactions.id) = 0');
        } elseif ($filter === 'medium') {
            $query->havingRaw(
                'COUNT(point_transactions.id) > 0 AND NOT COALESCE(BOOL_OR(point_transactions.created_at >= ?), FALSE)',
                [$since]
            );
        } elseif ($filter === 'active') {
            $query->havingRaw('COALESCE(BOOL_OR(point_transactions.created_at >= ?), FALSE)', [$since]);
        } else {
            $query->havingRaw('NOT COALESCE(BOOL_OR(point_transactions.created_at >= ?), FALSE)', [$since]);
        }

        $counts = $query->get()->countBy('house');

        return DB::table('houses')
            ->orderBy('name')
            ->get()
            ->map(function ($row) use ($counts) {
                return [
                    'house' => $row->name,
                    'count' => (int) ($counts[$row->name] ?? 0),
                ];
            })
            ->values()
            ->all();
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

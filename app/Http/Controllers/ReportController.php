<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $houses = DB::table('houses')->orderBy('name')->pluck('name')->values()->all();

        return view('reports.pc', [
            'houses' => $houses,
        ]);
    }

    public function leadership()
    {
        $houses = DB::table('houses')->orderBy('name')->get();

        return view('reports.leadership', compact('houses'));
    }

    public function reportChartData(Request $request)
    {
        [$start, $end, $house, $yearFilter] = $this->pcParseFilters($request);

        return response()->json([
            'donut' => $this->pcChartDonut($house, $start, $end, $yearFilter),
            'trend' => $this->pcChartTrend($house, $start, $end, $yearFilter),
            'house_breakdown' => $this->pcChartHousePoints($house, $start, $end, $yearFilter),
            'year_level' => $this->pcChartYearLevel($house, $start, $end, $yearFilter),
        ]);
    }

    public function reportDrilldown(Request $request)
    {
        $label = trim((string) $request->query('label', ''));
        [$start, $end, $house, $yearFilter] = $this->pcParseFilters($request);

        if ($label === '') {
            return response()->json(['title' => 'Drill-down', 'rows' => []]);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $label)) {
            return response()->json($this->pcDrilldownDate($label, $house, $start, $end, $yearFilter));
        }

        if ($label === 'High Risk') {
            return response()->json($this->pcDrilldownRiskHigh($house, $yearFilter));
        }

        if ($label === 'Medium Risk') {
            return response()->json($this->pcDrilldownRiskMedium($house, $start, $end, $yearFilter));
        }

        if ($label === 'Active') {
            return response()->json($this->pcDrilldownRiskActive($house, $start, $end, $yearFilter));
        }

        if ($label === 'Low') {
            return response()->json($this->pcDrilldownLowEngagement($house, $start, $end, $yearFilter));
        }

        // ===== YEAR LEVEL DRILLDOWN (FIX) — matches chart categories "Year N"; before house matching
        if (preg_match('/^Year\s+(\d+)$/', $label, $matches)) {
            $year = (int) $matches[1];

            $query = DB::table('point_transactions')
                ->join('students', 'students.id', '=', 'point_transactions.student_id');

            if ($house !== 'All') {
                $query->join('houses', 'houses.id', '=', 'students.house_id')
                    ->where('houses.name', $house);
            }

            if ($start && $end) {
                $query->whereBetween('point_transactions.created_at', [$start, $end]);
            }

            $query->whereRaw('EXTRACT(DOW FROM point_transactions.created_at::timestamp) BETWEEN 1 AND 5')
                ->where('students.year_level', $year);

            if ($yearFilter !== 'All') {
                $query->where('students.year_level', (int) $yearFilter);
            }

            $rows = $query
                ->select(
                    'students.first_name',
                    'students.last_name',
                    'students.year_level',
                    'point_transactions.amount',
                    'point_transactions.created_at'
                )
                ->orderByDesc('point_transactions.created_at')
                ->limit(100)
                ->get();

            return response()->json([
                'rows' => $rows,
            ]);
        }

        if (DB::table('houses')->where('name', $label)->exists()) {
            return response()->json($this->pcDrilldownHouseBar($label, $start, $end, $yearFilter));
        }

        return response()->json(['title' => $label, 'rows' => []]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string, 3: string}
     */
    private function pcParseFilters(Request $request): array
    {
        $house = (string) $request->query('house', 'All');
        if ($house !== 'All' && ! DB::table('houses')->where('name', $house)->exists()) {
            $house = 'All';
        }

        $yearFilter = (string) $request->query('year', 'All');
        if ($yearFilter !== 'All' && ! in_array($yearFilter, ['7', '8', '9', '10', '11', '12'], true)) {
            $yearFilter = 'All';
        }

        $endIn = $this->pcParseDateParam($request->query('end_date'));
        $startIn = $this->pcParseDateParam($request->query('start_date'));

        $end = $endIn ? $endIn->copy()->endOfDay() : Carbon::today()->endOfDay();
        $start = $startIn ?? $end->copy()->subDays(29)->startOfDay();

        if ($start->gt($end)) {
            $tmp = $start->copy();
            $start = $end->copy()->startOfDay();
            $end = $tmp->copy()->endOfDay();
        }

        return [$start, $end, $house, $yearFilter];
    }

    private function pcParseDateParam(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function pcStudentBaseForHouse(string $house)
    {
        if ($house === 'All') {
            return DB::table('students');
        }

        return DB::table('students')
            ->join('houses', 'students.house_id', '=', 'houses.id')
            ->where('houses.name', $house);
    }

    private function pcStudentWithHouseQuery(string $house)
    {
        $q = DB::table('students')
            ->leftJoin('houses as h', 'students.house_id', '=', 'h.id');
        if ($house !== 'All') {
            $q->where('h.name', $house);
        }

        return $q;
    }

    private function pcTransactionsInRangeWeekday($query, Carbon $start, Carbon $end, string $ptAlias = 'pt'): void
    {
        $query->whereBetween($ptAlias.'.created_at', [$start, $end])
            ->whereRaw('EXTRACT(DOW FROM '.$ptAlias.'.created_at::timestamp) BETWEEN 1 AND 5');
    }

    /**
     * @return array{labels: list<string>, series: list<int>}
     */
    private function pcChartDonut(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $rows = $this->pcStudentBaseForHouse($house)
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('students.year_level', (int) $yearFilter);
            })
            ->leftJoin('point_transactions as pw', function ($join) use ($start, $end) {
                $join->on('students.id', '=', 'pw.student_id');
                $this->pcTransactionsInRangeWeekday($join, $start, $end, 'pw');
            })
            ->select('students.id')
            ->selectRaw('COUNT(pw.id) as window_n')
            ->selectRaw('(SELECT COUNT(*) FROM point_transactions t WHERE t.student_id = students.id) as life_n')
            ->groupBy('students.id')
            ->get();

        $high = $rows->where('life_n', 0)->count();
        $active = $rows->where('window_n', '>', 0)->count();
        $medium = $rows->where('life_n', '>', 0)->where('window_n', 0)->count();

        return [
            'labels' => ['High Risk', 'Medium Risk', 'Active'],
            'series' => [(int) $high, (int) $medium, (int) $active],
        ];
    }

    /**
     * @return array{categories: list<string>, series: list<int>}
     */
    private function pcChartTrend(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $categories = [];
        $series = [];

        for ($d = $start->copy()->startOfDay(); $d->lte($end->copy()->startOfDay()); $d->addDay()) {
            if ($d->dayOfWeekIso < 1 || $d->dayOfWeekIso > 5) {
                continue;
            }
            $dayStr = $d->format('Y-m-d');

            $q = DB::table('point_transactions as pt')
                ->whereDate('pt.created_at', $dayStr)
                ->whereBetween('pt.created_at', [$start, $end])
                ->whereRaw('EXTRACT(DOW FROM pt.created_at::timestamp) BETWEEN 1 AND 5');

            if ($house !== 'All' || $yearFilter !== 'All') {
                $q->join('students as s', 'pt.student_id', '=', 's.id');
            }
            if ($yearFilter !== 'All') {
                $q->where('s.year_level', (int) $yearFilter);
            }
            if ($house !== 'All') {
                $q->join('houses as h', 's.house_id', '=', 'h.id')
                    ->where('h.name', $house);
            }

            $categories[] = $dayStr;
            $series[] = (int) $q->sum('pt.amount');
        }

        return [
            'categories' => $categories,
            'series' => $series,
        ];
    }

    /**
     * @return array{categories: list<string>, series: list<int>}
     */
    private function pcChartHousePoints(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $q = DB::table('houses')
            ->leftJoin('students as s', 's.house_id', '=', 'houses.id')
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('s.year_level', (int) $yearFilter);
            })
            ->leftJoin('point_transactions as pt', function ($join) use ($start, $end) {
                $join->on('pt.student_id', '=', 's.id')
                    ->whereBetween('pt.created_at', [$start, $end])
                    ->whereRaw('EXTRACT(DOW FROM pt.created_at::timestamp) BETWEEN 1 AND 5');
            })
            ->select('houses.name as house')
            ->selectRaw('COALESCE(SUM(pt.amount), 0) as total')
            ->groupBy('houses.id', 'houses.name')
            ->orderBy('houses.name');

        if ($house !== 'All') {
            $q->where('houses.name', $house);
        }

        $rows = $q->get();

        return [
            'categories' => $rows->pluck('house')->all(),
            'series' => $rows->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /**
     * @return array{categories: list<string>, series: list<int>}
     */
    private function pcChartYearLevel(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $q = DB::table('point_transactions')
            ->join('students', 'students.id', '=', 'point_transactions.student_id')
            ->whereBetween('point_transactions.created_at', [$start, $end])
            ->whereRaw('EXTRACT(DOW FROM point_transactions.created_at::timestamp) BETWEEN 1 AND 5')
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('students.year_level', (int) $yearFilter);
            })
            ->select('students.year_level')
            ->selectRaw('SUM(point_transactions.amount) as total')
            ->groupBy('students.year_level')
            ->orderBy('students.year_level');

        if ($house !== 'All') {
            $q->join('houses', 'students.house_id', '=', 'houses.id')
                ->where('houses.name', $house);
        }

        $rows = $q->get();

        return [
            'categories' => $rows->pluck('year_level')->map(fn ($yl) => (string) (int) $yl)->values()->all(),
            'series' => $rows->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /**
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function pcDrilldownLowEngagement(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $q = $this->pcStudentWithHouseQuery($house)
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('students.year_level', (int) $yearFilter);
            })
            ->leftJoin('point_transactions as pw', function ($join) use ($start, $end) {
                $join->on('students.id', '=', 'pw.student_id');
                $this->pcTransactionsInRangeWeekday($join, $start, $end, 'pw');
            })
            ->selectRaw('TRIM(CONCAT(students.first_name, \' \', students.last_name)) as name')
            ->selectRaw('h.name as house_name')
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'h.name')
            ->havingRaw('COUNT(pw.id) = 0')
            ->orderBy('name');

        $rows = $q->get()->map(fn ($r) => [
            'name' => $r->name,
            'house' => $r->house_name ?? '—',
        ])->all();

        return ['title' => 'Low engagement (no weekday points in range)', 'rows' => $rows];
    }

    /**
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function pcDrilldownRiskHigh(string $house, string $yearFilter = 'All'): array
    {
        $q = $this->pcStudentWithHouseQuery($house)
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('students.year_level', (int) $yearFilter);
            })
            ->selectRaw('TRIM(CONCAT(students.first_name, \' \', students.last_name)) as name')
            ->selectRaw('h.name as house_name')
            ->whereRaw('(SELECT COUNT(*) FROM point_transactions t WHERE t.student_id = students.id) = 0')
            ->orderBy('name');

        $rows = $q->get()->map(fn ($r) => [
            'name' => $r->name,
            'house' => $r->house_name ?? '—',
        ])->all();

        return ['title' => 'High risk (no transactions ever)', 'rows' => $rows];
    }

    /**
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function pcDrilldownRiskMedium(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $q = $this->pcStudentWithHouseQuery($house)
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('students.year_level', (int) $yearFilter);
            })
            ->leftJoin('point_transactions as pw', function ($join) use ($start, $end) {
                $join->on('students.id', '=', 'pw.student_id');
                $this->pcTransactionsInRangeWeekday($join, $start, $end, 'pw');
            })
            ->selectRaw('TRIM(CONCAT(students.first_name, \' \', students.last_name)) as name')
            ->selectRaw('h.name as house_name')
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'h.name')
            ->havingRaw('(SELECT COUNT(*) FROM point_transactions t WHERE t.student_id = students.id) > 0 AND COUNT(pw.id) = 0')
            ->orderBy('name');

        $rows = $q->get()->map(fn ($r) => [
            'name' => $r->name,
            'house' => $r->house_name ?? '—',
        ])->all();

        return ['title' => 'Medium risk (no weekday activity in range)', 'rows' => $rows];
    }

    /**
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function pcDrilldownRiskActive(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $q = $this->pcStudentWithHouseQuery($house)
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('students.year_level', (int) $yearFilter);
            })
            ->join('point_transactions as pw', function ($join) use ($start, $end) {
                $join->on('students.id', '=', 'pw.student_id');
                $this->pcTransactionsInRangeWeekday($join, $start, $end, 'pw');
            })
            ->selectRaw('TRIM(CONCAT(students.first_name, \' \', students.last_name)) as name')
            ->selectRaw('h.name as house_name')
            ->selectRaw('SUM(pw.amount) as points_in_range')
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'h.name')
            ->orderBy('name');

        $rows = $q->get()->map(fn ($r) => [
            'name' => $r->name,
            'house' => $r->house_name ?? '—',
            'points (range)' => (int) $r->points_in_range,
        ])->all();

        return ['title' => 'Active (weekday points in range)', 'rows' => $rows];
    }

    /**
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function pcDrilldownDate(string $dateStr, string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $day = Carbon::createFromFormat('Y-m-d', $dateStr)->startOfDay();

        if ($day->lt($start->copy()->startOfDay()) || $day->gt($end->copy()->endOfDay())) {
            return ['title' => 'Transactions on '.$dateStr, 'rows' => []];
        }

        if ($day->dayOfWeekIso < 1 || $day->dayOfWeekIso > 5) {
            return ['title' => 'Transactions on '.$dateStr.' (not a weekday in range)', 'rows' => []];
        }

        $query = DB::table('point_transactions')
            ->join('students', 'students.id', '=', 'point_transactions.student_id');

        if ($house !== 'All') {
            $query->join('houses', 'houses.id', '=', 'students.house_id')
                ->where('houses.name', $house);
        }

        if ($start && $end) {
            $query->whereBetween('point_transactions.created_at', [$start, $end]);
        }

        $query->whereRaw('EXTRACT(DOW FROM point_transactions.created_at::timestamp) BETWEEN 1 AND 5')
            ->whereDate('point_transactions.created_at', $dateStr);

        if ($yearFilter !== 'All') {
            $query->where('students.year_level', (int) $yearFilter);
        }

        $rows = $query
            ->select(
                'students.first_name',
                'students.last_name',
                'students.year_level',
                'point_transactions.amount',
                'point_transactions.created_at'
            )
            ->orderByDesc('point_transactions.created_at')
            ->limit(300)
            ->get();

        return [
            'title' => 'Transactions on '.$dateStr,
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function pcDrilldownHouseBar(string $houseName, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $q = DB::table('point_transactions as pt')
            ->join('students as s', 'pt.student_id', '=', 's.id')
            ->join('houses as h', 's.house_id', '=', 'h.id')
            ->where('h.name', $houseName)
            ->when($yearFilter !== 'All', function ($q) use ($yearFilter) {
                $q->where('s.year_level', (int) $yearFilter);
            })
            ->whereBetween('pt.created_at', [$start, $end])
            ->whereRaw('EXTRACT(DOW FROM pt.created_at::timestamp) BETWEEN 1 AND 5')
            ->selectRaw('TRIM(CONCAT(s.first_name, \' \', s.last_name)) as name')
            ->selectRaw('SUM(pt.amount) as total')
            ->groupBy('s.id', 's.first_name', 's.last_name')
            ->orderByDesc('total');

        $rows = $q->get()->map(fn ($r) => [
            'name' => $r->name,
            'points (weekdays)' => (int) $r->total,
        ])->all();

        return ['title' => 'Students in '.$houseName.' (weekday points)', 'rows' => $rows];
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

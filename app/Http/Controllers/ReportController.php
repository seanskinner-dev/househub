<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function atRiskStudents(Request $request)
    {
        $houses = DB::table('houses')->orderBy('name')->pluck('name')->values()->all();
        $data = $this->getReportData($request);

        return view('reports.pc', [
            'houses' => $houses,
            'data' => $data,
        ]);
    }

    public function leadership()
    {
        $houses = DB::table('houses')->orderBy('name')->get();

        return view('reports.leadership', compact('houses'));
    }

    public function teachers()
    {
        $houses = DB::table('houses')->orderBy('name')->pluck('name')->values()->all();

        return view('reports.teachers', compact('houses'));
    }

    public function houses()
    {
        return view('reports.houses');
    }

    public function reportChartData(Request $request)
    {
        return response()->json($this->getReportData($request));
    }

    private function getReportData(Request $request): array
    {
        [$start, $end, $house, $yearFilter] = $this->pcParseFilters($request);

        $legacyDonut = $this->pcChartDonut($house, $start, $end, $yearFilter);
        $legacyTrend = $this->pcChartTrend($house, $start, $end, $yearFilter);
        $legacyHouse = $this->pcChartHousePoints($house, $start, $end, $yearFilter);
        $legacyYearLevel = $this->pcChartYearLevel($house, $start, $end, $yearFilter);
        $recent = $this->pcTeacherRecentRows($house, $start, $end, $yearFilter);

        $high = (int) ($legacyDonut['series'][0] ?? 0);
        $medium = (int) ($legacyDonut['series'][1] ?? 0);
        $low = (int) ($legacyDonut['series'][2] ?? 0);

        $buildDataset = function (string $type, array $categories, array $data, string $name): array {
            $categories = array_values($categories);
            $data = array_values(array_map('intval', $data));

            if (count($categories) !== count($data)) {
                Log::error('Chart data mismatch', [
                    'categories' => count($categories),
                    'data' => count($data),
                ]);
            }

            if ($categories === [] || $data === []) {
                return [
                    'type' => $type,
                    'categories' => [],
                    'series' => [[
                        'name' => 'Empty',
                        'data' => [],
                    ]],
                ];
            }

            return [
                'type' => $type,
                'categories' => $categories,
                'series' => [[
                    'name' => $name,
                    'data' => $data,
                ]],
            ];
        };

        $riskDistribution = $buildDataset('breakdown', ['Low', 'Medium', 'High'], [$low, $medium, $high], 'Students');
        $pointsByHouse = $buildDataset(
            'breakdown',
            (array) ($legacyHouse['categories'] ?? []),
            (array) ($legacyHouse['series'] ?? []),
            'Points'
        );
        $engagementTrend = $buildDataset(
            'trend',
            (array) ($legacyTrend['categories'] ?? []),
            (array) ($legacyTrend['series'] ?? []),
            'Engagement'
        );

        $yearLevelRows = DB::table('students as s')
            ->leftJoin('houses as h', 's.house_id', '=', 'h.id')
            ->when($house !== 'All', fn ($q) => $q->where('h.name', $house))
            ->when($yearFilter !== 'All', fn ($q) => $q->where('s.year_level', (int) $yearFilter))
            ->whereNotNull('s.year_level')
            ->selectRaw('s.year_level as year_level, COUNT(*) as total_students')
            ->groupBy('s.year_level')
            ->orderBy('s.year_level')
            ->get();

        $yearLevelDistribution = $buildDataset(
            'breakdown',
            $yearLevelRows->pluck('year_level')->map(fn ($yl) => 'Year '.(int) $yl)->values()->all(),
            $yearLevelRows->pluck('total_students')->values()->all(),
            'Students'
        );

        return [
            'risk_distribution' => $riskDistribution,
            'points_by_house' => $pointsByHouse,
            'engagement_trend' => $engagementTrend,
            'year_level_distribution' => $yearLevelDistribution,

            // Legacy keys kept for existing report pages.
            'donut' => $legacyDonut,
            'trend' => $legacyTrend,
            'house_breakdown' => $legacyHouse,
            'year_level' => $legacyYearLevel,
            'recent' => $recent,
        ];
    }

    public function reportDrilldown(Request $request)
    {
        [$start, $end, $house, $yearFilter] = $this->pcParseFilters($request);

        if ($request->isMethod('post')) {
            return $this->reportDrilldownStructured($request->all(), $house, $start, $end, $yearFilter);
        }

        $teacher = trim((string) $request->query('teacher', ''));
        if ($teacher !== '') {
            return response()->json($this->drilldownTeacherStudents($teacher, $house, $start, $end, $yearFilter));
        }

        $label = trim((string) $request->query('label', ''));

        if ($label === '') {
            return response()->json(['title' => 'Drill-down', 'rows' => []]);
        }

        if ($label === 'LOW_USAGE') {
            return response()->json($this->tuDrilldownLowUsageTeachers($house, $start, $end, $yearFilter));
        }

        if (preg_match('/^__TU_DATE__(\d{4}-\d{2}-\d{2})$/', $label, $m)) {
            return response()->json($this->tuDrilldownDateStaff($m[1], $house, $start, $end, $yearFilter));
        }

        if (preg_match('/^__TU_T__(.+)$/s', $label, $m)) {
            return response()->json($this->tuDrilldownTeacherTransactions($m[1], $house, $start, $end, $yearFilter));
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $label)) {
            return response()->json($this->pcDrilldownDate($label, $house, $start, $end, $yearFilter));
        }

        if ($label === 'Active') {
            return response()->json($this->pcDrilldownRiskActive($house, $start, $end, $yearFilter));
        }

        if ($label === 'Low') {
            return response()->json($this->pcDrilldownLowAtRisk($house, $start, $end, $yearFilter));
        }

        // ===== ENGAGEMENT DRILLDOWN (weekday in-range row counts; Medium / High only)
        $activityBucket = match (true) {
            $label === 'Medium' || $label === 'Medium Risk' => 'Medium',
            $label === 'High' || $label === 'High Risk' => 'High',
            default => null,
        };
        if ($activityBucket !== null) {
            return response()->json($this->pcDrilldownEngagementActivity($activityBucket, $house, $start, $end, $yearFilter));
        }

        if (preg_match('/^Year\s+(\d+)$/', $label, $matches)) {
            return response()->json($this->pcDrilldownYearLevelBar((int) $matches[1], $house, $start, $end, $yearFilter));
        }

        if (DB::table('houses')->where('name', $label)->exists()) {
            return response()->json($this->pcDrilldownHouseBar($label, $start, $end, $yearFilter));
        }

        return response()->json(['title' => $label, 'rows' => []]);
    }

    private function reportDrilldownStructured(array $data, string $house, Carbon $start, Carbon $end, string $yearFilter)
    {
        $type = $data['type'] ?? null;
        if ($type === null || $type === '') {
            return response()->json(['rows' => []]);
        }

        return match ($type) {
            'date' => response()->json($this->drilldownPayloadDate($data, $house, $start, $end, $yearFilter)),
            'engagement_low' => response()->json($this->pcDrilldownLowAtRisk($house, $start, $end, $yearFilter)),
            'engagement_active' => response()->json($this->pcDrilldownRiskActive($house, $start, $end, $yearFilter)),
            'house_low' => response()->json($this->drilldownHouseLow((string) ($data['value'] ?? ''), $house, $start, $end, $yearFilter)),
            'teacher' => response()->json($this->drilldownTeacherStudents((string) ($data['value'] ?? ''), $house, $start, $end, $yearFilter)),
            'teacher_bucket' => response()->json($this->tuDrilldownTeacherBucketList(
                is_array($data['value'] ?? null) ? $data['value'] : [],
                $house,
                $start,
                $end,
                $yearFilter
            )),
            'year_level' => response()->json($this->drilldownPayloadYearLevel($data, $house, $start, $end, $yearFilter)),
            'risk_segment' => response()->json($this->drilldownPayloadRiskSegment($data, $house, $start, $end, $yearFilter)),
            default => response()->json(['rows' => []]),
        };
    }

    private function drilldownPayloadDate(array $data, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $value = (string) ($data['value'] ?? '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return ['title' => 'Drill-down', 'rows' => []];
        }
        if (($data['scope'] ?? '') === 'staff') {
            return $this->tuDrilldownDateStaff($value, $house, $start, $end, $yearFilter);
        }

        return $this->pcDrilldownDate($value, $house, $start, $end, $yearFilter);
    }

    private function drilldownPayloadYearLevel(array $data, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $raw = trim((string) ($data['value'] ?? ''));
        if (preg_match('/^Year\s+(\d+)$/', $raw, $m)) {
            return $this->drilldownYearLevelStudents((int) $m[1], $house, $start, $end, $yearFilter);
        }
        if (preg_match('/^\d+$/', $raw)) {
            return $this->drilldownYearLevelStudents((int) $raw, $house, $start, $end, $yearFilter);
        }

        return ['title' => 'Drill-down', 'rows' => []];
    }

    /**
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>}
     */
    private function drilldownYearLevelStudents(int $year, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $rows = DB::table('students as s')
            ->leftJoin('houses as h', 's.house_id', '=', 'h.id')
            ->leftJoin('point_transactions as pt', function ($join) use ($start, $end) {
                $join->on('pt.student_id', '=', 's.id')
                    ->whereBetween('pt.created_at', [$start, $end]);
            })
            ->where('s.year_level', $year)
            ->when($house !== 'All', fn ($q) => $q->where('h.name', $house))
            ->when($yearFilter !== 'All', fn ($q) => $q->where('s.year_level', (int) $yearFilter))
            ->groupBy('s.id', 's.first_name', 's.last_name', 's.year_level', 'h.name')
            ->select(
                's.first_name',
                's.last_name',
                's.year_level',
                DB::raw('COALESCE(h.name, \'Unknown\') as house_name'),
                DB::raw('COALESCE(SUM(pt.amount), 0) as house_points')
            )
            ->orderBy('s.last_name')
            ->orderBy('s.first_name')
            ->get();

        return [
            'title' => "Students in Year {$year}",
            'rows' => $rows,
        ];
    }

    private function drilldownPayloadRiskSegment(array $data, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $v = trim((string) ($data['value'] ?? ''));
        if ($v === 'High Risk' || $v === 'High') {
            return $this->pcDrilldownEngagementActivity('High', $house, $start, $end, $yearFilter);
        }
        if ($v === 'Medium Risk' || $v === 'Medium') {
            return $this->pcDrilldownEngagementActivity('Medium', $house, $start, $end, $yearFilter);
        }
        if ($v === 'Active') {
            return $this->pcDrilldownRiskActive($house, $start, $end, $yearFilter);
        }
        if ($v === 'Low' || $v === 'Low Risk') {
            return $this->pcDrilldownLowAtRisk($house, $start, $end, $yearFilter);
        }

        return ['title' => $v !== '' ? $v : 'Drill-down', 'rows' => []];
    }

    /**
     * @return array{title: string, student_breakdown: list<object>, debug?: list<object>}
     */
    private function drilldownTeacherStudents(string $teacher, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $query = DB::table('point_transactions')
            ->join('students', 'students.id', '=', 'point_transactions.student_id');

        if ($house !== 'All') {
            $query->join('houses', 'houses.id', '=', 'students.house_id')
                ->where('houses.name', $house);
        }

        if ($start && $end) {
            $query->whereBetween('point_transactions.created_at', [$start, $end]);
        }

        if ($yearFilter !== 'All') {
            $query->where('students.year_level', (int) $yearFilter);
        }

        if ($teacher === 'Unknown') {
            $query->leftJoin('users', 'users.id', '=', 'point_transactions.awarded_by')
                ->where(function ($q) {
                    $q->whereNull('point_transactions.awarded_by')
                        ->orWhereNull('users.id')
                        ->orWhereRaw("NULLIF(TRIM(users.name), '') IS NULL");
                });
        } else {
            $query->join('users', 'users.id', '=', 'point_transactions.awarded_by')
                ->where('users.name', $teacher);
        }

        $rows = $query
            ->select(
                'students.first_name',
                'students.last_name',
                DB::raw('SUM(point_transactions.amount) as total_points')
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name')
            ->orderByRaw('SUM(point_transactions.amount) ASC')
            ->limit(15)
            ->get();

        $payload = [
            'title' => 'Students receiving points from '.$teacher.' (lowest totals first, up to 15)',
            'student_breakdown' => $rows->values()->all(),
        ];

        if (config('app.debug')) {
            $payload['debug'] = $payload['student_breakdown'];
        }

        return $payload;
    }

    /**
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>|array}
     */
    private function drilldownHouseLow(string $houseName, string $filterHouse, Carbon $start, Carbon $end, string $yearFilter): array
    {
        if ($houseName === '' || ! DB::table('houses')->where('name', $houseName)->exists()) {
            return ['title' => 'Low activity by house', 'rows' => []];
        }

        if ($filterHouse !== 'All' && $filterHouse !== $houseName) {
            return ['title' => 'Low activity by house', 'rows' => []];
        }

        $rows = DB::table('students as s')
            ->join('houses as h', 's.house_id', '=', 'h.id')
            ->leftJoin('point_transactions as pt', function ($join) use ($start, $end) {
                $join->on('pt.student_id', '=', 's.id')
                    ->whereBetween('pt.created_at', [$start, $end])
                    ->whereRaw('EXTRACT(DOW FROM pt.created_at::timestamp) BETWEEN 1 AND 5');
            })
            ->where('h.name', $houseName)
            ->when($yearFilter !== 'All', fn ($q) => $q->where('s.year_level', (int) $yearFilter))
            ->select('s.first_name', 's.last_name', 's.year_level', DB::raw('COUNT(pt.id) as weekday_awards'))
            ->groupBy('s.id', 's.first_name', 's.last_name', 's.year_level')
            ->havingRaw('COUNT(pt.id) <= 5')
            ->orderByRaw('COUNT(pt.id) ASC')
            ->get();

        return [
            'title' => 'Lowest weekday award counts in '.$houseName.' (≤5 in range)',
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, mixed>}
     */
    private function pcDrilldownYearLevelBar(int $year, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
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
            ->orderBy('point_transactions.amount', 'asc')
            ->orderByDesc('point_transactions.created_at')
            ->limit(100)
            ->get();

        return [
            'title' => 'Year '.$year.' transactions (lowest point amounts first)',
            'rows' => $rows,
        ];
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
     * At-risk students: zero weekday point_transaction rows in the filter range.
     *
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>}
     */
    private function pcDrilldownLowAtRisk(string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $query = DB::table('students')
            ->leftJoin('point_transactions as pt', function ($join) use ($start, $end) {
                $join->on('students.id', '=', 'pt.student_id')
                    ->whereBetween('pt.created_at', [$start, $end])
                    ->whereRaw('EXTRACT(DOW FROM pt.created_at::timestamp) BETWEEN 1 AND 5');
            });

        if ($house !== 'All') {
            $query->join('houses', 'houses.id', '=', 'students.house_id')
                ->where('houses.name', $house);
        }

        if ($yearFilter !== 'All') {
            $query->where('students.year_level', (int) $yearFilter);
        }

        $rows = $query
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.year_level')
            ->select(
                'students.first_name',
                'students.last_name',
                'students.year_level',
                DB::raw('COUNT(pt.id) as activity_count')
            )
            ->havingRaw('COUNT(pt.id) = 0')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->get();

        return [
            'title' => 'At risk (no weekday points in range)',
            'rows' => $rows,
        ];
    }

    /**
     * Weekday in-range point_transaction row counts per student — Medium / High buckets only.
     *
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>}
     */
    private function pcDrilldownEngagementActivity(string $bucket, string $house, Carbon $start, Carbon $end, string $yearFilter = 'All'): array
    {
        $query = DB::table('students')
            ->leftJoin('point_transactions as pt', function ($join) use ($start, $end) {
                $join->on('students.id', '=', 'pt.student_id')
                    ->whereBetween('pt.created_at', [$start, $end])
                    ->whereRaw('EXTRACT(DOW FROM pt.created_at::timestamp) BETWEEN 1 AND 5');
            });

        if ($house !== 'All') {
            $query->join('houses', 'houses.id', '=', 'students.house_id')
                ->where('houses.name', $house);
        }

        if ($yearFilter !== 'All') {
            $query->where('students.year_level', (int) $yearFilter);
        }

        $query->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.year_level')
            ->select(
                'students.first_name',
                'students.last_name',
                'students.year_level',
                DB::raw('COUNT(pt.id) as activity_count')
            )
            ->orderBy('students.last_name')
            ->orderBy('students.first_name');

        if ($bucket === 'Medium') {
            $query->havingRaw('COUNT(pt.id) BETWEEN 1 AND 5');
        } else {
            $query->havingRaw('COUNT(pt.id) > 5');
        }

        $rows = $query->get();

        $title = $bucket === 'Medium'
            ? 'Medium activity (1–5 weekday point rows in range)'
            : 'High activity (>5 weekday point rows in range)';

        return ['title' => $title, 'rows' => $rows];
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

    /**
     * Recent activity rows for charts; includes teacher (awarding user) per transaction.
     *
     * @return list<object>
     */
    private function pcTeacherRecentRows(string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $q = $this->tuPointTransactionsBase($house, $start, $end, $yearFilter);

        return $q
            ->select(
                's.first_name',
                's.last_name',
                'pt.amount',
                'pt.created_at',
                'u.name as teacher'
            )
            ->orderByDesc('pt.created_at')
            ->limit(10000)
            ->get()
            ->all();
    }

    private function tuPointTransactionsBase(string $house, Carbon $start, Carbon $end, string $yearFilter)
    {
        $q = DB::table('point_transactions as pt')
            ->leftJoin('users as u', 'pt.awarded_by', '=', 'u.id')
            ->leftJoin('students as s', 'pt.student_id', '=', 's.id')
            ->whereBetween('pt.created_at', [$start, $end]);

        if ($house !== 'All') {
            $q->join('houses as h', 's.house_id', '=', 'h.id')
                ->where('h.name', $house);
        }

        if ($yearFilter !== 'All') {
            $q->where('s.year_level', (int) $yearFilter);
        }

        return $q;
    }

    /**
     * Teachers in a frequency bucket (names from the client chart), with award counts in the filtered range.
     * One row per bucket name (same display label as /reports/data recent), merged so none are omitted.
     *
     * @param  list<mixed>  $teacherNames
     * @return array{title: string, rows: list<array<string, mixed>>}
     */
    private function tuDrilldownTeacherBucketList(array $teacherNames, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $teacherNames = array_values(array_unique(array_filter(array_map('strval', $teacherNames), fn ($t) => $t !== '')));
        if (count($teacherNames) > 500) {
            $teacherNames = array_slice($teacherNames, 0, 500);
        }

        if ($teacherNames === []) {
            return [
                'title' => 'Teachers in selected range',
                'rows' => [],
            ];
        }

        $knownTeachers = array_values(array_filter($teacherNames, fn ($t) => $t !== 'Unknown'));
        $includeUnknown = in_array('Unknown', $teacherNames, true);

        $teacherExpr = "COALESCE(NULLIF(TRIM(u.name), ''), 'Unknown')";

        $q = $this->tuPointTransactionsBase($house, $start, $end, $yearFilter);

        $q->where(function ($w) use ($knownTeachers, $includeUnknown) {
            if ($knownTeachers !== [] && $includeUnknown) {
                $w->whereIn('u.name', $knownTeachers)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('pt.awarded_by')
                            ->orWhereNull('u.id')
                            ->orWhereRaw("NULLIF(TRIM(u.name), '') IS NULL");
                    });
            } elseif ($knownTeachers !== []) {
                $w->whereIn('u.name', $knownTeachers);
            } elseif ($includeUnknown) {
                $w->where(function ($q2) {
                    $q2->whereNull('pt.awarded_by')
                        ->orWhereNull('u.id')
                        ->orWhereRaw("NULLIF(TRIM(u.name), '') IS NULL");
                });
            } else {
                $w->whereRaw('1 = 0');
            }
        });

        $aggregated = $q
            ->selectRaw("{$teacherExpr} as teacher")
            ->selectRaw('COUNT(pt.id) as total_actions')
            ->groupBy(DB::raw($teacherExpr))
            ->orderBy('teacher')
            ->get();

        $counts = [];
        foreach ($aggregated as $r) {
            $counts[$r->teacher] = (int) $r->total_actions;
        }

        $rows = [];
        foreach ($teacherNames as $name) {
            $rows[] = [
                'teacher' => $name,
                'total_actions' => $counts[$name] ?? 0,
            ];
        }

        return [
            'title' => 'Teachers in selected range',
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>}
     */
    private function tuDrilldownLowUsageTeachers(string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $rows = $this->tuPointTransactionsBase($house, $start, $end, $yearFilter)
            ->selectRaw("COALESCE(NULLIF(TRIM(u.name), ''), 'Unknown') as teacher")
            ->selectRaw('COUNT(pt.id) as usage_count')
            ->groupBy('pt.awarded_by', 'u.id', 'u.name')
            ->havingRaw('COUNT(pt.id) <= 5')
            ->orderBy('usage_count')
            ->orderBy('teacher')
            ->get();

        return [
            'title' => 'Low-usage staff (≤5 awards in range)',
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>}
     */
    private function tuDrilldownTeacherTransactions(string $teacher, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $rows = $this->tuPointTransactionsBase($house, $start, $end, $yearFilter)
            ->whereRaw("COALESCE(NULLIF(TRIM(u.name), ''), 'Unknown') = ?", [$teacher])
            ->select(
                'pt.amount',
                'pt.category',
                'pt.created_at'
            )
            ->orderByDesc('pt.created_at')
            ->limit(500)
            ->get();

        return [
            'title' => 'Staff awards: '.$teacher,
            'rows' => $rows,
        ];
    }

    /**
     * @return array{title: string, rows: \Illuminate\Support\Collection<int, object>}
     */
    private function tuDrilldownDateStaff(string $dateStr, string $house, Carbon $start, Carbon $end, string $yearFilter): array
    {
        $day = Carbon::createFromFormat('Y-m-d', $dateStr)->startOfDay();

        if ($day->lt($start->copy()->startOfDay()) || $day->gt($end->copy()->endOfDay())) {
            return ['title' => 'Staff activity on '.$dateStr, 'rows' => []];
        }

        $rows = $this->tuPointTransactionsBase($house, $start, $end, $yearFilter)
            ->whereDate('pt.created_at', $dateStr)
            ->selectRaw("COALESCE(NULLIF(TRIM(u.name), ''), 'Unknown') as teacher")
            ->select('pt.amount', 'pt.category', 'pt.created_at')
            ->orderByDesc('pt.created_at')
            ->limit(500)
            ->get();

        return [
            'title' => 'Staff activity on '.$dateStr,
            'rows' => $rows,
        ];
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

<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Commendation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PointController extends Controller
{
    public function index()
    {
        // 🔥 ONLY CHANGE: JOIN houses to get house_name
        $students = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.*',
                'houses.name as house_name'
            )
            ->orderBy('students.id')
            ->get();

        $recent = DB::table('point_transactions')
            ->leftJoin('students', 'point_transactions.student_id', '=', 'students.id')
            ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select(
                'students.first_name',
                'students.last_name',
                'houses.name as house_name',
                'point_transactions.amount',
                'point_transactions.category',
                'point_transactions.created_at',
                DB::raw("CASE WHEN users.email = 'system@househub.local' THEN 'System' ELSE COALESCE(users.name, 'System') END as teacher")
            )
            ->orderByDesc('point_transactions.created_at')
            ->limit(6)
            ->get();

        $houses = DB::table('houses')->get();

        return view('points.index', compact('students','recent','houses'));
    }

    public function store(Request $request)
    {
        // 🔧 FIX: default amount
        $amount = (int) $request->input('amount', 1);

        $systemUser = User::firstOrCreate(
            ['email' => 'system@househub.local'],
            [
                'name' => 'System',
                'password' => bcrypt('notused123')
            ]
        );
        $userId = auth()->check()
            ? (int) auth()->id()
            : (int) $systemUser->id;
        $teacherLabel = auth()->check()
            ? (string) auth()->user()->name
            : 'System';

        return DB::transaction(function () use ($request, $amount, $userId, $teacherLabel) {

            $student = null;
            $house = null;
            $recentEntry = null;
            $newPoints = null;
            $newHousePoints = null;

            $resolveHouseFromRequest = function () use ($request) {
                if ($request->filled('house_id')) {
                    return DB::table('houses')->where('id', (int) $request->input('house_id'))->first();
                }
                if ($request->filled('house_name')) {
                    return DB::table('houses')->where('name', $request->house_name)->first();
                }

                return null;
            };

            $house = $resolveHouseFromRequest();

            if ($house && ! $request->filled('student_id')) {

                DB::table('houses')
                    ->where('id', $house->id)
                    ->increment('points', $amount);

                $newHousePoints = (int) (DB::table('houses')
                    ->where('id', $house->id)
                    ->value('points') ?? 0);

                DB::table('point_transactions')->insert([
                    'student_id' => null,
                    'house_id' => $house->id,
                    'amount' => $amount,
                    'category' => 'manual',
                    'description' => 'House points awarded',
                    'awarded_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $recentEntry = [
                    'amount' => $amount,
                    'who' => $house->name,
                    'category' => 'manual',
                    'teacher' => $teacherLabel,
                ];
            }

            $manualMode = (string) $request->input('manual_mode', '');
            $isHouseOnlyManual = strtolower(trim($manualMode)) === 'house_only';

            if ($request->filled('student_id') && ! $isHouseOnlyManual) {

                $student = DB::table('students')
                    ->where('id', $request->student_id)
                    ->first();

                if ($student) {

                    DB::table('students')
                        ->where('id', $student->id)
                        ->increment('house_points', $amount);

                    $newPoints = (int) (DB::table('students')
                        ->where('id', $student->id)
                        ->value('house_points') ?? 0);

                    $house = null;
                    if (! empty($student->house_id)) {
                        $house = DB::table('houses')->where('id', $student->house_id)->first();
                    }
                    if (! $house && ! empty($student->house_name)) {
                        $house = DB::table('houses')->where('name', $student->house_name)->first();
                    }

                    if ($house) {
                        DB::table('houses')
                            ->where('id', $house->id)
                            ->increment('points', $amount);

                        $newHousePoints = (int) (DB::table('houses')
                            ->where('id', $house->id)
                            ->value('points') ?? 0);
                    }

                    DB::table('point_transactions')->insert([
                        'student_id' => $student->id,
                        'house_id' => $house->id ?? null,
                        'amount' => $amount,
                        // 🔧 FIX: use type instead of category
                        'category' => $request->input('type', 'manual'),
                        'description' => $request->input('description', ''),
                        'awarded_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $recentEntry = [
                        'amount' => $amount,
                        'who' => trim(($student->first_name ?? '').' '.($student->last_name ?? '')),
                        'category' => $request->input('type', 'manual'),
                        'teacher' => $teacherLabel,
                    ];
                }
            }

            // 🔧 FIX: ALWAYS return valid response
            return response()->json([
                'success' => true,
                'amount' => $amount,
                'student' => $student ? $student->first_name.' '.$student->last_name : null,
                'house' => $house ? $house->name : null,
                'teacher' => $teacherLabel,
                'points' => $newPoints,
                'house_points' => $newHousePoints,
                'recent_entry' => $recentEntry,
            ]);
        });
    }

    public function storeCommendation(Request $request)
    {
        $systemUser = User::firstOrCreate(
            ['email' => 'system@househub.local'],
            [
                'name' => 'System',
                'password' => bcrypt('notused123')
            ]
        );

        $userId = auth()->check()
            ? auth()->id()
            : $systemUser->id;

        $data = validator($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'description' => 'nullable|string|max:5000',
        ])->validate();

        $teacherName = auth()->check() ? auth()->user()->name : 'System';

        $student = DB::table('students')->where('id', $data['student_id'])->first();
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $houseId = $student->house_id ?? null;

        DB::transaction(function () use ($data, $userId, $student, $houseId) {
            $description = trim((string) ($data['description'] ?? ''));
            if ($description === '') {
                $description = 'Commendation';
            }

            Commendation::create([
                'student_id' => $student->id,
                'awarded_by' => $userId,
            ]);

            DB::table('point_transactions')->insert([
                'student_id' => $student->id,
                'house_id' => $houseId,
                'amount' => 0,
                'category' => 'commendation',
                'description' => $description,
                'awarded_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $who = trim(($student->first_name ?? '').' '.($student->last_name ?? ''));
        $total = Commendation::query()
            ->where('student_id', $student->id)
            ->count();

        return response()->json([
            'success' => true,
            'total' => $total,
            'recent_entry' => [
                'amount' => 0,
                'who' => $who,
                'category' => 'commendation',
                'teacher' => $teacherName,
            ],
        ]);
    }

    public function storeAward(Request $request)
    {
        $systemUser = User::firstOrCreate(
            ['email' => 'system@househub.local'],
            [
                'name' => 'System',
                'password' => bcrypt('notused123')
            ]
        );

        $userId = auth()->check()
            ? auth()->id()
            : $systemUser->id;

        $data = validator($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'award_name' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ])->validate();

        $teacherName = auth()->check() ? auth()->user()->name : 'System';

        $student = DB::table('students')->where('id', $data['student_id'])->first();
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $houseId = $student->house_id ?? null;

        DB::transaction(function () use ($data, $userId, $student, $houseId) {
            Award::create([
                'student_id' => $student->id,
                'awarded_by' => $userId,
                'name' => $data['award_name'],
                'description' => $data['description'],
            ]);

            DB::table('point_transactions')->insert([
                'student_id' => $student->id,
                'house_id' => $houseId,
                'amount' => 0,
                'category' => 'award',
                'description' => $data['award_name'].': '.$data['description'],
                'awarded_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $who = trim(($student->first_name ?? '').' '.($student->last_name ?? ''));

        return response()->json([
            'success' => true,
            'recent_entry' => [
                'amount' => 0,
                'who' => $who,
                'category' => 'award: '.$data['award_name'],
                'teacher' => $teacherName,
            ],
        ]);
    }

    public function showStudent($id)
    {
        $student = DB::table('students')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select('students.*', 'houses.name as house_name')
            ->where('students.id', $id)
            ->first();

        if (!$student) abort(404);

        $awards = DB::table('awards')
            ->leftJoin('users', 'awards.awarded_by', '=', 'users.id')
            ->select('awards.*', 'users.name as teacher_name')
            ->where('awards.student_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $pointTransactions = DB::table('point_transactions')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('point_transactions.*', 'users.name as teacher_name')
            ->where('point_transactions.student_id', $id)
            ->where('point_transactions.amount', '!=', 0)
            ->orderByDesc('point_transactions.created_at')
            ->get();

        $commendations = DB::table('point_transactions')
            ->leftJoin('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select('point_transactions.*', 'users.name as teacher_name')
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

    public function certificate($id)
    {
        $award = DB::table('awards')
            ->leftJoin('students', 'awards.student_id', '=', 'students.id')
            ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'awards.*',
                'students.first_name',
                'students.last_name',
                'houses.name as house_name'
            )
            ->where('awards.id', $id)
            ->first();

        if (!$award) abort(404);

        return view('certificates.show', compact('award'));
    }

    public function tv()
    {
        $raw = DB::table('point_transactions')
            ->selectRaw('DATE(point_transactions.created_at) as date, houses.name as house, SUM(point_transactions.amount) as total')
            ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->where('point_transactions.created_at', '>=', now()->subDays(7))
            ->groupByRaw('DATE(point_transactions.created_at), houses.name')
            ->orderBy('date')
            ->get();

        $dates = $raw->pluck('date')->unique()->values();

        $houseMap = [
            'Gryffindor' => '#740001',
            'Slytherin'  => '#1a472a',
            'Ravenclaw'  => '#3b82f6',
            'Hufflepuff' => '#ffcc00',
        ];

        $series = [];

        foreach ($houseMap as $house => $colour) {
            $series[$house] = array_fill(0, count($dates), 0);
        }

        foreach ($raw as $row) {
            $formattedDate = Carbon::parse($row->date)->format('Y-m-d');
            $index = $dates->search($formattedDate);

            if ($index !== false && isset($series[$row->house])) {
                $series[$row->house][$index] = (int) $row->total;
            }
        }

        $apexSeries = [];

        foreach ($houseMap as $house => $colour) {
            $apexSeries[] = [
                'name' => $house,
                'data' => $series[$house],
                'color' => $colour,
            ];
        }

        $labels = $dates->map(function ($d) {
            return Carbon::parse($d)->format('D');
        });

        $topStudents = DB::table('students')
            ->join('houses', 'students.house_id', '=', 'houses.id')
            ->select(
                'students.id',
                'students.first_name',
                'students.last_name',
                'students.house_points',
                'houses.name as house_name'
            )
            ->orderByDesc('students.house_points')
            ->limit(10)
            ->get();

        $topTeachers = DB::table('point_transactions')
            ->join('users', 'point_transactions.awarded_by', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->where('point_transactions.created_at', '>=', now()->subDays(7))
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $termTotals = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                'houses.name as house_name',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->where('point_transactions.created_at', '>=', now()->subWeeks(12))
            ->groupBy('houses.name')
            ->get();

        $annualTotals = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                'houses.name as house_name',
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->where('point_transactions.created_at', '>=', now()->subYear())
            ->groupBy('houses.name')
            ->get();

        $recent = DB::table('point_transactions')
            ->leftJoin('students', 'point_transactions.student_id', '=', 'students.id')
            ->leftJoin('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                DB::raw("TRIM(COALESCE(students.first_name, '') || ' ' || COALESCE(students.last_name, '')) as student_name"),
                'houses.name as house_name',
                DB::raw("COALESCE(point_transactions.category, 'Points awarded') as description"),
                'point_transactions.amount'
            )
            ->orderByDesc('point_transactions.created_at')
            ->limit(10)
            ->get();

        $termCaseSql = 'CASE '
            . 'WHEN EXTRACT(MONTH FROM point_transactions.created_at)::integer BETWEEN 1 AND 3 THEN 1 '
            . 'WHEN EXTRACT(MONTH FROM point_transactions.created_at)::integer BETWEEN 4 AND 6 THEN 2 '
            . 'WHEN EXTRACT(MONTH FROM point_transactions.created_at)::integer BETWEEN 7 AND 9 THEN 3 '
            . 'ELSE 4 END';

        $termBreakdownTotals = DB::table('point_transactions')
            ->join('houses', 'point_transactions.house_id', '=', 'houses.id')
            ->select(
                'houses.name as house',
                DB::raw("({$termCaseSql}) as term"),
                DB::raw('SUM(point_transactions.amount) as total')
            )
            ->groupByRaw("houses.name, {$termCaseSql}")
            ->get();

        $houseOrder = ['Gryffindor', 'Slytherin', 'Ravenclaw', 'Hufflepuff'];
        $housePointsByTerm = [];
        foreach ($houseOrder as $houseName) {
            $housePointsByTerm[] = [
                'house' => $houseName,
                'data' => [0, 0, 0, 0],
            ];
        }

        foreach ($termBreakdownTotals as $row) {
            $idx = array_search($row->house, $houseOrder, true);
            $term = (int) $row->term;
            if ($idx !== false && $term >= 1 && $term <= 4) {
                $housePointsByTerm[$idx]['data'][$term - 1] = (int) $row->total;
            }
        }

        $now = now();
        $yearStart = Carbon::create((int) $now->year, 1, 1)->startOfDay();
        $termStartMonth = (int) (floor((((int) $now->month) - 1) / 3) * 3) + 1;
        $termStart = Carbon::create((int) $now->year, $termStartMonth, 1)->startOfDay();
        $termEnd = (clone $termStart)->addMonths(3)->subSecond();
        $houses = DB::table('houses')
            ->select('id', 'name', 'colour_hex')
            ->orderBy('name')
            ->get();

        $thisYearRows = DB::table('houses')
            ->leftJoin('point_transactions', function ($join) use ($yearStart) {
                $join->on('houses.id', '=', 'point_transactions.house_id')
                    ->where('point_transactions.created_at', '>=', $yearStart);
            })
            ->select(
                'houses.name as house',
                'houses.colour_hex',
                DB::raw('COALESCE(SUM(point_transactions.amount), 0) as total')
            )
            ->groupBy('houses.id', 'houses.name', 'houses.colour_hex')
            ->orderByDesc('total')
            ->get();

        $housePointsThisYear = $thisYearRows->map(function ($row) use ($houseMap) {
            $hex = $row->colour_hex ?: ($houseMap[$row->house] ?? '#334155');

            return [
                'house' => $row->house,
                'total' => (int) $row->total,
                'colour_hex' => $hex,
            ];
        })->values()->all();
        $houseTotalsLookup = collect($housePointsThisYear)->keyBy('house');
        $gryffindorPoints = (int) (($houseTotalsLookup->get('Gryffindor')['total'] ?? 0));
        $slytherinPoints = (int) (($houseTotalsLookup->get('Slytherin')['total'] ?? 0));
        $ravenclawPoints = (int) (($houseTotalsLookup->get('Ravenclaw')['total'] ?? 0));
        $hufflepuffPoints = (int) (($houseTotalsLookup->get('Hufflepuff')['total'] ?? 0));

        $houseTotalsYear = DB::table('point_transactions')
            ->select('house_id', DB::raw('SUM(amount) as total'))
            ->whereYear('created_at', (int) $now->year)
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $houseTotalsTerm = DB::table('point_transactions')
            ->select('house_id', DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$termStart, $termEnd])
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $leadingHouseTermId = collect($houseTotalsTerm)
            ->sortByDesc('total')
            ->keys()
            ->first();
        $leadingHouseTerm = optional($houses->firstWhere('id', (int) $leadingHouseTermId))->name;
        $leadingHouseYearId = collect($houseTotalsYear)
            ->sortByDesc('total')
            ->keys()
            ->first();
        $leadingHouseYear = optional($houses->firstWhere('id', (int) $leadingHouseYearId))->name;

        $weather = Cache::remember('tv_weather', 600, function () {
            $fallback = [
                ['label' => '8AM', 'temp' => 14, 'rain' => 20, 'code' => 1],
                ['label' => 'RECESS', 'temp' => 16, 'rain' => 10, 'code' => 1],
                ['label' => '12PM', 'temp' => 18, 'rain' => 5, 'code' => 0],
                ['label' => 'LUNCH', 'temp' => 19, 'rain' => 15, 'code' => 1],
                ['label' => '3PM', 'temp' => 17, 'rain' => 25, 'code' => 2],
            ];

            try {
                $response = Http::timeout(12)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => -42.73,
                    'longitude' => 147.24,
                    'hourly' => 'temperature_2m,precipitation_probability,weathercode',
                    'timezone' => 'Australia/Hobart',
                ]);

                if (! $response->successful()) {
                    return $fallback;
                }

                $data = $response->json();
                $hourly = $data['hourly'] ?? null;
                if (! is_array($hourly)) {
                    return $fallback;
                }

                $temps = $hourly['temperature_2m'] ?? null;
                $rains = $hourly['precipitation_probability'] ?? null;
                $codes = $hourly['weathercode'] ?? null;
                if (! is_array($temps) || ! is_array($rains) || ! is_array($codes)) {
                    return $fallback;
                }

                $indices = [8, 10, 12, 13, 15];
                foreach ($indices as $i) {
                    if (! array_key_exists($i, $temps) || ! array_key_exists($i, $rains) || ! array_key_exists($i, $codes)) {
                        return $fallback;
                    }
                }

                return [
                    ['label' => '8AM', 'temp' => round($temps[8]), 'rain' => (int) $rains[8], 'code' => (int) $codes[8]],
                    ['label' => 'RECESS', 'temp' => round($temps[10]), 'rain' => (int) $rains[10], 'code' => (int) $codes[10]],
                    ['label' => '12PM', 'temp' => round($temps[12]), 'rain' => (int) $rains[12], 'code' => (int) $codes[12]],
                    ['label' => 'LUNCH', 'temp' => round($temps[13]), 'rain' => (int) $rains[13], 'code' => (int) $codes[13]],
                    ['label' => '3PM', 'temp' => round($temps[15]), 'rain' => (int) $rains[15], 'code' => (int) $codes[15]],
                ];
            } catch (\Exception $e) {
                return $fallback;
            }
        });

        $topByHouse = function (string $houseName) {
            return DB::table('students')
                ->leftJoin('houses', 'students.house_id', '=', 'houses.id')
                ->select('students.first_name', 'students.last_name', 'students.house_points', 'houses.name as house_name')
                ->where('houses.name', $houseName)
                ->orderByDesc('students.house_points')
                ->limit(16)
                ->get();
        };

        $topGryffindor = $topByHouse('Gryffindor');
        $topSlytherin = $topByHouse('Slytherin');
        $topRavenclaw = $topByHouse('Ravenclaw');
        $topHufflepuff = $topByHouse('Hufflepuff');

        $houseHeroCards = collect($houseOrder)->map(function (string $name) use ($houseTotalsLookup) {
            $row = $houseTotalsLookup->get($name);

            return (object) [
                'name' => $name,
                'points' => (int) (($row['total'] ?? 0)),
            ];
        });

        $houseWinnerName = $leadingHouseYear;

        return view('tv.index', [
            'series' => $apexSeries,
            'dates' => $labels,
            'topStudents' => $topStudents,
            'topTeachers' => $topTeachers,
            'termTotals' => $termTotals,
            'annualTotals' => $annualTotals,
            'recent' => $recent,
            'weather' => $weather,
            'houses' => $houses,
            'housePointsByTerm' => $housePointsByTerm,
            'houseTotalsYear' => $houseTotalsYear,
            'houseTotalsTerm' => $houseTotalsTerm,
            'leadingHouseTerm' => $leadingHouseTerm,
            'leadingHouseYear' => $leadingHouseYear,
            'houseHeroCards' => $houseHeroCards,
            'houseWinnerName' => $houseWinnerName,
            'topGryffindor' => $topGryffindor,
            'topSlytherin' => $topSlytherin,
            'topRavenclaw' => $topRavenclaw,
            'topHufflepuff' => $topHufflepuff,
            'gryffindorPoints' => $gryffindorPoints,
            'slytherinPoints' => $slytherinPoints,
            'ravenclawPoints' => $ravenclawPoints,
            'hufflepuffPoints' => $hufflepuffPoints,
        ]);
    }
}
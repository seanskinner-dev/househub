<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('awards')->truncate();
        DB::table('commendations')->truncate();
        DB::table('point_transactions')->truncate();
        DB::table('students')->truncate();

        $houses = [
            ['id' => 1, 'name' => 'Gryffindor', 'colour_hex' => '#740001'],
            ['id' => 2, 'name' => 'Slytherin', 'colour_hex' => '#1a472a'],
            ['id' => 3, 'name' => 'Ravenclaw', 'colour_hex' => '#0e1a40'],
            ['id' => 4, 'name' => 'Hufflepuff', 'colour_hex' => '#ffcc00'],
        ];

        foreach ($houses as $house) {
            DB::table('houses')->updateOrInsert(
                ['id' => $house['id']],
                [
                    'name' => $house['name'],
                    'colour_hex' => $house['colour_hex'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $firstNames = [
            'Alex','Jordan','Taylor','Casey','Riley','Morgan','Avery','Quinn','Jamie','Harper',
            'Parker','Rowan','Charlie','Sawyer','Elliot','Hayden','Reese','Emerson','Dakota','Finley',
            'Bailey','Logan','Skyler','Cameron','Blake','Micah','Drew','Jules','River','Noel',
        ];
        $lastNames = [
            'Smith','Johnson','Brown','Taylor','Anderson','Thomas','Jackson','White','Harris','Martin',
            'Young','King','Wright','Scott','Green','Baker','Nelson','Hill','Ward','Carter',
            'Cooper','Kelly','Morgan','Bell','Cook','Foster','Gray','Reed','Brooks','Price',
        ];

        $studentCount = 80;
        $students = [];
        for ($i = 1; $i <= $studentCount; $i++) {
            $house = $houses[($i - 1) % count($houses)];
            $students[] = [
                'id' => $i,
                'first_name' => $firstNames[($i - 1) % count($firstNames)],
                'last_name' => $lastNames[(int) floor(($i - 1) / count($firstNames)) % count($lastNames)],
                'house_name' => $house['name'],
                'house_id' => $house['id'],
                'colour_hex' => $house['colour_hex'],
                'year_level' => 7 + (($i - 1) % 6),
                'house_points' => 0,
                'points' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('students')->insert($students);

        $teacherId = (int) (DB::table('users')->orderBy('id')->value('id') ?? 1);
        $teacherName = (string) (DB::table('users')->where('id', $teacherId)->value('name') ?? 'Demo Teacher');

        $targetByStudent = [];
        for ($i = 1; $i <= $studentCount; $i++) {
            if ($i <= 24) {
                $targetByStudent[$i] = random_int(0, 5);
            } elseif ($i <= 52) {
                $targetByStudent[$i] = random_int(6, 15);
            } else {
                $targetByStudent[$i] = random_int(16, 80);
            }
        }

        // Build a 31-day activity profile: some high, some low, some zero.
        $dayOffsets = range(0, 30);
        shuffle($dayOffsets);
        $zeroOffsets = array_slice($dayOffsets, 0, 7);
        $highOffsets = array_slice($dayOffsets, 7, 10);
        $lowOffsets = array_slice($dayOffsets, 17);

        $activityWeightByOffset = [];
        foreach (range(0, 30) as $offset) {
            if (in_array($offset, $zeroOffsets, true)) {
                $activityWeightByOffset[$offset] = 0;
            } elseif (in_array($offset, $highOffsets, true)) {
                $activityWeightByOffset[$offset] = 5;
            } elseif (in_array($offset, $lowOffsets, true)) {
                $activityWeightByOffset[$offset] = 2;
            } else {
                $activityWeightByOffset[$offset] = 1;
            }
        }

        $weightedOffsets = [];
        foreach ($activityWeightByOffset as $offset => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $weightedOffsets[] = $offset;
            }
        }
        if ($weightedOffsets === []) {
            $weightedOffsets = [0];
        }

        $transactions = [];
        for ($studentId = 1; $studentId <= $studentCount; $studentId++) {
            $student = $students[$studentId - 1];
            $target = $targetByStudent[$studentId];

            $txnCount = $target <= 5 ? random_int(1, 3) : ($target <= 15 ? random_int(3, 6) : random_int(6, 14));
            $remaining = $target;
            $daysUsed = [];

            for ($t = 1; $t <= $txnCount; $t++) {
                $maxForTxn = max(1, $remaining - (($txnCount - $t) * 1));
                $amount = $t === $txnCount ? $remaining : random_int(1, $maxForTxn);
                $remaining -= $amount;

                $dayOffset = $weightedOffsets[array_rand($weightedOffsets)];
                while (isset($daysUsed[$dayOffset])) {
                    $dayOffset = $weightedOffsets[array_rand($weightedOffsets)];
                }
                $daysUsed[$dayOffset] = true;

                $when = Carbon::now()->subDays($dayOffset)->setTime(random_int(8, 16), random_int(0, 59), 0);
                $transactions[] = [
                    'student_id' => $studentId,
                    'house_id' => $student['house_id'],
                    'amount' => $amount,
                    'category' => 'demo',
                    'description' => 'Demo seeded activity',
                    'awarded_by' => $teacherId,
                    'teacher_name' => $teacherName,
                    'created_at' => $when,
                    'updated_at' => $when,
                ];
            }
        }

        DB::table('point_transactions')->insert($transactions);

        // Seed awards/commendations across the same historical window.
        $awardTemplates = [
            ['name' => 'Leadership', 'description' => 'Demonstrated strong leadership in house activities.'],
            ['name' => 'Resilience', 'description' => 'Showed resilience and consistent effort.'],
            ['name' => 'Teamwork', 'description' => 'Contributed positively to team outcomes.'],
            ['name' => 'Academic Effort', 'description' => 'Maintained excellent academic effort.'],
        ];
        $awards = [];
        $commendations = [];
        foreach (range(1, $studentCount) as $studentId) {
            if (random_int(1, 100) <= 20) {
                $awardDay = $weightedOffsets[array_rand($weightedOffsets)];
                $tpl = $awardTemplates[array_rand($awardTemplates)];
                $when = Carbon::now()->subDays($awardDay)->setTime(random_int(8, 16), random_int(0, 59), 0);
                $awards[] = [
                    'student_id' => $studentId,
                    'awarded_by' => $teacherId,
                    'name' => $tpl['name'],
                    'description' => $tpl['description'],
                    'created_at' => $when,
                    'updated_at' => $when,
                ];
            }
            if (random_int(1, 100) <= 35) {
                $commendDay = $weightedOffsets[array_rand($weightedOffsets)];
                $when = Carbon::now()->subDays($commendDay)->setTime(random_int(8, 16), random_int(0, 59), 0);
                $commendations[] = [
                    'student_id' => $studentId,
                    'awarded_by' => $teacherId,
                    'created_at' => $when,
                    'updated_at' => $when,
                ];
            }
        }
        if ($awards !== []) {
            DB::table('awards')->insert($awards);
        }
        if ($commendations !== []) {
            DB::table('commendations')->insert($commendations);
        }

        foreach ($targetByStudent as $studentId => $total) {
            DB::table('students')
                ->where('id', $studentId)
                ->update(['house_points' => $total, 'points' => $total, 'updated_at' => $now]);
        }

        $houseTotals = DB::table('students')
            ->select('house_id', DB::raw('SUM(house_points) as total'))
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        foreach ($houses as $house) {
            DB::table('houses')
                ->where('id', $house['id'])
                ->update([
                    'points' => (int) ($houseTotals[$house['id']]->total ?? 0),
                    'updated_at' => $now,
                ]);
        }
    }
}

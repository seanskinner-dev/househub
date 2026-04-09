<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoSchoolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('point_transactions')->truncate();
        DB::table('commendations')->truncate();
        DB::table('awards')->truncate();
        DB::table('students')->truncate();
        DB::table('houses')->truncate();
        DB::table('users')->where('id', '>', 1)->delete();

        /*
        |--------------------------------------------------------------------------
        | HOUSES
        |--------------------------------------------------------------------------
        */

        $houses = [
            ['name' => 'Gryffindor', 'colour_hex' => '#740001'],
            ['name' => 'Slytherin', 'colour_hex' => '#1a472a'],
            ['name' => 'Ravenclaw', 'colour_hex' => '#0e1a40'],
            ['name' => 'Hufflepuff', 'colour_hex' => '#ffcc00'],
        ];

        foreach ($houses as $house) {
            DB::table('houses')->insert([
                'name' => $house['name'],
                'colour_hex' => $house['colour_hex'],
                'points' => 0,
            ]);
        }

        $houseIds = DB::table('houses')->pluck('id', 'name');

        /*
        |--------------------------------------------------------------------------
        | TEACHERS
        |--------------------------------------------------------------------------
        */

        $teachers = [
            'Minerva Smith','James McKenzie','Sarah O\'Connor','Lachlan Brown',
            'Emily Nguyen','Tom Weasley','Sophie Granger','Daniel Thompson'
        ];

        $teacherProfiles = [];

        foreach ($teachers as $name) {

            $roll = rand(1, 100);

            if ($roll <= 20) $type = 'generous';
            elseif ($roll <= 80) $type = 'balanced';
            else $type = 'strict';

            $teacherProfiles[] = [
                'id' => DB::table('users')->insertGetId([
                    'name' => $name,
                    'email' => Str::slug($name) . '@school.com',
                    'password' => bcrypt('password'),
                ]),
                'type' => $type
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENTS
        |--------------------------------------------------------------------------
        */

        $firstNames = [
            'Albus','Scorpius','Teddy','Victoire','Hugo','Rose',
            'Oliver','Jack','Noah','William','Charlie','Leo',
            'Olivia','Amelia','Isla','Mia','Ava','Grace',
            'Liam','Ethan','Lucas','Zoe','Ruby','Ella',
            'Mason','Logan','Aiden','Harper','Aria','Layla',
            'Jackson','Sebastian','Henry','Alexander','Chloe','Lily'
        ];

        $lastNames = [
            'Potter','Malfoy','Longbottom','Lovegood',
            'Smith','Brown','Taylor','Wilson','Anderson',
            'Harris','Clark','Walker','White','Hall',
            'Johnson','Williams','Jones','Davis','Miller',
            'Moore','Martin','Lee','Perez','Thompson'
        ];

        $usedNames = [];

        function getUniqueName($firstNames, $lastNames, &$usedNames) {
            do {
                $first = $firstNames[array_rand($firstNames)];
                $last = $lastNames[array_rand($lastNames)];
                $full = $first . ' ' . $last;

                if (!isset($usedNames[$full])) {
                    $usedNames[$full] = 0;
                }

                $usedNames[$full]++;

            } while ($usedNames[$full] > 2);

            return [$first, $last];
        }

        function pickHouse($weights, $houseIds) {
            $total = array_sum($weights);
            $rand = rand(1, $total);

            foreach ($weights as $house => $weight) {
                $rand -= $weight;
                if ($rand <= 0) {
                    return $houseIds[$house];
                }
            }
        }

        $houseWeights = [
            'Gryffindor' => 30,
            'Slytherin' => 25,
            'Ravenclaw' => 25,
            'Hufflepuff' => 20,
        ];

        $students = [];

        for ($i = 0; $i < 200; $i++) {

            $houseId = pickHouse($houseWeights, $houseIds);

            $roll = rand(1, 100);

            if ($roll <= 5) $type = 'star';
            elseif ($roll <= 20) $type = 'high';
            elseif ($roll <= 75) $type = 'medium';
            else $type = 'low';

            [$first, $last] = getUniqueName($firstNames, $lastNames, $usedNames);

            $students[] = [
                'id' => DB::table('students')->insertGetId([
                    'first_name' => $first,
                    'last_name' => $last,
                    'house_id' => $houseId,
                    'house_points' => 0,
                    'year_level' => rand(7, 12),
                ]),
                'house_id' => $houseId,
                'type' => $type
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | WEIGHTED POOL
        |--------------------------------------------------------------------------
        */

        $weightedPool = [];

        foreach ($students as $student) {

            $weight = match ($student['type']) {
                'star' => 5,
                'high' => 4,
                'medium' => 3,
                default => 2,
            };

            for ($i = 0; $i < $weight; $i++) {
                $weightedPool[] = $student;
            }
        }

        $recentUse = [];

        /*
        |--------------------------------------------------------------------------
        | TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $start = Carbon::now()->subMonths(6);

        for ($day = 0; $day < 180; $day++) {

            $date = $start->copy()->addDays($day);
            if ($date->isWeekend()) continue;

            $eventsPerDay = rand(25, 60);

            for ($i = 0; $i < $eventsPerDay; $i++) {

                // 🔥 decay (fixes lock issue)
                foreach ($recentUse as $k => $v) {
                    $recentUse[$k] = max(0, $v - 1);
                }

                $student = $weightedPool[array_rand($weightedPool)];
                $id = $student['id'];

                if (!isset($recentUse[$id])) {
                    $recentUse[$id] = 0;
                }

                $recentUse[$id]++;

                $teacher = $teacherProfiles[array_rand($teacherProfiles)];

                $amount = rand(1, 4);

                DB::table('point_transactions')->insert([
                    'student_id' => $student['id'],
                    'house_id' => $student['house_id'],
                    'amount' => $amount,
                    'category' => 'manual',
                    'description' => 'Great work',
                    'awarded_by' => $teacher['id'],
                    'created_at' => $date,
                    'updated_at' => now(),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        DB::table('houses')->update(['points' => 0]);
        DB::table('students')->update(['house_points' => 0]);

        $transactions = DB::table('point_transactions')->get();

        foreach ($transactions as $t) {

            DB::table('houses')
                ->where('id', $t->house_id)
                ->increment('points', $t->amount);

            if ($t->student_id) {
                DB::table('students')
                    ->where('id', $t->student_id)
                    ->increment('house_points', $t->amount);
            }
        }
    }
}
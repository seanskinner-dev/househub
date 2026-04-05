<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HogwartsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('point_transactions')->truncate();
        DB::table('students')->truncate();

        $houses = ['Gryffindor','Slytherin','Ravenclaw','Hufflepuff'];

        // =========================
        // WESTERN FIRST NAMES
        // =========================
        $westernFirst = [
            'Aaron','Abel','Adam','Adrian','Aiden','Alan','Albert','Alex','Alexander','Alfie',
            'Andrew','Anthony','Arthur','Austin','Ben','Benjamin','Blake','Brandon','Brian','Callum',
            'Caleb','Cameron','Carl','Charles','Chris','Christian','Connor','Cooper','Daniel','David',
            'Declan','Dylan','Edward','Elliot','Ethan','Felix','Finn','Fraser','Gabriel','George',
            'Harry','Harvey','Henry','Hudson','Hugh','Isaac','Jack','Jacob','Jake','James',
            'Jamie','Jason','Jayden','Joel','John','Jonathan','Jordan','Joseph','Joshua','Jude',
            'Kai','Kieran','Kyle','Lachlan','Leo','Leon','Lewis','Liam','Logan','Louis',
            'Lucas','Luke','Marcus','Mason','Matthew','Max','Michael','Mitchell','Morgan','Nathan',
            'Nicholas','Noah','Oliver','Oscar','Owen','Patrick','Paul','Peter','Reece','Rhys',
            'Riley','Ryan','Samuel','Sean','Sebastian','Spencer','Taylor','Thomas','Toby','Tyler',
            'William','Zachary'
        ];

        // =========================
        // WESTERN LAST NAMES
        // =========================
        $westernLast = [
            'Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez',
            'Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin',
            'Lee','Perez','Thompson','White','Harris','Sanchez','Clark','Ramirez','Lewis','Robinson',
            'Walker','Young','Allen','King','Wright','Scott','Torres','Nguyen','Hill','Flores',
            'Green','Adams','Nelson','Baker','Hall','Rivera','Campbell','Mitchell','Carter','Roberts',
            'Gomez','Phillips','Evans','Turner','Diaz','Parker','Cruz','Edwards','Collins','Reyes',
            'Stewart','Morris','Morales','Murphy','Cook','Rogers','Gutierrez','Ortiz','Morgan','Cooper',
            'Peterson','Bailey','Reed','Kelly','Howard','Ramos','Kim','Cox','Ward','Richardson',
            'Watson','Brooks','Chavez','Wood','James','Bennett','Gray','Mendoza','Ruiz','Hughes'
        ];

        // =========================
        // HOGWARTS NAMES
        // =========================
        $hpFirst = [
            'Albus','Scorpius','Lysander','Hugo','Rose','Draco','Minerva',
            'Sirius','Remus','Cedric','Luna','Neville'
        ];

        $hpLast = [
            'Potter','Malfoy','Weasley','Lovegood','Longbottom','Black',
            'Lupin','Diggory','Nott','Parkinson'
        ];

        // =========================
        // GENERATE STUDENTS
        // =========================
        $students = [];
        $usedNames = [];
        $firstNameCounts = [];

        $maxPerFirstName = 2;

        for ($i = 1; $i <= 600; $i++) {

            do {
                $type = rand(1,3);

                if ($type === 1) {
                    $first = $westernFirst[array_rand($westernFirst)];
                    $last  = $westernLast[array_rand($westernLast)];
                }
                elseif ($type === 2) {
                    $first = $hpFirst[array_rand($hpFirst)];
                    $last  = $hpLast[array_rand($hpLast)];
                }
                else {
                    $first = rand(0,1)
                        ? $hpFirst[array_rand($hpFirst)]
                        : $westernFirst[array_rand($westernFirst)];

                    $last = rand(0,1)
                        ? $hpLast[array_rand($hpLast)]
                        : $westernLast[array_rand($westernLast)];
                }

                $full = $first . ' ' . $last;

            } while (
                in_array($full, $usedNames) ||
                (($firstNameCounts[$first] ?? 0) >= $maxPerFirstName)
            );

            $usedNames[] = $full;
            $firstNameCounts[$first] = ($firstNameCounts[$first] ?? 0) + 1;

            $students[] = [
                'id' => $i,
                'first_name' => $first,
                'last_name' => $last,
                'house_name' => $houses[array_rand($houses)],
                'year_level' => rand(7, 12),
                'house_points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('students')->insert($students);

        // =========================
        // 6 MONTHS DATA
        // =========================
        $start = Carbon::now()->subMonths(6);
        $end = Carbon::now();

        $students = DB::table('students')->get();

        for ($d = $start->copy(); $d <= $end; $d->addDay()) {

            foreach ($students as $student) {

                if (rand(0,100) < 35) {

                    $amount = rand(-1, 3);

                    DB::table('point_transactions')->insert([
                        'student_id' => $student->id,
                        'amount' => $amount,
                        'category' => 'auto',
                        'description' => 'Generated',
                        'awarded_by' => 1,
                        'created_at' => $d,
                        'updated_at' => $d,
                    ]);
                }
            }
        }

        // =========================
        // AT RISK
        // =========================
        $atRiskIds = DB::table('students')
            ->inRandomOrder()
            ->limit(40)
            ->pluck('id');

        foreach ($atRiskIds as $id) {
            DB::table('point_transactions')->insert([
                'student_id' => $id,
                'amount' => -10,
                'category' => 'decline',
                'description' => 'At risk behaviour',
                'awarded_by' => 1,
                'created_at' => now()->subDays(rand(1,10)),
                'updated_at' => now(),
            ]);
        }

        // =========================
        // UPDATE TOTALS
        // =========================
        $totals = DB::table('point_transactions')
            ->select('student_id', DB::raw('SUM(amount) as total'))
            ->groupBy('student_id')
            ->get();

        foreach ($totals as $t) {
            DB::table('students')
                ->where('id', $t->student_id)
                ->update(['house_points' => $t->total]);
        }
    }
}
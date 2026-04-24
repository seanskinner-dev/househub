<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@househub.com',
        ], [
            'name' => 'Admin User',
        ]);

        $this->call(DemoTeacherSeeder::class);

        if (Student::query()->count() === 0) {
            Student::factory(50)->create();
        }

        $houseData = [
            ['name' => 'Gryffindor', 'colour_hex' => '#740001'],
            ['name' => 'Slytherin', 'colour_hex' => '#1a472a'],
            ['name' => 'Ravenclaw', 'colour_hex' => '#0e1a40'],
            ['name' => 'Hufflepuff', 'colour_hex' => '#ffcc00'],
        ];

        foreach ($houseData as $house) {
            DB::table('houses')->updateOrInsert(
                ['name' => $house['name']],
                [
                    'colour_hex' => $house['colour_hex'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $houseIds = DB::table('houses')->whereIn('name', array_column($houseData, 'name'))->pluck('id')->all();

        Student::query()
            ->whereNull('house_id')
            ->chunkById(200, function ($students) use ($houseIds): void {
                foreach ($students as $student) {
                    DB::table('students')
                        ->where('id', $student->id)
                        ->update(['house_id' => $houseIds[array_rand($houseIds)]]);
                }
            });

        $students = DB::table('students')
            ->whereNotNull('house_id')
            ->select('id', 'house_id')
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students with house_id found. Skipping point_transactions seeding.');
            return;
        }

        $awardedBy = User::query()->value('id') ?? 1;
        $count = random_int(300, 500);
        $rows = [];

        for ($i = 0; $i < $count; $i++) {
            $student = $students[random_int(0, $students->count() - 1)];
            $createdAt = Carbon::now()->subDays(random_int(0, 29))->subMinutes(random_int(0, 1439));

            $rows[] = [
                'student_id' => $student->id,
                'house_id' => $student->house_id,
                'amount' => random_int(1, 5),
                'category' => 'manual',
                'description' => 'Test points',
                'awarded_by' => $awardedBy,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        DB::table('point_transactions')->insert($rows);

        $this->command->info("Development seed complete: houses ensured, student house assignments updated, {$count} point transactions created.");
    }
}
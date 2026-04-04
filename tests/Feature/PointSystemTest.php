<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PointSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_point_updates_student_and_house()
    {
        $houseId = DB::table('houses')->insertGetId([
            'name' => 'Gryffindor',
            'colour_hex' => '#ff0000',
            'points' => 0
        ]);

        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Harry',
            'last_name' => 'Potter',
            'house_id' => $houseId,
            'house_name' => 'Gryffindor',
            'house_points' => 0
        ]);

        $this->post('/points', [
            'student_id' => $studentId,
            'amount' => 1
        ]);

        $this->assertEquals(1, DB::table('students')->where('id', $studentId)->value('house_points'));
        $this->assertEquals(1, DB::table('houses')->where('id', $houseId)->value('points'));
    }

    public function test_house_point_does_not_affect_student()
    {
        $houseId = DB::table('houses')->insertGetId([
            'name' => 'Slytherin',
            'colour_hex' => '#00ff00',
            'points' => 0
        ]);

        $this->post('/points', [
            'house_name' => 'Slytherin',
            'amount' => 1
        ]);

        $this->assertEquals(1, DB::table('houses')->where('id', $houseId)->value('points'));
        $this->assertEquals(0, DB::table('students')->count());
    }
}
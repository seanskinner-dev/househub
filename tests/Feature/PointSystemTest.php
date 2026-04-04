<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PointSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_plus_one_adds_points_and_transaction(): void
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Harry',
            'last_name' => 'Potter',
            'year_level' => 7,
            'house_name' => 'Gryffindor',
            'house_points' => 0,
        ]);

        $response = $this->post('/points', [
            'student_id' => $studentId,
            'amount' => 1
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('students', [
            'id' => $studentId,
            'house_points' => 1
        ]);

        $this->assertDatabaseHas('point_transactions', [
            'student_id' => $studentId,
            'amount' => 1
        ]);
    }

    public function test_house_award_creates_single_transaction_and_updates_all_students(): void
    {
        DB::table('houses')->insert([
            'name' => 'Gryffindor',
            'colour_hex' => '#740001'
        ]);

        DB::table('students')->insert([
            [
                'first_name' => 'Harry',
                'last_name' => 'Potter',
                'year_level' => 7,
                'house_name' => 'Gryffindor',
                'house_points' => 0
            ],
            [
                'first_name' => 'Ron',
                'last_name' => 'Weasley',
                'year_level' => 7,
                'house_name' => 'Gryffindor',
                'house_points' => 0
            ]
        ]);

        $response = $this->post('/points', [
            'house_name' => 'Gryffindor',
            'amount' => 1,
            'type' => 'house'
        ]);

        $response->assertStatus(302);

        $this->assertEquals(
            1,
            DB::table('point_transactions')->count()
        );

        $this->assertEquals(
            2,
            DB::table('students')->where('house_points', 1)->count()
        );
    }

    public function test_commendation_saves_with_description(): void
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Hermione',
            'last_name' => 'Granger',
            'year_level' => 7,
            'house_name' => 'Gryffindor',
            'house_points' => 0,
        ]);

        $response = $this->post('/points', [
            'student_id' => $studentId,
            'amount' => 1,
            'category' => 'commendation',
            'description' => 'Helped another student'
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('point_transactions', [
            'student_id' => $studentId,
            'category' => 'commendation',
            'description' => 'Helped another student'
        ]);
    }

    public function test_ajax_request_returns_json_response(): void
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Hermione',
            'last_name' => 'Granger',
            'year_level' => 7,
            'house_name' => 'Gryffindor',
            'house_points' => 0,
        ]);

        $response = $this->postJson('/points', [
            'student_id' => $studentId,
            'amount' => 1
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);
    }

    // 🔥 NEW TEST (HOUSE BUTTON VIA AJAX)
    public function test_house_ajax_request_returns_success_and_updates_data(): void
    {
        DB::table('houses')->insert([
            'name' => 'Gryffindor',
            'colour_hex' => '#740001'
        ]);

        DB::table('students')->insert([
            [
                'first_name' => 'Harry',
                'last_name' => 'Potter',
                'year_level' => 7,
                'house_name' => 'Gryffindor',
                'house_points' => 0
            ],
            [
                'first_name' => 'Ron',
                'last_name' => 'Weasley',
                'year_level' => 7,
                'house_name' => 'Gryffindor',
                'house_points' => 0
            ]
        ]);

        $response = $this->postJson('/points', [
            'house_name' => 'Gryffindor',
            'amount' => 1
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);

        // Transaction created
        $this->assertEquals(
            1,
            DB::table('point_transactions')->count()
        );

        // All students updated
        $this->assertEquals(
            2,
            DB::table('students')->where('house_points', 1)->count()
        );
    }
}
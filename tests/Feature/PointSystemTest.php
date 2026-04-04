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

    // ✅ NEW RULE: HOUSE DOES NOT TOUCH STUDENTS
    public function test_house_award_updates_house_only(): void
    {
        DB::table('houses')->insert([
            'name' => 'Gryffindor',
            'colour_hex' => '#740001',
            'points' => 0
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
            'amount' => 1
        ]);

        $response->assertStatus(302);

        // ✅ Only 1 transaction
        $this->assertEquals(1, DB::table('point_transactions')->count());

        // ❌ Students should NOT change
        $this->assertEquals(
            0,
            DB::table('students')->where('house_points', 1)->count()
        );

        // ✅ House total updated
        $this->assertEquals(
            1,
            DB::table('houses')->where('points', 1)->count()
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

    // ✅ UPDATED AJAX TEST
    public function test_house_ajax_request_updates_house_only(): void
    {
        DB::table('houses')->insert([
            'name' => 'Gryffindor',
            'colour_hex' => '#740001',
            'points' => 0
        ]);

        $response = $this->postJson('/points', [
            'house_name' => 'Gryffindor',
            'amount' => 1
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);

        // ✅ Only 1 transaction
        $this->assertEquals(
            1,
            DB::table('point_transactions')->count()
        );

        // ✅ House updated
        $this->assertEquals(
            1,
            DB::table('houses')->where('points', 1)->count()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NEW TESTS (AWARDS + DESCRIPTIONS)
    |--------------------------------------------------------------------------
    */

    public function test_award_stores_description(): void
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Harry',
            'last_name' => 'Potter',
            'year_level' => 7,
            'house_name' => 'Gryffindor',
            'house_points' => 0,
        ]);

        $this->postJson('/points', [
            'student_id' => $studentId,
            'award_key' => 'quidditch_star',
            'description' => 'Won the match'
        ]);

        $this->assertDatabaseHas('point_transactions', [
            'student_id' => $studentId,
            'description' => 'Won the match'
        ]);
    }

    public function test_award_uses_config_points(): void
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Harry',
            'last_name' => 'Potter',
            'year_level' => 7,
            'house_name' => 'Gryffindor',
            'house_points' => 0,
        ]);

        $this->postJson('/points', [
            'student_id' => $studentId,
            'award_key' => 'quidditch_star'
        ]);

        $this->assertDatabaseHas('point_transactions', [
            'student_id' => $studentId,
            'amount' => config('awards.quidditch_star.points')
        ]);
    }

    public function test_invalid_award_returns_error(): void
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'Harry',
            'last_name' => 'Potter',
            'year_level' => 7,
            'house_name' => 'Gryffindor',
            'house_points' => 0,
        ]);

        $response = $this->postJson('/points', [
            'student_id' => $studentId,
            'award_key' => 'invalid_award'
        ]);

        $response->assertStatus(422);
    }
}
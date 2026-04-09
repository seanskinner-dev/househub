<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\House;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $firstNames = [
            'Albus', 'Cedric', 'Draco', 'Hermione', 'Luna', 'Neville', 'Pansy', 'Seamus', 
            'Cho', 'Cormac', 'Lavender', 'Millicent', 'Gregory', 'Vincent', 'Padma', 'Parvati',
            'Dean', 'Argus', 'Phineas', 'Septimus', 'Garrick', 'Minerva', 'Pomona', 'Filius',
            'Oliver', 'Jack', 'Noah', 'William', 'Charlie', 'Leo',
            'Olivia', 'Amelia', 'Isla', 'Mia', 'Ava', 'Grace'
        ];

        $lastNames = [
            'Potter', 'Granger', 'Weasley', 'Malfoy', 'Longbottom', 'Lovegood', 'Diggory', 
            'Chang', 'Finnigan', 'Brown', 'Parkinson', 'Goyle', 'Crabbe', 'Patil', 'Thomas',
            'Slughorn', 'Flitwick', 'Sprout', 'Zabini', 'Nott', 'Bulstrode', 'Macmillan',
            'Smith', 'Taylor', 'Wilson', 'Anderson', 'Harris', 'Clark', 'Walker'
        ];

        // ✅ Get a real house (safe fallback)
        $houseId = House::inRandomOrder()->value('id') ?? 1;

        return [
            'first_name' => $this->faker->randomElement($firstNames),
            'last_name' => $this->faker->randomElement($lastNames),

            // ✅ FIXED
            'house_id' => $houseId,

            'year_level' => $this->faker->numberBetween(7, 12),

            // ✅ FIXED
            'house_points' => 0,
        ];
    }
}
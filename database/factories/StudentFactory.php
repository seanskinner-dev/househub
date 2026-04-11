<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $firstNames = [
            'Albus', 'Cedric', 'Draco', 'Hermione', 'Luna', 'Neville', 'Pansy', 'Seamus', 
            'Cho', 'Cormac', 'Lavender', 'Millicent', 'Gregory', 'Vincent', 'Padma', 'Parvati',
            'Dean', 'Argus', 'Phineas', 'Septimus', 'Garrick', 'Minerva', 'Pomona', 'Filius'
        ];

        $lastNames = [
            'Potter', 'Granger', 'Weasley', 'Malfoy', 'Longbottom', 'Lovegood', 'Diggory', 
            'Chang', 'Finnigan', 'Brown', 'Parkinson', 'Goyle', 'Crabbe', 'Patil', 'Thomas',
            'Slughorn', 'Flitwick', 'Sprout', 'Zabini', 'Nott', 'Bulstrode', 'Macmillan'
        ];

        $houses = [
            ['name' => 'Gryffindor', 'color' => '#740001'],
            ['name' => 'Hufflepuff', 'color' => '#ecb939'],
            ['name' => 'Ravenclaw', 'color' => '#0e1a40'],
            ['name' => 'Slytherin', 'color' => '#1a472a'],
        ];

        $house = $this->faker->randomElement($houses);

        return [
            'first_name' => $this->faker->randomElement($firstNames),
            'last_name' => $this->faker->randomElement($lastNames),
            'house_name' => $house['name'],
            'colour_hex' => $house['color'],
            'year_level' => $this->faker->numberBetween(1, 7),
            'house_points' => $this->faker->numberBetween(0, 150),
        ];
    }
}
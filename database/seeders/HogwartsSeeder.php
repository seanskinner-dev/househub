<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\House;
use App\Models\Student;

class HogwartsSeeder extends Seeder
{
    public function run(): void
    {
        $houses = [
            ['name' => 'Gryffindor', 'colour_hex' => '#7f0909'],
            ['name' => 'Slytherin', 'colour_hex' => '#0d6217'],
            ['name' => 'Ravenclaw', 'colour_hex' => '#0e1a40'],
            ['name' => 'Hufflepuff', 'colour_hex' => '#ecb939'],
        ];

        foreach ($houses as $house) {
            $createdHouse = House::create([
                'name' => $house['name'],
                'colour_hex' => $house['colour_hex']
            ]);

            Student::create([
                'first_name' => 'Test',
                'last_name' => $house['name'] . ' Student',
                'house_id' => $createdHouse->id,
                'year_level' => rand(1, 7)
            ]);
        }
    }
}
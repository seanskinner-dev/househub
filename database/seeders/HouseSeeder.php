<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\House;

class HouseSeeder extends Seeder
{
    public function run(): void
    {
        House::create(['name' => 'Gryffindor', 'color' => '#740001', 'points' => 0]);
        House::create(['name' => 'Slytherin', 'color' => '#1a472a', 'points' => 0]);
        House::create(['name' => 'Ravenclaw', 'color' => '#0e1a40', 'points' => 0]);
        House::create(['name' => 'Hufflepuff', 'color' => '#ecb939', 'points' => 0]);
    }
}
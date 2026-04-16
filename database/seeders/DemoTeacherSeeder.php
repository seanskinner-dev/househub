<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            'Mr Smith',
            'Ms Johnson',
            'Mr Brown',
            'Ms Taylor',
            'Mr Wilson',
            'Ms Clark',
            'Mr Harris',
            'Ms Martin'
        ];

        foreach ($teachers as $name) {
            User::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '.', $name)).'@househub.local'],
                [
                    'name' => $name,
                    'password' => bcrypt('notused123')
                ]
            );
        }
    }
}

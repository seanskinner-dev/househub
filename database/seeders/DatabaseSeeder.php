<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@househub.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'updated_at' => now(),
            ]
        );

        $this->call(DemoTeacherSeeder::class);
        $this->call(DemoDataSeeder::class);

        $this->command->info('Seeder ran clean');
    }
}

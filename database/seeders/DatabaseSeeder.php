<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a default admin user (Optional)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@househub.com',
        ]);

        $this->call(DemoTeacherSeeder::class);

        // 2. Create 50 random students
        Student::factory(50)->create();

        $this->command->info('Database seeded: 50 students created successfully!');
    }
}
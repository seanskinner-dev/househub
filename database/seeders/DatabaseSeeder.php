<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Admin user (keep this)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@househub.com',
        ]);

        // ✅ MAIN SEEDER (this drives EVERYTHING)
        $this->call(DemoSchoolSeeder::class);

        $this->command->info('HouseHub demo data seeded successfully!');
    }
}
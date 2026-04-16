<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SystemTeacherSeeder extends Seeder
{
    public function run(): void
    {
        // Renames old demo identity and keeps a single hidden system account.
        User::updateOrCreate(
            ['email' => 'system@househub.local'],
            [
                'name' => 'System',
                'password' => 'demo123',
            ]
        );

        User::where('email', 'like', '%@househub.local')
            ->where('email', '!=', 'system@househub.local')
            ->update([
                'name' => 'System',
                'email' => 'system@househub.local',
            ]);
    }
}

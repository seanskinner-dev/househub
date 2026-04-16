<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTeacherSeeder extends Seeder
{
    /**
     * Public demo mode: attributions for unauthenticated /points usage.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'demo@househub.local'],
            [
                'name' => 'Demo Teacher',
                'password' => 'demo123',
            ]
        );
    }
}

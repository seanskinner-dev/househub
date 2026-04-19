<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('name', 'System')
            ->update([
                'name' => 'Staff',
                'updated_at' => now(),
            ]);

        if (Schema::hasColumn('point_transactions', 'teacher_name')) {
            DB::table('point_transactions')
                ->where('teacher_name', 'System')
                ->update([
                    'teacher_name' => 'Staff',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        //
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {

            // ✅ ADD THIS
            $table->unsignedBigInteger('house_id')->nullable()->after('student_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {

            // ✅ REMOVE IT IF ROLLED BACK
            $table->dropColumn('house_id');

        });
    }
};
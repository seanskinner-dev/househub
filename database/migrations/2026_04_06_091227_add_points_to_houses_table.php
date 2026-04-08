<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointsToHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('houses', function (Blueprint $table) {
            // Only add the points column if it doesn't already exist
            if (!Schema::hasColumn('houses', 'points')) {
                $table->integer('points')->default(0);  // Add the points column with a default value of 0
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('houses', function (Blueprint $table) {
            // Drop the points column if the migration is rolled back
            if (Schema::hasColumn('houses', 'points')) {
                $table->dropColumn('points');
            }
        });
    }
}
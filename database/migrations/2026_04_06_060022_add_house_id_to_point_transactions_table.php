<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHouseIdToPointTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only add the foreign key constraint if the column exists
        Schema::table('point_transactions', function (Blueprint $table) {
            // Ensure the 'house_id' column exists, then add the foreign key constraint
            if (!Schema::hasColumn('point_transactions', 'house_id')) {
                $table->foreignId('house_id')->constrained()->onDelete('cascade');
            } else {
                // If the column already exists, just add the foreign key constraint
                $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
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
        Schema::table('point_transactions', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['house_id']);
            $table->dropColumn('house_id');
        });
    }
}
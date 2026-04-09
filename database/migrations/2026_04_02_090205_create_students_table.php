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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');

            // ✅ CORRECT RELATIONSHIP
            $table->foreignId('house_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // ✅ STUDENT DATA
            $table->integer('year_level')->nullable();

            // ✅ THEIR OWN TOTAL
            $table->integer('house_points')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
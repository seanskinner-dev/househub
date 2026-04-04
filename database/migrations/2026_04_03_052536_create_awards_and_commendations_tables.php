<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('commendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('awarded_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('awarded_by')->constrained('users');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('awards');
        Schema::dropIfExists('commendations');
    }
};
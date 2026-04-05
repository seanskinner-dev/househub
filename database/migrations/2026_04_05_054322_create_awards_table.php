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
        Schema::create('awards', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('awarded_by')->nullable();

            // Award details
            $table->string('title');
            $table->text('description')->nullable();

            // Metadata
            $table->timestamp('awarded_at')->useCurrent();

            // Laravel timestamps
            $table->timestamps();

            // Foreign keys (safe, but optional if you prefer)
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('awarded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
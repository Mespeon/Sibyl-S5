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
        Schema::create('user_student_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID of user this student profile belongs to.');
            $table->unsignedBigInteger('course_id')->comment('ID of course this student profile is bound to.');
            $table->unsignedInteger('year_level')->default(1)->comment('Year level of the student this profile belongs to. This is separate from academic year.');
            $table->string('section', 50)->nullable()->default(null)->comment('Section this student belongs to.');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index(['user_id', 'year_level'], 'user_year_level');
            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_student_profiles');
    }
};

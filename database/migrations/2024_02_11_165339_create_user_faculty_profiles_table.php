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
        Schema::create('user_faculty_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID of user to which this faculty profile belongs to.');
            $table->unsignedBigInteger('department_id')->comment('ID of department to which this faculty belongs to.');
            $table->string('employee_number', 20);
            $table->string('position_name', 100)->comment('Name of faculty position.');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'department_id'], 'user_faculty_departments');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_faculty_profiles');
    }
};

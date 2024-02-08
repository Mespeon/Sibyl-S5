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
        Schema::create('user_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->comment('ID of user bound to this department.');
            $table->unsignedBigInteger('department_id')->comment('ID of department this user belongs to.');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_departments');
    }
};

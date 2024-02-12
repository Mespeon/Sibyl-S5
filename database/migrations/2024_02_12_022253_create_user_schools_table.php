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
        Schema::create('user_schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID of user.');
            $table->unsignedBigInteger('school_id')->comment('ID of school this user belongs to.');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'school_id'], 'user_schools');
            $table->foreign('school_id')->references('id')->on('schools');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_schools');
    }
};

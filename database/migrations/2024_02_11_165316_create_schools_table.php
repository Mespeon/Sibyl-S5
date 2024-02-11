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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->comment('Code or acronym for the school.');
            $table->string('name', 100)->comment('Full name of school.');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['code', 'name'], 'school_code_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};

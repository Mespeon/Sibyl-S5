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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->comment('ID of user this profile belongs to.');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable()->default(null);
            $table->string('last_name', 100);
            $table->string('email_address', 150)->comment('Email address associated with this user.');
            $table->string('contact_number', 50)->comment('Phone or mobile number of user.');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

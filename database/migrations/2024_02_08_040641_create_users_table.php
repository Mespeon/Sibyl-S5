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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->comment('Username of user.');
            $table->string('password')->comment('Password of user.');
            $table->bigInteger('status_id')->unsigned()->comment('Status of user account');
            $table->dateTime('account_verified_at')->nullable()->default(null)->comment('Timestamp when the account is verified. Updated when the VERIFY_ACCOUNT setting is enabled.');
            $table->dateTime('last_login')->nullable()->default(null)->comment('Timestamp of user last login');
            $table->dateTime('last_password_change')->nullable()->default(null)->comment('Timestamp of user last password change');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('status_id')->references('id')->on('account_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

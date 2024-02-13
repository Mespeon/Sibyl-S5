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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropPrimary('email');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->id()->first();
            $table->unsignedBigInteger('user_id')->comment('ID of user that requested this password reset.')->after('id');
            $table->renameColumn('email', 'email_address')->comment('Email address of user that requested the password reset.')->change();
            $table->timestamp('updated_at')->nullable()->default(null)->after('created_at');
            $table->softDeletes()->after('updated_at');
            $table->index(['user_id', 'email_address'], 'user_password_reset_requests');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

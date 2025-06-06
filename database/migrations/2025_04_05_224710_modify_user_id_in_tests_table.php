<?php

use App\Models\User;
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
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('time_spent');
        });
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('time_spent');
        });
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};

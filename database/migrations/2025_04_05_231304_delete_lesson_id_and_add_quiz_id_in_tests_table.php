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
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
            $table->dropColumn('lesson_id');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('quiz_id')->after('user_id');
        });
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropColumn('quiz_id');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->after('user_id');
        });
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }
};

<?php

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration replaces foreign key constraints in the tests table
     */
    public function up(): void
    {
        // Update tests table - replace foreign keys
        Schema::table('tests', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign('tests_user_id_foreign');
            $table->dropForeign('tests_enrollment_step_id_foreign');
            $table->dropForeign('tests_quiz_id_foreign');

            // Re-add the foreign key constraints with cascade on delete
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('enrollment_step_id')->references('id')->on('enrollment_steps')->cascadeOnDelete();
            $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert tests table - replace cascade foreign keys with original ones
        Schema::table('tests', function (Blueprint $table) {
            // Drop foreign keys with cascade delete
            $table->dropForeign('tests_user_id_foreign');
            $table->dropForeign('tests_enrollment_step_id_foreign');
            $table->dropForeign('tests_quiz_id_foreign');


            // Re-add the original foreign key constraints without cascade delete
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('enrollment_step_id')->references('id')->on('enrollment_steps');
            $table->foreign('quiz_id')->references('id')->on('quizzes');

        });
    }
};

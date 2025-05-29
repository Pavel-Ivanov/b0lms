<?php

use App\Models\User;
use App\Models\Test;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration replaces foreign key constraints in the test_answers table
     */
    public function up(): void
    {
        // Update test_answers table - replace foreign keys
        Schema::table('test_answers', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign('test_answers_user_id_foreign');
            $table->dropForeign('test_answers_test_id_foreign');
            $table->dropForeign('test_answers_question_id_foreign');
            $table->dropForeign('test_answers_option_id_foreign');

            // Re-add the foreign key constraints without dropping columns
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('test_id')->references('id')->on('tests')->cascadeOnDelete();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
            $table->foreign('option_id')->references('id')->on('question_options')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert test_answers table - replace cascade foreign keys with original ones
        Schema::table('test_answers', function (Blueprint $table) {
            // Drop foreign keys with cascade delete
            $table->dropForeign('test_answers_user_id_foreign');
            $table->dropForeign('test_answers_test_id_foreign');
            $table->dropForeign('test_answers_question_id_foreign');
            $table->dropForeign('test_answers_option_id_foreign');


            // Re-add the original foreign key constraints without cascade delete
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('test_id')->references('id')->on('tests');
            $table->foreign('question_id')->references('id')->on('questions');
            $table->foreign('option_id')->references('id')->on('question_options');
        });
    }
};

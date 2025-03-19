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
        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->boolean('correct');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('test_id')->constrained('tests');
            $table->foreignId('question_id')->constrained('questions');
            $table->foreignId('option_id')->nullable()->constrained('question_options');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_answers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: This migration requires the doctrine/dbal package.
     * Please install it using: composer require doctrine/dbal
     */
    public function up(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->text('option')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->string('option')->change();
        });
    }
};

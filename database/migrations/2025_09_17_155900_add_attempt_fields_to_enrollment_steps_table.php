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
        Schema::table('enrollment_steps', function (Blueprint $table) {
            // Place the new fields right after completed_at, as requested
            $table->unsignedSmallInteger('max_attempts')->nullable()->after('completed_at');
            $table->unsignedSmallInteger('passing_percentage')->nullable()->after('max_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_steps', function (Blueprint $table) {
            $table->dropColumn(['max_attempts', 'passing_percentage']);
        });
    }
};

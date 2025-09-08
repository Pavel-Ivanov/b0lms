<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_steps', function (Blueprint $table) {
            // Add unique index to prevent duplicate step entries per enrollment
            $table->unique(['enrollment_id', 'stepable_type', 'stepable_id'], 'uniq_enrollment_stepable');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'last_synced_at')) {
                $table->dateTime('last_synced_at')->nullable()->after('is_steps_created');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_steps', function (Blueprint $table) {
            $table->dropUnique('uniq_enrollment_stepable');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'last_synced_at')) {
                $table->dropColumn('last_synced_at');
            }
        });
    }
};

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
            $table->string('status')->default('pending')->after('result');
            $table->integer('current_attempt')->default(0)->after('status');
            $table->boolean('passed')->default(false)->after('current_attempt');
            $table->timestamp('started_at')->nullable()->after('time_spent');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->json('answers')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'current_attempt',
                'passed',
                'started_at',
                'completed_at',
                'answers'
            ]);
        });
    }
};

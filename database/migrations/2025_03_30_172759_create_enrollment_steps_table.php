<?php

use App\Models\Enrollment;
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
        Schema::create('enrollment_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Enrollment::class, 'enrollment_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('stepable_id');
            $table->string('stepable_type');
            $table->integer('position')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_steps');
    }
};

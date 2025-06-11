<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Schema;

trait DatabaseTestTables
{
    /**
     * Create all necessary tables for testing
     */
    public function createTestTables()
    {
        // Create company_departments table
        Schema::create('company_departments', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Create company_positions table
        Schema::create('company_positions', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Create users table
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignId('company_department_id')->nullable()->constrained();
            $table->foreignId('company_position_id')->nullable()->constrained();
            $table->string('password');
            $table->string('role')->nullable(); // Add role column needed by User model
            $table->rememberToken();
            $table->timestamps();
        });

        // Create media table
        Schema::create('media', function ($table) {
            $table->id();
            $table->morphs('model');
            $table->uuid()->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->nullableTimestamps();
        });

        // Create course_types table
        Schema::create('course_types', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Create course_levels table
        Schema::create('course_levels', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Create course_categories table
        Schema::create('course_categories', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Create courses table
        Schema::create('courses', function ($table) {
            $table->id();
            $table->string('name');
            $table->foreignId('course_type_id')->nullable()->constrained();
            $table->foreignId('course_level_id')->nullable()->constrained();
            $table->foreignId('course_category_id')->nullable()->constrained();
            $table->timestamps();
        });

        // Create enrollments table
        Schema::create('enrollments', function ($table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('enrollment_date');
            $table->timestamp('completion_deadline')->nullable();
            $table->boolean('is_steps_created')->default(false);
            $table->timestamps();
        });

        // Create lessons table
        Schema::create('lessons', function ($table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('position');
            $table->timestamps();
        });

        // Create quizzes table
        Schema::create('quizzes', function ($table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        // Create questions table
        Schema::create('questions', function ($table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->text('hint')->nullable();
            $table->string('more_info_link')->nullable();
            $table->timestamps();
        });

        // Create question_options table
        Schema::create('question_options', function ($table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('option');
            $table->text('rationale')->nullable();
            $table->boolean('correct')->default(false);
            $table->timestamps();
        });

        // Create enrollment_steps table
        Schema::create('enrollment_steps', function ($table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('stepable_id');
            $table->string('stepable_type');
            $table->integer('position');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // Create tests table
        Schema::create('tests', function ($table) {
            $table->id();
            $table->integer('result')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('time_spent')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_step_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Create test_answers table
        Schema::create('test_answers', function ($table) {
            $table->id();
            $table->boolean('correct')->default(false);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('question_options')->cascadeOnDelete();
            $table->timestamps();
        });

        // Create permissions table
        Schema::create('permissions', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        // Create roles table
        Schema::create('roles', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        // Create model_has_permissions table
        Schema::create('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        // Create model_has_roles table
        Schema::create('model_has_roles', function ($table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        // Create role_has_permissions table
        Schema::create('role_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // Create cache table
        Schema::create('cache', function ($table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Create cache_locks table
        Schema::create('cache_locks', function ($table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Create jobs table
        Schema::create('jobs', function ($table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Create job_batches table
        Schema::create('job_batches', function ($table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Create failed_jobs table
        Schema::create('failed_jobs', function ($table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }
}

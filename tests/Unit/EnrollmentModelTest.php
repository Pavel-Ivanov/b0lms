<?php

use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\CourseType;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Traits\DatabaseTestTables;

uses(TestCase::class, DatabaseTransactions::class, DatabaseTestTables::class);

beforeEach(function () {
    // Make sure we're using SQLite in-memory database
    config(['database.default' => 'sqlite']);
    config(['database.connections.sqlite.database' => ':memory:']);

    // Enable foreign key support for SQLite
    DB::statement('PRAGMA foreign_keys = ON');

    // Create necessary tables for testing
    $this->createTestTables();
});

it('can connect to the database', function () {
    expect(DB::connection()->getPdo())->toBeInstanceOf(PDO::class);
});

it('allows mass assignment', function () {
    $user = User::factory()->create();

    // Create required related models
    $courseType = CourseType::create(['name' => 'Test Type']);
    $courseLevel = CourseLevel::create(['name' => 'Test Level']);
    $courseCategory = CourseCategory::create(['name' => 'Test Category']);

    $course = Course::create([
        'name' => 'Test Course',
        'course_type_id' => $courseType->id,
        'course_level_id' => $courseLevel->id,
        'course_category_id' => $courseCategory->id,
    ]);

    $data = [
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => false,
    ];

    $enrollment = Enrollment::create($data);

    expect($enrollment->course_id)->toBe($course->id);
    expect($enrollment->user_id)->toBe($user->id);
    expect($enrollment->enrollment_date->toDateString())->toBe(now()->toDateString());
    expect($enrollment->completion_deadline->toDateString())->toBe(now()->addDays(30)->toDateString());
    expect($enrollment->is_steps_created)->toBeFalse();
});

it('casts attributes correctly', function () {
    $user = User::factory()->create();

    // Create required related models
    $courseType = CourseType::create(['name' => 'Test Type']);
    $courseLevel = CourseLevel::create(['name' => 'Test Level']);
    $courseCategory = CourseCategory::create(['name' => 'Test Category']);

    $course = Course::create([
        'name' => 'Test Course',
        'course_type_id' => $courseType->id,
        'course_level_id' => $courseLevel->id,
        'course_category_id' => $courseCategory->id,
    ]);

    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => true,
    ]);

    expect($enrollment->enrollment_date)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($enrollment->completion_deadline)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($enrollment->is_steps_created)->toBeTrue();
});

it('has relationships with course and user', function () {
    $user = User::factory()->create();

    // Create required related models
    $courseType = CourseType::create(['name' => 'Test Type']);
    $courseLevel = CourseLevel::create(['name' => 'Test Level']);
    $courseCategory = CourseCategory::create(['name' => 'Test Category']);

    $course = Course::create([
        'name' => 'Test Course',
        'course_type_id' => $courseType->id,
        'course_level_id' => $courseLevel->id,
        'course_category_id' => $courseCategory->id,
    ]);

    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
    ]);

    expect($enrollment->course)->toBeInstanceOf(Course::class);
    expect($enrollment->user)->toBeInstanceOf(User::class);
    expect($enrollment->course->id)->toBe($course->id);
    expect($enrollment->user->id)->toBe($user->id);
});

it('cascades deletion to enrollment steps', function () {
    // Create a user, course, and enrollment
    $user = User::factory()->create();

    // Create required related models
    $courseType = CourseType::create(['name' => 'Test Type']);
    $courseLevel = CourseLevel::create(['name' => 'Test Level']);
    $courseCategory = CourseCategory::create(['name' => 'Test Category']);

    $course = Course::create([
        'name' => 'Test Course',
        'course_type_id' => $courseType->id,
        'course_level_id' => $courseLevel->id,
        'course_category_id' => $courseCategory->id,
    ]);

    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => true,
    ]);

    // Create enrollment steps
    $step1 = EnrollmentStep::create([
        'enrollment_id' => $enrollment->id,
        'course_id' => $course->id,
        'user_id' => $user->id,
        'stepable_id' => 1,
        'stepable_type' => 'App\\Models\\Lesson',
        'position' => 1,
        'is_completed' => false,
    ]);

    $step2 = EnrollmentStep::create([
        'enrollment_id' => $enrollment->id,
        'course_id' => $course->id,
        'user_id' => $user->id,
        'stepable_id' => 2,
        'stepable_type' => 'App\\Models\\Quiz',
        'position' => 2,
        'is_completed' => false,
    ]);

    // Verify steps were created
    expect(EnrollmentStep::count())->toBe(2);

    // Delete the enrollment
    $enrollment->delete();

    // Verify steps were deleted
    expect(EnrollmentStep::count())->toBe(0);
});

it('cascades deletion to tests and test answers', function () {
    // Create a user, course, and enrollment
    $user = User::factory()->create();

    // Create required related models
    $courseType = CourseType::create(['name' => 'Test Type']);
    $courseLevel = CourseLevel::create(['name' => 'Test Level']);
    $courseCategory = CourseCategory::create(['name' => 'Test Category']);

    $course = Course::create([
        'name' => 'Test Course',
        'course_type_id' => $courseType->id,
        'course_level_id' => $courseLevel->id,
        'course_category_id' => $courseCategory->id,
    ]);

    // Create a lesson
    $lesson = Lesson::create([
        'course_id' => $course->id,
        'name' => 'Test Lesson',
        'position' => 1,
    ]);

    // Create a quiz
    $quiz = Quiz::create([
        'lesson_id' => $lesson->id,
        'name' => 'Test Quiz',
    ]);

    // Create a question
    $question = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Test Question',
    ]);

    // Create a question option
    $option = QuestionOption::create([
        'question_id' => $question->id,
        'option' => 'Test Option',
        'correct' => true,
    ]);

    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => true,
    ]);

    // Create enrollment step
    $step = EnrollmentStep::create([
        'enrollment_id' => $enrollment->id,
        'course_id' => $course->id,
        'user_id' => $user->id,
        'stepable_id' => $quiz->id,
        'stepable_type' => 'App\\Models\\Quiz',
        'position' => 1,
        'is_completed' => false,
    ]);

    // Create test
    $test = Test::create([
        'result' => 80,
        'ip_address' => '127.0.0.1',
        'time_spent' => 300,
        'user_id' => $user->id,
        'enrollment_step_id' => $step->id,
        'quiz_id' => $quiz->id,
    ]);

    // Create test answer
    $testAnswer = TestAnswer::create([
        'correct' => true,
        'user_id' => $user->id,
        'test_id' => $test->id,
        'question_id' => $question->id,
        'option_id' => $option->id,
    ]);

    // Verify records were created
    expect(EnrollmentStep::count())->toBe(1);
    expect(Test::count())->toBe(1);
    expect(TestAnswer::count())->toBe(1);

    // Delete the enrollment
    $enrollment->delete();

    // Verify all related records were deleted
    expect(EnrollmentStep::count())->toBe(0);
    expect(Test::count())->toBe(0);
    expect(TestAnswer::count())->toBe(0);
});

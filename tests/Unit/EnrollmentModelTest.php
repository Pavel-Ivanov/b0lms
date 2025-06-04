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

it('can create a learning plan', function () {
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

    // Create lessons and quizzes for the course
    $lesson1 = Lesson::create([
        'course_id' => $course->id,
        'name' => 'Lesson 1',
        'position' => 1,
    ]);

    $quiz1 = Quiz::create([
        'lesson_id' => $lesson1->id,
        'name' => 'Quiz 1',
    ]);

    $lesson2 = Lesson::create([
        'course_id' => $course->id,
        'name' => 'Lesson 2',
        'position' => 2,
    ]);

    $quiz2 = Quiz::create([
        'lesson_id' => $lesson2->id,
        'name' => 'Quiz 2',
    ]);

    // Create an enrollment with is_steps_created = false
    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => false,
    ]);

    // Verify no steps exist initially
    expect(EnrollmentStep::count())->toBe(0);

    // Call createLearningPlan
    $result = $enrollment->createLearningPlan();

    // Verify the result
    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('План обучения создан');
    expect($result['count'])->toBe(4); // 2 lessons + 2 quizzes
    expect($result['total'])->toBe(4);

    // Verify steps were created
    expect(EnrollmentStep::count())->toBe(4);

    // Verify the enrollment was updated
    $enrollment->refresh();
    expect($enrollment->is_steps_created)->toBeTrue();

    // Verify step details
    $steps = EnrollmentStep::orderBy('position')->get();

    // First step should be Lesson 1
    expect($steps[0]->stepable_id)->toBe($lesson1->id);
    expect($steps[0]->stepable_type)->toBe(Lesson::class);
    expect($steps[0]->position)->toBe(1);

    // Second step should be Quiz 1
    expect($steps[1]->stepable_id)->toBe($quiz1->id);
    expect($steps[1]->stepable_type)->toBe(Quiz::class);
    expect($steps[1]->position)->toBe(2);

    // Third step should be Lesson 2
    expect($steps[2]->stepable_id)->toBe($lesson2->id);
    expect($steps[2]->stepable_type)->toBe(Lesson::class);
    expect($steps[2]->position)->toBe(3);

    // Fourth step should be Quiz 2
    expect($steps[3]->stepable_id)->toBe($quiz2->id);
    expect($steps[3]->stepable_type)->toBe(Quiz::class);
    expect($steps[3]->position)->toBe(4);

    // Test calling createLearningPlan again (should fail because steps already exist)
    $result2 = $enrollment->createLearningPlan();
    expect($result2['success'])->toBeFalse();
    expect($result2['message'])->toBe('План обучения уже создан');
});

it('calculates progress correctly', function () {
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

    // Create an enrollment
    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => true,
    ]);

    // Create 4 enrollment steps
    for ($i = 1; $i <= 4; $i++) {
        EnrollmentStep::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'user_id' => $user->id,
            'stepable_id' => $i,
            'stepable_type' => 'App\\Models\\Lesson',
            'position' => $i,
            'is_completed' => $i <= 2, // Mark first 2 steps as completed
        ]);
    }

    // Calculate progress
    $progress = $enrollment->progress();

    // Verify progress calculation
    expect($progress['value'])->toBe(2); // 2 completed steps
    expect($progress['max'])->toBe(4); // 4 total steps
    expect($progress['percentage'])->toBe(50); // 50% completion

    // Mark another step as completed
    EnrollmentStep::where('enrollment_id', $enrollment->id)
        ->where('position', 3)
        ->update(['is_completed' => true]);

    // Recalculate progress
    $progress = $enrollment->progress();

    // Verify updated progress
    expect($progress['value'])->toBe(3); // 3 completed steps
    expect($progress['max'])->toBe(4); // 4 total steps
    expect($progress['percentage'])->toBe(75); // 75% completion
});

it('checks if enrollment has steps', function () {
    // Create a user, course, and enrollment with is_steps_created = false
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
        'is_steps_created' => false,
    ]);

    // Verify hasSteps() returns false when is_steps_created is false
    expect($enrollment->hasSteps())->toBeFalse();

    // Update enrollment to set is_steps_created to true
    $enrollment->update(['is_steps_created' => true]);
    $enrollment->refresh();

    // Verify hasSteps() returns true when is_steps_created is true
    expect($enrollment->hasSteps())->toBeTrue();
});

it('formats enrollment info correctly', function () {
    // Create a user with a specific name
    $user = User::factory()->create([
        'name' => 'Test User'
    ]);

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

    // Create an enrollment with specific dates
    $enrollmentDate = '2023-01-15';
    $completionDeadline = '2023-02-15';

    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => $enrollmentDate,
        'completion_deadline' => $completionDeadline,
        'is_steps_created' => false,
    ]);

    // Expected format: "User Name / DD.MM.YYYY - DD.MM.YYYY"
    $expectedInfo = 'Test User / 15.01.2023 - 15.02.2023';

    // Verify enrollmentInfo() returns the correctly formatted string
    expect($enrollment->enrollmentInfo())->toBe($expectedInfo);
});

it('retrieves completed steps correctly', function () {
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

    // Create an enrollment
    $enrollment = Enrollment::create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'enrollment_date' => now(),
        'completion_deadline' => now()->addDays(30),
        'is_steps_created' => true,
    ]);

    // Create 5 enrollment steps with different completion statuses
    $stepData = [
        ['position' => 1, 'is_completed' => true],
        ['position' => 2, 'is_completed' => false],
        ['position' => 3, 'is_completed' => true],
        ['position' => 4, 'is_completed' => false],
        ['position' => 5, 'is_completed' => true],
    ];

    foreach ($stepData as $data) {
        EnrollmentStep::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'user_id' => $user->id,
            'stepable_id' => $data['position'],
            'stepable_type' => 'App\\Models\\Lesson',
            'position' => $data['position'],
            'is_completed' => $data['is_completed'],
        ]);
    }

    // Get completed steps
    $completedSteps = $enrollment->completedSteps()->get();

    // Verify the count of completed steps
    expect($completedSteps)->toHaveCount(3);

    // Verify the positions of the completed steps
    $completedPositions = $completedSteps->pluck('position')->toArray();
    expect($completedPositions)->toBe([1, 3, 5]);

    // Mark another step as completed
    EnrollmentStep::where('enrollment_id', $enrollment->id)
        ->where('position', 2)
        ->update(['is_completed' => true]);

    // Get updated completed steps
    $updatedCompletedSteps = $enrollment->completedSteps()->get();

    // Verify the updated count
    expect($updatedCompletedSteps)->toHaveCount(4);

    // Verify the updated positions
    $updatedCompletedPositions = $updatedCompletedSteps->pluck('position')->toArray();
    expect($updatedCompletedPositions)->toBe([1, 2, 3, 5]);
});

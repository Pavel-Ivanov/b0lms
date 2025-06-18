<?php

namespace App\Livewire;

use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\TestAnswer;
use App\States\StateFactory;
use App\States\TestManagerState;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class TestManager extends Component implements HasForms
{
    use InteractsWithForms;
    public Quiz $quiz;
    public Enrollment $enrollment;
    public EnrollmentStep $enrollmentStep;
    public ?Test $userTestAttempt = null;
    public Collection $questions;
    public array $userAnswers = [];
    public string $currentStateName = 'initial_screen';
    public int $currentQuestionIndex = 0;
    public array $correctAnswers = [];
    public ?array $data = [];
    public int $currentAttempt = 0;
    protected ?TestManagerState $currentState = null;

    public function form(Form $form): Form
    {
        $steps = [];
        $i = 1;

        $questions = $this->quiz->questions()->with('questionOptions')->get()->toArray();
        foreach ($questions as $question) {
            $steps[] = Step::make('Вопрос ' . $i)
                ->schema([
                    Radio::make($question['id'])
                        ->label(fn() => new HtmlString(
                            '<span class="font-bold">' . $question['question_text'] . '</span>' .
                            (!empty($question['hint']) ? '<br><span class="text-gray-500">Подсказка: ' . $question['hint'] . '</span>' : '')
                        ))
                        ->options(
                            collect($question['question_options'] ?? [])
                                ->mapWithKeys(fn($answer) => [$answer['id'] => $answer['option']])
                                ->toArray()
                        )
                        ->required(),
                ]);
            $i++;
        }

        return $form
            ->schema([
                Wizard::make($steps)
                    ->submitAction(new HtmlString(Blade::render(
                        <<<BLADE
                    <x-filament::button type="submit" size="sm">
                        Отправить результаты
                    </x-filament::button>
BLADE
                    ))),
            ])
            ->statePath('data')
            ->model($this->quiz);
    }

    public function mount(Quiz $quiz, Enrollment $enrollment, EnrollmentStep $enrollmentStep)
    {
        $this->quiz = $quiz;
        $this->enrollment = $enrollment;
        $this->enrollmentStep = $enrollmentStep;
        $this->questions = $this->quiz->questions()->with('questionOptions')->get();
        $this->correctAnswers = $this->questions
            ->mapWithKeys(fn (Question $question) => [$question->id => $question->correctQuestionOption()->id])
            ->toArray();

        $this->findTestAttempt();
        $this->determineInitialState();
        $this->form->fill();
    }

    /**
     * Initialize the state object based on the current state name
     */
    protected function initializeState(): void
    {
        $this->currentState = StateFactory::createState($this->currentStateName, $this);
        $this->currentState->enter();
    }

    /**
     * Determine the initial state based on the enrollment step and attempt count
     */
    protected function determineInitialState(): void
    {
        // If the enrollment step is already completed, go to final state
        if ($this->enrollmentStep->is_completed) {
            $this->currentStateName = 'final';
        }
        // Check if the user has reached the maximum number of attempts
        elseif ($this->currentAttempt >= $this->quiz->max_attempts) {
            $this->currentStateName = 'final';
        }
        // Otherwise, go to initial screen
        else {
            $this->currentStateName = 'initial_screen';
        }

        // Initialize the state object
        $this->initializeState();
    }

    /**
     * Transition to a new state
     */
    public function transitionTo(string $stateName): void
    {
        $this->currentStateName = $stateName;
        $this->initializeState();
    }

    protected function findTestAttempt(): void
    {
        // Find the latest test record for this user, quiz, and enrollment step
        $this->userTestAttempt = Test::where('user_id', auth()->id())
            ->where('quiz_id', $this->quiz->id)
            ->where('enrollment_step_id', $this->enrollmentStep->id)
            ->latest()
            ->first();

        // Initialize currentAttempt based on the latest completed test record
        if ($this->userTestAttempt && $this->userTestAttempt->status === 'completed') {
            $this->currentAttempt = $this->userTestAttempt->current_attempt;
        } else {
            $this->currentAttempt = 0;
        }

        // If a test record exists with answers but no status, load its answers
        if ($this->userTestAttempt && !$this->userTestAttempt->status && !empty($this->userTestAttempt->answers)) {
            $this->userAnswers = $this->userTestAttempt->answers;
        }
    }

    // The determineState method has been replaced by the state pattern implementation

    public function startTest(): void
    {
        $this->transitionTo('test_form');
    }

    public function submit(): void
    {
        $result = 0;
        $startTime = now();

        // Create a new test record for this attempt
        $newTest = Test::create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->quiz->id,
            'enrollment_step_id' => $this->enrollmentStep->id,
            'result' => 0,
            'ip_address' => request()->ip(),
            'time_spent' => 0,
            'status' => 'pending',
            'current_attempt' => $this->currentAttempt,
            'attempt_number' => $this->currentAttempt,
            'passed' => false,
            'started_at' => $startTime,
            'answers' => [],
        ]);

        // Set the userTestAttempt property
        $this->userTestAttempt = $newTest;

        foreach ($this->data as $questionId => $userAnswer) {
            $userAnswer = (int) $userAnswer;
            $correctAnswer = (int) ($this->correctAnswers[$questionId] ?? 0);
            $isCorrect = $userAnswer === $correctAnswer ? 1 : 0;

            $result += $isCorrect;

            $this->userAnswers[$questionId] = $userAnswer;

            // Create a new TestAnswer record
            TestAnswer::create([
                'test_id' => $this->userTestAttempt->id,
                'question_id' => $questionId,
                'user_id' => auth()->id(),
                'option_id' => $userAnswer,
                'correct' => $isCorrect,
            ]);
        }

        // Update the test record with the answers
        $this->userTestAttempt->update([
            'answers' => $this->userAnswers,
        ]);

        $this->transitionTo('result');
    }

    // These methods are no longer needed with the Wizard form

    public function finishTest(): void
    {
        $this->submit();
    }

    public function calculateScoreAndFinishAttempt(): void
    {
        $score = 0;
        $totalQuestions = count($this->questions);

        foreach ($this->userAnswers as $questionId => $optionId) {
            $optionId = (int) $optionId;
            $correctOptionId = (int) ($this->correctAnswers[$questionId] ?? 0);
            if ($optionId === $correctOptionId) {
                $score++;
            }
        }

        $scorePercentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
        $passed = $scorePercentage >= $this->quiz->passing_percentage;

        // Update the current test record with the score and status
        $this->userTestAttempt->update([
            'result' => $score,
            'status' => 'completed',
            'passed' => $passed,
            'completed_at' => now(),
            'time_spent' => now()->diffInSeconds($this->userTestAttempt->started_at),
        ]);

        if ($passed) {
            // Mark the enrollment step as completed
            $this->enrollmentStep->markAsCompleted();
            $this->enrollmentStep->completed_at = now();
            $this->enrollmentStep->save();

            // Enable next step if available
            $nextStep = $this->enrollment->steps()
                ->where('position', '>', $this->enrollmentStep->position)
                ->orderBy('position')
                ->first();

            if ($nextStep) {
                $nextStep->enable();
                $nextStep->save();
            }

            // Dispatch events for UI updates
            $this->dispatch('enrollment-step-completed');
            $this->dispatch('enrollment-navigation-update');

            Notification::make()
                ->title('Тест успешно пройден!')
                ->success()
                ->send();
        } else {
            $message = 'Тест не пройден. Необходимо набрать минимум ' . $this->quiz->passing_percentage . '% правильных ответов.';

            Notification::make()
                ->title($message)
                ->color('danger')
                ->send();
        }
    }

    public function retakeTest(): void
    {
        // Check if the user has attempts left and the enrollment step is not completed
        if ($this->currentAttempt < $this->quiz->max_attempts && !$this->enrollmentStep->is_completed) {
            $this->transitionTo('test_form');
        } else {
            Notification::make()
                ->title('Вы исчерпали все попытки или уже прошли тест.')
                ->warning()
                ->send();
        }
    }

    /**
     * Get the total number of questions
     */
    public function getTotalQuestionsProperty()
    {
        return count($this->questions);
    }

    /**
     * Get the total number of questions (accessor for state objects)
     */
    public function getTotalQuestions(): int
    {
        return $this->getTotalQuestionsProperty();
    }

    public function showFinalState()
    {
        $this->transitionTo('final');
    }

    /**
     * Initialize the state object after component rehydration
     */
    public function hydrate(): void
    {
        // Initialize the state object after rehydration
        $this->initializeState();
    }

    public function render()
    {
        // Initialize the state object if it's not set
        if ($this->currentState === null) {
            $this->initializeState();
        }

        // Check if we need to transition to another state
        $nextState = $this->currentState->shouldTransition();
        if ($nextState !== null) {
            $this->transitionTo($nextState);
        }

        // Render the current state
        return view('livewire.test-manager', [
            'currentState' => $this->currentStateName,
            'stateView' => $this->currentState->render(),
        ]);
    }
}

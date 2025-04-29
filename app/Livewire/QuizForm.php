<?php

namespace App\Livewire;

use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\TestAnswer;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class QuizForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Enrollment $enrollment;
    public EnrollmentStep $activeStep;
    public Quiz $quiz;
    public Collection $questions;
    public ?array $data = [];
    public array $correctAnswers = [];

    public bool $completed = false;
    public ?string $message = null;
    public ?string $state = null;
    public int $startTimeSeconds = 0;
    public ?Test $latestTest = null;


    public function mount(Quiz $quiz, Enrollment $enrollment, EnrollmentStep $activeStep)
    {
        $this->quiz = $quiz;
        $this->enrollment = $enrollment;
        $this->activeStep = $activeStep;
        $this->questions = $this->quiz->questions()->with('questionOptions')->get();
        $this->correctAnswers = $this->questions
            ->mapWithKeys(fn (Question $question) => [$question->id => $question->correctQuestionOption()->id])
            ->toArray();
        $this->startTimeSeconds = now()->timestamp;
        $this->latestTest = $this->activeStep->tests()->latest()->first();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $steps = [];
        $i = 1;

        $questions = $this->quiz->questions()->with('questionOptions')->get()->toArray();
        foreach ($questions as $question) {
            $steps[] = Step::make('Вопрос ' . $i)
                ->schema([
                    Radio::make($question['id'])
                        ->label($question['question_text'])
                        ->options(
                            collect($question['question_options'] ?? [])
                                ->mapWithKeys(fn ($answer) => [$answer['id'] => $answer['option']])
                                ->toArray()
                        )
                        ->required()
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
            ->model($this->quiz); // Можно попробовать привязать к модели Quiz, если это имеет смысл
    }

    public function submit(): void
    {
        $result = 0;

        $test = Test::create([
            'result'     => 0,
            'ip_address' => request()->ip(),
            'time_spent' => now()->timestamp - $this->startTimeSeconds,
            'user_id'    => auth()->id(),
            'enrollment_step_id' => $this->activeStep->id,
            'quiz_id'    => $this->quiz->id,
        ]);

        foreach ($this->data as $questionId => $userAnswer) {
            $correctAnswer = $this->correctAnswers[$questionId] ?? null;
            $isCorrect = ($correctAnswer !== null && $userAnswer == $correctAnswer) ? 1 : 0;
            $result += $isCorrect;

            TestAnswer::create([
                'correct'     => $isCorrect,
                'user_id'     => auth()->id(),
                'test_id'     => $test->id,
                'question_id' => $questionId,
                'option_id'   => $userAnswer,
            ]);
        }

        $test->update(['result' => $result]);

        $this->activeStep->update(['is_completed' => true]);

        $count = count(array_diff($this->correctAnswers, $this->data));
        $this->completed = true;
        $this->state = $count === 0 ? 'success' : 'failure';
        $this->message = $count === 0 ? 'Вы ответили правильно на все вопросы!' : 'У Вас ' . $count . ' неправильных ответов!';

        // Если хотите вернуться на страницу результатов, можно использовать Livewire's $this->redirect(route('intern.enrollment.results', ['enrollment' => $this->enrollment->id]))'
        // Если хотите вернуться на предыдущую страницу, можно использовать Laravel's redirect()->back() или Livewire's $this->redirect()->back()
//        $this->redirect(request()->header('Referer'));
    }

    public function viewDetails()
    {
/*        $this->form->fill([
            'message' => 'Здесь могут быть подробные результаты теста',
        ]);*/
        $this->dispatch('open-modal', 'quiz-details');
    }

/*    protected function getFormSchema(): array
    {
        return [
            TextInput::make('message')
                ->label('Сообщение')
                ->default('Это детали вашего теста')
                ->disabled(),
            // Добавьте здесь другие компоненты формы, если нужно
        ];
    }*/

    public function resetResults()
    {
        // Сбрасываем состояние компонента
        $this->completed = false;
        $this->message = null;
        $this->state = null;
        $this->data = [];
        $this->startTimeSeconds = 0;

        // Обновляем статус шага в enrollment, если это необходимо
        // Возможно, вы захотите оставить его завершенным, если хотя бы одна попытка была успешной
         $this->activeStep->update(['is_completed' => false]);

        // Перезагружаем форму
        $this->form->fill();

        // Отправляем событие для обновления интерфейса
        $this->dispatch('quiz-reset');

        // Отправляем событие для обновления EnrollmentNavigation
        $this->dispatch('enrollment-navigation-update');
//        $this->dispatch('$refresh')->to('enrollment-navigation');

        // Обновляем текущий компонент
        $this->render();
    }

    public function render()
    {
        return view('livewire.quiz-form');
    }
}

<?php

namespace App\Livewire;

use App\Models\Enrollment;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\TestAnswer;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class QuizForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Enrollment $enrollment;
    public Quiz $quiz;
    public Collection $questions;
    public ?array $data = [];
    public array $correctAnswers = [];
    public bool $completed = false;
    public ?string $message = null;
    public ?string $state = null;
    public int $startTimeSeconds = 0;


    public function mount(Quiz $quiz, Enrollment $enrollment)
    {
        $this->quiz = $quiz;
        $this->enrollment = $enrollment;
        $this->questions = $this->quiz->questions()->with('questionOptions')->get();
        $this->correctAnswers = $this->questions
            ->mapWithKeys(fn (Question $question) => [$question->id => $question->correctQuestionOption()->id])
            ->toArray();
        $this->startTimeSeconds = now()->timestamp;
        $this->form->fill();
//        dump($this->form);
//        dump($this->quiz, $this->enrollment, $this->questions, $this->correctAnswers);
    }

    public function form(Form $form): Form
    {
        $steps = [];
        $i = 1;

//        $questions = $this->questions->toArray();
        $questions = $this->quiz->questions()->with('questionOptions')->get()->toArray();
        foreach ($questions as $question) {
//            if ($i === 2) {
//                dump($question);
//            }
//            dump($question['question_options']);
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
        // Перенесите сюда логику из submit() метода QuizView
        $result = 0;

        $test = Test::create([
            'result'     => 0,
            'ip_address' => request()->ip(),
            'time_spent' => now()->timestamp - $this->startTimeSeconds,
            'user_id'    => auth()->id(),
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

        $count = count(array_diff($this->correctAnswers, $this->data));
        $this->completed = true;
        $this->state = $count === 0 ? 'success' : 'failure';
        $this->message = $count === 0 ? 'Вы ответили правильно на все вопросы!' : 'У Вас ' . $count . ' неправильных ответов!';
    }

    public function render()
    {
        return view('livewire.quiz-form');
    }
}

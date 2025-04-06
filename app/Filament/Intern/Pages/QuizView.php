<?php

namespace App\Filament\Intern\Pages;

use App\Models\Enrollment;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\TestAnswer;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Relations\HasMany as Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class QuizView extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';
    protected static string $view = 'filament.intern.pages.quiz-view';
    protected static ?string $slug = 'quizzes';

    public Model | int | string | null $record;
    public Enrollment $enrollment;

    public Collection $questions;
    public ?array $data = [];   // массив для хранения ответов Пользователя
    public array $correctAnswers = [];   // массив для хранения Правильных ответов
    public bool $completed = false;
    public ?string $message = null;
    public ?string $state = null;
    public int $startTimeSeconds = 0;

//    public array $questionsAnswers = [];
//    public Collection $questions1;


    public function mount(int|string $record)
    {
        $this->record = Quiz::findOrFail($record);
        abort_if(! $this->record->is_published, 404);

        $this->enrollment = Enrollment::where('course_id', $this->record->lesson->course->id)
            ->where('user_id', auth()->id())
            ->with('steps')
            ->firstOrFail();

        $this->questions = Question::where('quiz_id', $this->record->id)->with('questionOptions')->get();

        $this->correctAnswers = $this->questions
            ->mapWithKeys(function (Question $question) {
//                return [$question->id => $question->questionOptions->where('correct', true)->first()->id];
                return [$question->id => $question->correctQuestionOption()->id];
            })->toArray();

/*        $this->questions1 = Question::query()
            ->inRandomOrder()
            ->whereRelation('quiz', 'id', $this->record->id)
            ->with(['questionOptions' => fn (Builder $query) => $query->inRandomOrder()])
            ->get();*/

/*        for($i = 0; $i < $this->questionsCount(); $i++) {
            $this->questionsAnswers[$i] = null;
        }*/

        $this->startTimeSeconds = now()->timestamp;

//        dump($this->questions);
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        $steps = [];
        $i = 1;

        $questions = Question::where('quiz_id', $this->record->id)->with('questionOptions')->get()->toArray();

//        dump($questions);
        foreach ($questions as $question) {
            $steps[] = Wizard\Step::make('Вопрос ' . $i)
                ->schema([
                    Radio::make($question['id'])
                        ->label($question['question_text'])
                        ->options(
                            collect($question['question_options'])
                                ->mapWithKeys(function ($answer) {
                                    return [$answer['id'] => $answer['option']];
                                })->toArray()
                        )
                        ->required()
                ]);
            $i++;
        }
//        dump($steps);
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
            ->statePath('data');
    }

    public function submit(): void
    {
        $result = 0;

        $test = Test::create([
            'result'     => 0,
            'ip_address' => request()->ip(),
            'time_spent' => now()->timestamp - $this->startTimeSeconds,
            'user_id'    => auth()->id(),
            'quiz_id'    => $this->record->id,
        ]);

        foreach ($this->data as $questionId => $userAnswer) {
            // Получаем правильный ответ для текущего question_id
            $correctAnswer = $this->correctAnswers[$questionId] ?? null;

            $isCorrect = ($correctAnswer !== null && $userAnswer == $correctAnswer) ? 1 : 0;
            $result += $isCorrect;

            // Сохраняем результат в таблицу с помощью модели TestAnswer
            TestAnswer::create([
                'correct'     => $isCorrect,     // Результат: 1 — правильный, 0 — неправильный
                'user_id'     => auth()->id(),  // ID пользователя
                'test_id'     => $test->id,     // ID текущего теста
                'question_id' => $questionId,   // ID вопроса
                'option_id'   => $userAnswer,   // ID ответа, который выбрал пользователь
            ]);
        }

        $test->update([
            'result' => $result,
        ]);

        // Подсчет количества правильных ответов
        $count = count(array_diff($this->correctAnswers, $this->data));
        $this->completed = true;

        if ($count === 0) {
            $this->state = 'success';
            $this->message = 'Вы ответили правильно на все вопросы!';
        } else {
            $this->state = 'failure';
            $this->message = 'У Вас ' . $count . ' неправильных ответов!';
        }

    }

    public function questionsCount(): int
    {
        return $this->questions->count();
    }

    public static function getRoutePath(): string
    {
        return '/' . static::getSlug() . '/{record}';
    }

}

<?php

namespace App\Infolists\Components;

use App\Filament\Resources\ResultResource;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\TestAnswer;
use Filament\Infolists\Components\Component;
use App\Models\Lesson;
use App\Models\Question;
use Filament\Infolists\Components\Concerns\HasName;
use Illuminate\Support\Collection;

class ListQuestions extends Component
{
    use HasName;

    protected string $view = 'infolists.components.list-questions';

    protected Lesson $lesson;
    protected Collection $questions;
    public Question $currentQuestion;
    public array $questionsAnswers = [];

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function lesson($lesson)
    {
        $this->lesson  = $lesson;
        $this->questions = $lesson->questions;

        return $this;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

/*    public function submit(): void
    {
        $result = 0;

        $test = Test::create([
            'user_id'    => auth()->id(),
            'quiz_id'    => $this->record->id,
            'result'     => 0,
            'ip_address' => request()->ip(),
            'time_spent' => now()->timestamp - $this->startTimeSeconds,
        ]);

        foreach ($this->questionsAnswers as $key => $option) {
            info($option);
            $status = 0;

            if (! empty($option) && QuestionOption::find($option)->correct) {
                $status = 1;
                $result++;
            }

            TestAnswer::create([
                'user_id'     => auth()->id(),
                'test_id'     => $test->id,
                'question_id' => $this->questions[$key]->id,
                'option_id'   => $option ?? null,
                'correct'     => $status,
            ]);
        }

        $test->update([
            'result' => $result,
        ]);

        $this->redirectIntended(ResultResource::getUrl('view', ['record' => $test]));
    }*/


}

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




}

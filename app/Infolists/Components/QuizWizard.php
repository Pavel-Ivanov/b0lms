<?php

namespace App\Infolists\Components;

use App\Models\Quiz;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Concerns\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class QuizWizard extends Component implements HasForms
{
    use HasName;
    use InteractsWithForms;

    protected string $view = 'infolists.components.quiz-wizard';

    protected Quiz $quiz;
    protected $questions;
    public ?array $data = [];

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function setQuiz(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->questions = $quiz->questions->toArray();
        $this->data = array_fill_keys(array_keys($this->questions), null);
//        dump(array_keys($this->questions));
//        $this->form->fill();

        return $this;
    }

/*    public function mount(Quiz $quiz): void
    {
        $this->quiz = $quiz;
        $this->questions = $quiz->questions;
        $this->data = array_fill_keys(array_keys($this->questions), null);
        $this->form->fill();
    }*/

    public function form(Wizard $form): Wizard
    {
        $steps = [];
/*        foreach ($this->questions as $index => $question) {
            $stepSchema = [];

            $options = collect($question['data']['options'])->pluck('text', 'option')->toArray();
            $stepSchema[] = Radio::make("answers.{$index}")
                ->label($question['question_text'])
                ->options($options)
                ->required();

            $steps[] = Wizard\Step::make("Вопрос " . ($index + 1))
                ->schema($stepSchema);
        }*/

        $steps[] = Wizard\Step::make('Завершение')
            ->schema([
                \Filament\Forms\Components\Placeholder::make('final_message')
                    ->content('Пожалуйста, проверьте свои ответы перед завершением теста.'),
            ]);

        return $form
            ->schema($steps)
            ->startOnStep(1)
            ->statePath('data.answers');

//        return $form;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function getData()
    {
        return $this->data;
    }

}

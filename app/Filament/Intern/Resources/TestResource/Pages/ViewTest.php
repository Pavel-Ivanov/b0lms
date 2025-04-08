<?php

namespace App\Filament\Intern\Resources\TestResource\Pages;

use App\Filament\Intern\Resources\TestResource;
use App\Models\Test;
use Filament\Actions;
use Filament\Infolists;
use App\Models\TestAnswer;
use App\Models\QuestionOption;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Filament\Resources\Pages\ViewRecord;

class ViewTest extends ViewRecord
{
    protected static string $resource = TestResource::class;

    public function mount(int|string $record): void
    {
        dump($record);
        parent::mount($record);

        $this->record->load('testAnswers.question.questionOptions');

//        $this->authorizeAccess();

/*        if (! $this->hasInfolist()) {
            $this->fillForm();
        }*/
    }

/*    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }*/

    public function infolist(Infolist $infolist): Infolist
    {
        dump('111');
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->columns(1)
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->inlineLabel()
                            ->label('Date'),
                        Infolists\Components\TextEntry::make('result')
                            ->inlineLabel()
                            ->formatStateUsing(function (Test $record) {
                                return $record->result . '/' . $record->questions->count() . ' (time: ' . (int)($record->time_spent / 60) . ':' . gmdate('s', $record->time_spent) . ' minutes)';
                            }),
                    ]),

/*                Infolists\Components\RepeatableEntry::make('testAnswers')
                    ->label('Questions')
                    ->columnSpanFull()
                    ->schema([
                        Infolists\Components\TextEntry::make('question.question_text')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->weight(FontWeight::Bold),
                        Infolists\Components\RepeatableEntry::make('question.questionOptions')
                            ->inlineLabel()
                            ->contained(false)
                            ->schema([
                                Infolists\Components\TextEntry::make('option')
                                    ->html()
                                    ->hiddenLabel()
                                    ->weight(fn (QuestionOption $record) => $record->correct ? FontWeight::Bold : null)
                                    ->formatStateUsing(function (QuestionOption $record) {
                                        $answer = static::getRecord()->testAnswers->firstWhere(function (TestAnswer $value) use ($record) {
                                            return $value->question_id === $record->question_id;
                                        });

                                        return $record->option . ' ' .
                                            ($record->correct ? new HtmlString('<span class="italic">(correct answer)</span>') : null) . ' ' .
                                            ($answer->option_id == $record->id ? new HtmlString('<span class="italic">(your answer)</span>') : null);
                                    }),
                            ]),
                        Infolists\Components\TextEntry::make('question.code_snippet')
                            ->inlineLabel()
                            ->label('Code Snippet')
                            ->visible(fn (?string $state): bool => ! is_null($state))
                            ->formatStateUsing(fn ($state) => new HtmlString('<pre class="border-gray-100 bg-gray-50 p-2">' . htmlspecialchars($state) . '</pre>')),
                        Infolists\Components\TextEntry::make('question.more_info_link')
                            ->inlineLabel()
                            ->label('More Information')
                            ->url(fn (?string $state): string => $state)
                            ->visible(fn (?string $state): bool => ! is_null($state)),
                    ]),*/
            ]);
    }
}

<?php

namespace App\Filament\Intern\Resources;

//use App\Filament\Resources\TestResource\Pages;
//use App\Filament\Resources\TestResource\RelationManagers;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\TestAnswer;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->inlineLabel()
                            ->label('Дата'),
                        TextEntry::make('result')
                            ->inlineLabel()
                            ->label('Результат')
                            ->formatStateUsing(function (Test $record) {
                                return $record->result . '/' . $record->questions->count() . ' (время: ' . (int)($record->time_spent / 60) . ':' . gmdate('s', $record->time_spent) . ' минут)';
                            }),
                    ]),

                    RepeatableEntry::make('testAnswers')
                        ->label('Вопросы и ответы')
                        ->columnSpanFull()
                        ->schema([
                            TextEntry::make('question.question_text')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->weight(FontWeight::Bold),
                            RepeatableEntry::make('question.questionOptions')
                                ->inlineLabel()
                                ->label('Ответы')
                                ->contained(false)
                                ->schema([
                                    TextEntry::make('option')
                                        ->html()
                                        ->hiddenLabel()
                                        ->weight(fn (QuestionOption $record) => $record->correct ? FontWeight::Bold : null)

                                        ->formatStateUsing(function (QuestionOption $recordOption) {
                                            // нам нужна запись Test, чтобы получить ответы Пользователя на этот вопрос
                                            $answers = static::record;
dump($answers);
                                            $answer = $answers->firstWhere(function (TestAnswer $value) use ($recordOption) {
                                                return $value->question_id === $recordOption->question_id;
                                            });

                                            return $recordOption->option . ' ' .
                                                ($recordOption->correct ? new HtmlString('<span class="italic">(correct answer)</span>') : null) . ' ' .
                                                ($answer->option_id == $recordOption->id ? new HtmlString('<span class="italic">(your answer)</span>') : null);
                                        })
                                ])
                            ,
                            TextEntry::make('question.code_snippet')
                                ->inlineLabel()
                                ->label('Code Snippet')
                                ->visible(fn (?string $state): bool => ! is_null($state))
                                ->formatStateUsing(fn ($state) => new HtmlString('<pre class="border-gray-100 bg-gray-50 p-2">' . htmlspecialchars($state) . '</pre>')),
                            TextEntry::make('question.more_info_link')
                                ->inlineLabel()
                                ->label('More Information')
                                ->url(fn (?string $state): string => $state)
                                ->visible(fn (?string $state): bool => ! is_null($state)),
                        ]),
            ])
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Intern\Resources\TestResource\Pages\ListTests::route('/'),
//            'create' => \App\Filament\Intern\Resources\TestResource\Pages\CreateTest::route('/create'),
//            'edit' => \App\Filament\Intern\Resources\TestResource\Pages\EditTest::route('/{record}/edit'),
            'view' => \App\Filament\Intern\Resources\TestResource\Pages\ViewTest::route('/{record}'),
        ];
    }
}

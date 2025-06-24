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
                            TextEntry::make('option_id')
                                ->label('Ответ')
                                ->html()
                                ->formatStateUsing(function ($state, $record) {
                                    // Проверяем, есть ли ответ пользователя
                                    if (!$state) {
                                        return new HtmlString('<span class="text-gray-500">Нет ответа</span>');
                                    }

                                    // Получаем выбранный вариант ответа
                                    $option = QuestionOption::find($state);
                                    if (!$option) {
                                        return new HtmlString('<span class="text-gray-500">Ответ не найден</span>');
                                    }

                                    // Определяем, правильный ли ответ
                                    $isCorrect = $option->correct;

                                    // SVG иконки для правильного и неправильного ответов
                                    $correctIcon = '<svg class="inline-block h-5 w-5 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>';

                                    $incorrectIcon = '<svg class="inline-block h-5 w-5 text-red-600 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>';

                                    // Формируем HTML для ответа
                                    $html = '<div class="flex items-start">';
                                    $html .= $isCorrect ? $correctIcon : $incorrectIcon;
                                    $html .= '<div>';
                                    $html .= '<div class="font-medium ' . ($isCorrect ? 'text-green-700' : 'text-red-700') . '">' . $option->option . '</div>';

                                    // Добавляем объяснение, если оно есть
                                    if ($option->rationale) {
                                        $html .= '<p class="mt-1 text-xs text-gray-500">' . $option->rationale . '</p>';
                                    }

                                    $html .= '</div></div>';

                                    return new HtmlString($html);
                                })
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

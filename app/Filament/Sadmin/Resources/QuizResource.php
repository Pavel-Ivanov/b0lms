<?php

namespace App\Filament\Sadmin\Resources;

use App\Filament\Sadmin\Resources\QuizResource\Pages;
use App\Filament\Sadmin\Resources\QuizResource\RelationManagers;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;
//    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Тест';
    protected static ?string $pluralModelLabel = 'Тесты';
    protected static ?string $navigationGroup = 'Академия';
    protected static ?string $navigationLabel = 'Тесты';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                Forms\Components\Select::make('lesson_id')
                                    ->label('Урок')
                                    ->relationship('lesson', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Название теста')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Описание теста')
                                    ->columnSpanFull(),
                                Forms\Components\Checkbox::make('is_published')
                                    ->label('Опубликован'),
                            ]),
                        Tabs\Tab::make('Вопросы')
                            ->schema([
                                Forms\Components\Repeater::make('questions')
                                    ->hiddenLabel()
                                    ->relationship('questions')
                                    ->schema([
                                        Forms\Components\Textarea::make('question_text')
                                            ->label('Текст вопроса')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\Repeater::make('questionOptions')
                                            ->label('Ответы')
                                            ->required()
                                            ->relationship()
                                            ->columnSpanFull()
                                            ->schema([
                                                Forms\Components\Textarea::make('option')
                                                    ->label('Ответ')
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Forms\Components\Textarea::make('rationale')
                                                    ->label('Объяснение ответа')
                                                    ->nullable()
                                                    ->columnSpanFull(),
                                                Forms\Components\Checkbox::make('correct')
                                                    ->label('Правильный ответ'),
                                            ])
                                            ->itemLabel(function (array $state): ?string {
                                                return $state['option'] ?? '';
                                            })
                                            ->columns()
                                            ->addActionLabel('Добавить ответ')
//                                            ->reorderable(true)
//                                            ->reorderableWithButtons()
//                                            ->cloneable()
                                            ->collapsible()
                                            ->collapsed()
                                            ->deleteAction(function (\Filament\Forms\Components\Actions\Action $action) {
                                                return $action
                                                    ->action(function (array $arguments, \Filament\Forms\Components\Repeater $component) {
                                                        $item = $arguments['item'];
                                                        $record = null;

                                                        // Get the record from the database if it exists
                                                        if (isset($component->getItemState($item)['id'])) {
                                                            $recordId = $component->getItemState($item)['id'];
                                                            $record = \App\Models\QuestionOption::find($recordId);
                                                        }

                                                        // Check if the record has related TestAnswer records
                                                        if ($record && $record->testAnswers()->count() > 0) {
                                                            \Filament\Notifications\Notification::make()
                                                                ->danger()
                                                                ->title('Невозможно удалить ответ')
                                                                ->body('Этот ответ используется в ответах тестов и не может быть удален.')
                                                                ->send();
                                                            return;
                                                        }

                                                        // If no related records, proceed with deletion
                                                        $state = $component->getState();
                                                        unset($state[$item]);
                                                        $component->state($state);
                                                    });
                                            }),
                                        Forms\Components\Textarea::make('hint')
                                            ->label('Подсказка')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('more_info_link')
                                            ->label('Ссылка на дополнительную информацию')
                                            ->columnSpanFull(),
                                    ])
                                    ->itemLabel(function (array $state): ?string {
                                        if (empty($state['question_text'])) {
                                            return '';
                                        }
                                        return $state['question_text'];
                                    })
                                    ->columns()
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Добавить вопрос')
                                    ->defaultItems(0)
                                    ->deleteAction(function (\Filament\Forms\Components\Actions\Action $action) {
                                        return $action
                                            ->action(function (array $arguments, \Filament\Forms\Components\Repeater $component) {
                                                $item = $arguments['item'];
                                                $record = null;

                                                // Get the record from the database if it exists
                                                if (isset($component->getItemState($item)['id'])) {
                                                    $recordId = $component->getItemState($item)['id'];
                                                    $record = \App\Models\Question::find($recordId);
                                                }

                                                // Check if the record has related TestAnswer records
                                                if ($record && $record->testAnswers()->count() > 0) {
                                                    \Filament\Notifications\Notification::make()
                                                        ->danger()
                                                        ->title('Невозможно удалить вопрос')
                                                        ->body('Этот вопрос используется в ответах тестов и не может быть удален.')
                                                        ->send();
                                                    return;
                                                }

                                                // If no related records, proceed with deletion
                                                $state = $component->getState();
                                                unset($state[$item]);
                                                $component->state($state);
                                            });
                                    }),
                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lesson.name')
                    ->label('Название Урока')
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название Теста')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Кол-во вопросов')
                    ->counts('questions'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликован')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('lesson')
                    ->label('Урок')
                    ->relationship('lesson', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('is_published')
                    ->label('Опубликован')
                    ->toggle()

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hiddenLabel(),
            ])
/*            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])*/
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
